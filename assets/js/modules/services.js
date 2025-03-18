/**
 * Services Module
 * 
 * This module handles service-related functionality:
 * - Fetching services from the API
 * - Displaying services in a grid
 * - Filtering and sorting services
 * - Service search
 */

import API from '../utils/api.js';
import UI from '../utils/ui.js';

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
                    services: response.data,
                    pagination: response.pagination
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
     * Render service card HTML
     * @param {Object} service - Service data
     * @returns {string} - HTML for service card
     */
    renderServiceCard: (service) => {
        return `
            <div class="service-box">
                <div class="text-primary text-3xl mb-4">
                    <i class="${service.icon || 'fas fa-spa'}"></i>
                </div>
                <h3 class="font-heading text-xl mb-2">${service.name}</h3>
                <p class="text-text-light mb-4">${service.short_description || ''}</p>
                <div class="flex justify-between items-center">
                    <span class="font-semibold">₹${parseFloat(service.price).toFixed(2)}</span>
                    <a href="/sundarta/service-detail.php?id=${service.id}" class="hover-link text-primary text-sm">
                        Learn More <i class="fas fa-arrow-right ml-1"></i>
                    </a>
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
        
        const servicesHtml = services.map(service => ServicesModule.renderServiceCard(service)).join('');
        
        container.innerHTML = servicesHtml;
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

export default ServicesModule; 