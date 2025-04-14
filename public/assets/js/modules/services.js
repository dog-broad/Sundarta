/**
 * Services Module
 * 
 * This module handles service-related functionality:
 * - Fetching services from the API
 * - Displaying services in a grid
 * - Filtering and sorting services
 * - Service search
 * - Adding services to cart
 */

import API from '../utils/api.js';
import UI from '../utils/ui.js';
import ReviewsModule from './reviews.js';
import Cart from './cart.js';
import URL from '../utils/url.js';

const ServicesModule = {
    /**
     * Fetch featured services
     * @param {number} limit - Number of services to fetch
     * @returns {Promise<Array>} - Array of services
     */
    getFeaturedServices: async (limit = 6) => {
        try {
            const response = await API.get('/services/featured', { limit });
            
            if (response.success) {
                return response.data;
            }
            
            return [];
        } catch (error) {
            console.error('Error fetching featured services:', error);
            return [];
        }
    },
    
    /**
     * Fetch services with pagination
     * @param {Object} options - Pagination and filter options
     * @returns {Promise<Object>} - Services and pagination info
     */
    getServices: async (options = {}) => {
        const {
            page = 1,
            limit = 12,
            category = null,
            sort = 'newest',
            search = null
        } = options;
        
        try {
            let endpoint = '/services';
            const params = { page, limit };
            
            // Add category filter if provided
            if (category) {
                params.category = category;
            }
            
            // Add sort parameter
            params.sort = sort;
            
            // Use search endpoint if search term is provided
            if (search) {
                endpoint = '/services/search';
                params.query = search;
            }
            
            const response = await API.get(endpoint, params);
            
            if (response.success) {
                return {
                    services: response.data.services,
                    pagination: response.data.pagination
                };
            }
            
            return {
                services: [],
                pagination: {
                    total: 0,
                    page: 1,
                    limit: limit,
                    pages: 0
                }
            };
        } catch (error) {
            console.error('Error fetching services:', error);
            return {
                services: [],
                pagination: {
                    total: 0,
                    page: 1,
                    limit: limit,
                    pages: 0
                }
            };
        }
    },

    /**
     * Get service details by ID
     * @param {number} serviceId - Service ID
     * @returns {Promise<Object|null>} - Service details or null if not found
     */
    getServiceDetails: async (serviceId) => {
        try {
            const response = await API.get('/services/detail', { id: serviceId });
            
            if (response.success) {
                return response.data;
            }
            
            return null;
        } catch (error) {
            console.error('Error fetching service details:', error);
            return null;
        }
    },
    
    /**
     * Render service card HTML
     * @param {Object} service - Service data
     * @returns {string} - HTML for service card
     */
    renderServiceCard: (service) => {
        // Parse images from JSON string or use default placeholder
        let images;
        try {
            images = service.images ? JSON.parse(service.images) : ['https://via.placeholder.com/300x200?text=Service'];
        } catch (error) {
            console.error('Error parsing service images:', error);
            images = ['https://via.placeholder.com/300x200?text=Service'];
        }
        
        return `
            <div class="card card-service service-card" data-service-id="${service.id}">
                <img src="${images[0]}" 
                     alt="${service.name}" 
                     class="card-service-image">
                <div class="card-service-body">
                    ${service.is_new ? '<span class="badge badge-primary mb-2">New</span>' : ''}
                    <h3 class="font-heading text-lg mb-1">${service.name}</h3>
                    <p class="text-text-light text-sm mb-2">${service.short_description || ''}</p>
                    
                    ${service.rating ? `
                    <div class="flex items-center mb-2">
                        <div class="flex text-primary">
                            ${ReviewsModule.renderStarRating(service.rating || 0)}
                        </div>
                        <span class="text-xs ml-1">(${parseFloat(service.rating).toFixed(1) || 0})</span>
                    </div>
                    ` : ''}
                    
                    <div class="mt-2">
                        <div class="flex items-center text-sm text-text-light">
                            <i class="far fa-clock mr-1"></i> ${service.duration || '60 mins'}
                        </div>
                    </div>
                </div>
                <div class="card-service-footer flex justify-between items-center">
                    <span class="font-semibold">₹${parseFloat(service.price).toFixed(2)}</span>
                    <button class="btn btn-primary py-1 px-3 text-sm add-to-cart-btn" data-service-id="${service.id}">
                        Add to Cart
                    </button>
                </div>
            </div>
        `;
    },
    
    /**
     * Render services grid
     * @param {Array} services - Array of service objects
     * @param {HTMLElement} container - Container element
     */
    renderServicesGrid: (services, container) => {
        if (!container) return;
        
        if (services.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-spa text-4xl text-text-light mb-4"></i>
                    <p class="text-text-light">No services found.</p>
                </div>
            `;
            return;
        }
        
        // Create a grid container
        let gridHtml = `<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">`;
        
        // Add each service card to the grid
        services.forEach(service => {
            gridHtml += `<div class="service-grid-item">${ServicesModule.renderServiceCard(service)}</div>`;
        });
        
        // Close the grid container
        gridHtml += `</div>`;
        
        // if this is being done in home page, use services else use grid
        if (container.id === 'featured-services') {
            const servicesHtml = services.map(service => ServicesModule.renderServiceCard(service)).join('');
            container.innerHTML = servicesHtml;
        } else {
            container.innerHTML = gridHtml;
        }
        
        // Add event listeners to "Add to Cart" buttons
        const addToCartButtons = container.querySelectorAll('.add-to-cart-btn');
        addToCartEventListeners(addToCartButtons);
        
        // Add click event to service cards for navigation to detail page
        const serviceCards = container.querySelectorAll('.service-card');
        serviceCards.forEach(card => {
            card.style.cursor = 'pointer'; // Show pointer cursor on hover
            card.addEventListener('click', () => {
                const serviceId = card.getAttribute('data-service-id');
                URL.redirect(`service-detail?id=${serviceId}`);
            });
        });
        
        // Add styling for service grid
        const style = document.createElement('style');
        style.textContent = `
            .service-grid-item .card-service {
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            
            .service-grid-item .card-service-body {
                flex-grow: 1;
            }
            
            .service-grid-item .card-service-image {
                height: 200px;
                object-fit: cover;
                width: 100%;
                border-top-left-radius: 0.375rem;
                border-top-right-radius: 0.375rem;
            }
            
            .card-service {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .card-service:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }
        `;
        document.head.appendChild(style);
    },
    
    /**
     * Initialize featured services section
     * @param {string} containerId - ID of container element
     * @param {number} limit - Number of services to display
     */
    initFeaturedServices: async (containerId = 'featured-services', limit = 3) => {
        const container = document.getElementById(containerId);
        
        if (!container) return;
        
        // Show loading state
        container.innerHTML = `
            <div class="text-center py-8">
                <div class="loading-spinner">
                    <div class="spinner-container">
                        <div class="spinner"></div>
                    </div>
                </div>
                <p class="mt-4 text-text-light">Loading services...</p>
            </div>
        `;
        
        // Fetch featured services
        const services = await ServicesModule.getFeaturedServices(limit);
        
        // Render services
        ServicesModule.renderServicesGrid(services, container);
    }
};

/**
 * Add event listeners to "Add to Cart" buttons
 * @param {NodeList} addToCartButtons - List of Add to Cart buttons
 */
const addToCartEventListeners = (addToCartButtons) => {
    addToCartButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.stopPropagation(); // Prevent triggering the card click
            
            const serviceId = e.target.getAttribute('data-service-id');
            const originalBtnText = e.target.innerHTML;
            
            // Show loading state
            e.target.disabled = true;
            e.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Add to cart using the Cart module
            const success = await Cart.addItem('service', serviceId, 1);
            
            // Reset button state
            e.target.disabled = false;
            e.target.innerHTML = originalBtnText;
            
            if (success) {
                UI.showSuccess('Service added to cart!');
            } else {
                UI.showError('Failed to add service to cart');
            }
        });
    });
};

export default ServicesModule; 