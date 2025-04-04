/**
 * Service Detail Module
 * 
 * This module handles functionality for the service detail page:
 * - Fetching service details from the API
 * - Displaying service information
 * - Adding services to cart
 * - Integration with reviews
 * 
 * API Endpoints Used:
 * - GET /api/services/detail - Get service details
 * - POST /api/cart/item - Add to cart
 */

import API from '../utils/api.js';
import UI from '../utils/ui.js';
import ReviewsModule from './reviews.js';

const ServiceDetailModule = {
    /**
     * Fetch service details
     * @param {number} serviceId - Service ID
     * @returns {Promise<Object>} - Service details
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
     * Add service to cart
     * @param {number} serviceId - Service ID
     * @param {number} quantity - Quantity to add (usually 1 for services)
     * @param {string} selectedDate - Optional selected date for appointment
     * @param {string} selectedTime - Optional selected time for appointment
     * @returns {Promise<boolean>} - Success status
     */
    addToCart: async (serviceId, quantity = 1, selectedDate = null, selectedTime = null) => {
        try {
            const cartItem = {
                service_id: serviceId,
                quantity
            };
            
            // Add appointment details if provided
            if (selectedDate && selectedTime) {
                cartItem.appointment = {
                    date: selectedDate,
                    time: selectedTime
                };
            }
            
            const response = await API.post('/cart/item', cartItem);
            
            return response.success;
        } catch (error) {
            console.error('Error adding service to cart:', error);
            return false;
        }
    },
    
    /**
     * Initialize service detail page
     * @param {string} containerId - ID of container element
     * @param {number} serviceId - Service ID
     * @param {boolean} isAuthenticated - Whether user is authenticated
     */
    init: async (containerId, serviceId, isAuthenticated = false) => {
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
                <p class="mt-4 text-text-light">Loading service details...</p>
            </div>
        `;
        
        // Fetch service details
        const service = await ServiceDetailModule.getServiceDetails(serviceId);
        
        if (!service) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-4xl text-primary mb-4"></i>
                    <p class="text-text-light mb-4">Service not found or unavailable.</p>
                    <a href="/sundarta/services" class="btn btn-primary">Browse Services</a>
                </div>
            `;
            return;
        }
        
        // Parse service images
        const images = service.images ? JSON.parse(service.images) : ['https://via.placeholder.com/800x500?text=Service'];
        
        // Render service details
        container.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Service Gallery -->
                <div class="service-gallery-wrapper">
                    ${images.length > 1 ? `
                        <div class="product-gallery">
                            <div class="product-gallery-slides">
                                ${images.map(image => `
                                    <img src="${image}" alt="${service.name}" class="product-gallery-image">
                                `).join('')}
                            </div>
                            <div class="product-gallery-prev">
                                <i class="fas fa-chevron-left"></i>
                            </div>
                            <div class="product-gallery-next">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                            <div class="product-gallery-nav"></div>
                        </div>
                    ` : `
                        <img src="${images[0]}" alt="${service.name}" class="w-full h-auto rounded-md shadow-lg">
                    `}
                    
                    <!-- Additional Images Thumbnails for larger screens -->
                    ${images.length > 1 ? `
                        <div class="mt-4 hidden md:grid grid-cols-4 gap-2">
                            ${images.map((image, index) => `
                                <div class="thumbnail-container" data-index="${index}">
                                    <img src="${image}" alt="${service.name}" class="w-full h-24 object-cover rounded-md cursor-pointer hover:opacity-80 transition-opacity ${index === 0 ? 'border-2 border-primary' : ''}">
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
                
                <!-- Service Information -->
                <div class="service-info">
                    <h1 class="font-heading text-3xl mb-2">${service.name}</h1>
                    
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-primary">
                            ${ReviewsModule.renderStarRating(service.rating || 0)}
                        </div>
                        <span class="text-sm text-text-light">(${service.reviews ? service.reviews.length : 0} reviews)</span>
                    </div>
                    
                    <div class="flex items-center gap-4 mb-6">
                        <div class="text-xl font-semibold">₹${parseFloat(service.price).toFixed(2)}</div>
                        <div class="text-sm text-text-light flex items-center">
                            <i class="far fa-clock mr-1"></i> ${service.duration || '60 mins'}
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <p>${service.description}</p>
                    </div>
                    
                    ${service.benefits ? `
                        <div class="mb-6">
                            <h3 class="font-semibold mb-2">Benefits:</h3>
                            <ul class="list-disc pl-6 space-y-1">
                                ${service.benefits.split('|').map(benefit => `
                                    <li>${benefit.trim()}</li>
                                `).join('')}
                            </ul>
                        </div>
                    ` : ''}
                    
                    <!-- Service Purchase Section -->
                    <div class="mt-6">
                        <h3 class="font-heading text-xl mb-4">Book This Service</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="input-group md:col-span-2">
                                <label for="appointment-date" class="input-label">Preferred Date (Optional)</label>
                                <input type="date" id="appointment-date" class="input-text" min="${new Date().toISOString().split('T')[0]}">
                            </div>
                            
                            <div class="input-group md:col-span-2">
                                <label for="appointment-time" class="input-label">Preferred Time (Optional)</label>
                                <select id="appointment-time" class="input-text">
                                    <option value="">Select a time slot</option>
                                    <option value="09:00">09:00 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="12:00">12:00 PM</option>
                                    <option value="13:00">01:00 PM</option>
                                    <option value="14:00">02:00 PM</option>
                                    <option value="15:00">03:00 PM</option>
                                    <option value="16:00">04:00 PM</option>
                                    <option value="17:00">05:00 PM</option>
                                </select>
                            </div>
                            
                            <div class="input-group">
                                <label for="quantity" class="input-label">Quantity</label>
                                <div class="flex items-center">
                                    <button class="quantity-btn decrement-btn px-3 py-2 bg-sand-light rounded-l-md">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="quantity" class="text-center w-16 py-2 border-y border-sand" value="1" min="1" max="${service.max_quantity || 10}">
                                    <button class="quantity-btn increment-btn px-3 py-2 bg-sand-light rounded-r-md">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-4 mt-6">
                            <button id="add-to-cart-btn" class="btn btn-primary flex-1">
                                <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                            </button>
                            <button id="buy-now-btn" class="btn btn-secondary flex-1">
                                Buy Now
                            </button>
                        </div>
                        
                        <p class="text-sm text-text-light mt-4">
                            <i class="fas fa-info-circle mr-1"></i> 
                            You can select your preferred date and time for the service. Our team will contact you to confirm your appointment.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Service Tabs -->
            <div class="mt-12">
                <div class="border-b mb-6">
                    <div class="flex overflow-x-auto">
                        <button class="tab-btn active px-4 py-2 whitespace-nowrap" data-tab="description">
                            Description
                        </button>
                        <button class="tab-btn px-4 py-2 whitespace-nowrap" data-tab="process">
                            Process
                        </button>
                        <button class="tab-btn px-4 py-2 whitespace-nowrap" data-tab="faqs">
                            FAQs
                        </button>
                    </div>
                </div>
                
                <div class="tab-content active" id="tab-description">
                    <div class="prose max-w-none">
                        ${service.full_description || service.description}
                    </div>
                </div>
                
                <div class="tab-content hidden" id="tab-process">
                    ${service.process ? `
                        <div class="space-y-6">
                            ${service.process.split('|').map((step, index) => `
                                <div class="flex">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-semibold mr-4">
                                        ${index + 1}
                                    </div>
                                    <div>
                                        <p>${step.trim()}</p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    ` : `
                        <div class="text-center py-8 text-text-light">
                            No process information available.
                        </div>
                    `}
                </div>
                
                <div class="tab-content hidden" id="tab-faqs">
                    ${service.faqs ? `
                        <div class="accordion">
                            ${service.faqs.split('|').map(faq => {
                                const [question, answer] = faq.split('?');
                                return `
                                    <div class="accordion-header">
                                        ${question.trim()}?
                                    </div>
                                    <div class="accordion-content">
                                        <p>${answer.trim()}</p>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    ` : `
                        <div class="text-center py-8 text-text-light">
                            No FAQs available.
                        </div>
                    `}
                </div>
            </div>
        `;
        
        // Add event listeners
        
        // Tab navigation
        const tabButtons = container.querySelectorAll('.tab-btn');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                // Remove active class from all buttons
                tabButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                e.target.classList.add('active');
                
                // Hide all tab content
                const tabContents = container.querySelectorAll('.tab-content');
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                    content.classList.remove('active');
                });
                
                // Show active tab content
                const tabName = e.target.getAttribute('data-tab');
                const activeTab = document.getElementById(`tab-${tabName}`);
                activeTab.classList.remove('hidden');
                activeTab.classList.add('active');
            });
        });
        
        // Initialize accordion if present
        const accordionHeaders = container.querySelectorAll('.accordion-header');
        
        if (accordionHeaders.length) {
            accordionHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    // Toggle active class on the header
                    header.classList.toggle('active');
                    
                    // Get the content element (next sibling)
                    const content = header.nextElementSibling;
                    
                    // Toggle active class on the content
                    content.classList.toggle('active');
                });
            });
        }
        
        // Initialize image gallery
        if (images.length > 1) {
            ServiceDetailModule.initImageGallery(container, images, service.name);
        }
        
        // Quantity buttons
        const quantityInput = container.querySelector('#quantity');
        const decrementBtn = container.querySelector('.decrement-btn');
        const incrementBtn = container.querySelector('.increment-btn');
        
        if (quantityInput && decrementBtn && incrementBtn) {
            decrementBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
            
            incrementBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value);
                const max = parseInt(quantityInput.getAttribute('max'));
                if (currentValue < max) {
                    quantityInput.value = currentValue + 1;
                }
            });
            
            quantityInput.addEventListener('change', () => {
                let value = parseInt(quantityInput.value);
                const max = parseInt(quantityInput.getAttribute('max'));
                
                if (isNaN(value) || value < 1) {
                    value = 1;
                } else if (value > max) {
                    value = max;
                }
                
                quantityInput.value = value;
            });
        }
        
        // Add to cart button
        const addToCartBtn = container.querySelector('#add-to-cart-btn');
        
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', async () => {
                const quantity = parseInt(quantityInput.value);
                const selectedDate = document.getElementById('appointment-date').value;
                const selectedTime = document.getElementById('appointment-time').value;
                
                // Show loading state
                const originalBtnText = addToCartBtn.innerHTML;
                addToCartBtn.disabled = true;
                addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Adding...';
                
                // Add to cart
                const success = await ServiceDetailModule.addToCart(
                    serviceId, 
                    quantity,
                    selectedDate,
                    selectedTime
                );
                
                // Reset button state
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = originalBtnText;
                
                if (success) {
                    UI.showSuccess('Service added to cart!');
                    
                    // Update cart count in header if it exists
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        const currentCount = parseInt(cartCountElement.textContent);
                        cartCountElement.textContent = currentCount + quantity;
                    }
                } else {
                    UI.showError('Failed to add service to cart. Please try again.');
                }
            });
        }
        
        // Buy now button
        const buyNowBtn = container.querySelector('#buy-now-btn');
        
        if (buyNowBtn) {
            buyNowBtn.addEventListener('click', async () => {
                const quantity = parseInt(quantityInput.value);
                const selectedDate = document.getElementById('appointment-date').value;
                const selectedTime = document.getElementById('appointment-time').value;
                
                // Add to cart first
                const success = await ServiceDetailModule.addToCart(
                    serviceId, 
                    quantity,
                    selectedDate,
                    selectedTime
                );
                
                if (success) {
                    // Redirect to checkout
                    window.location.href = '/sundarta/checkout';
                } else {
                    UI.showError('Failed to proceed to checkout. Please try again.');
                }
            });
        }
        
        // Initialize reviews section
        ReviewsModule.initProductReviews('reviews-section', serviceId, isAuthenticated);
    },
    
    /**
     * Initialize service image gallery
     * @param {HTMLElement} container - Container element
     * @param {Array} images - Array of image URLs
     * @param {string} serviceName - Service name for alt text
     */
    initImageGallery: (container, images, serviceName) => {
        const galleryWrapper = container.querySelector('.service-gallery-wrapper');
        const gallery = container.querySelector('.product-gallery');
        const slidesContainer = container.querySelector('.product-gallery-slides');
        const navContainer = container.querySelector('.product-gallery-nav');
        const prevBtn = container.querySelector('.product-gallery-prev');
        const nextBtn = container.querySelector('.product-gallery-next');
        const thumbnails = container.querySelectorAll('.thumbnail-container');
        
        if (!gallery || !slidesContainer || !images.length) return;
        
        let currentIndex = 0;
        const totalImages = images.length;
        
        // Create navigation dots
        if (navContainer) {
            // Clear any existing dots first
            navContainer.innerHTML = '';
            
            images.forEach((_, index) => {
                const dot = document.createElement('span');
                dot.className = `product-gallery-dot ${index === 0 ? 'active' : ''}`;
                dot.setAttribute('data-index', index);
                navContainer.appendChild(dot);
                
                dot.addEventListener('click', () => {
                    scrollToImage(index);
                });
            });
        }
        
        // Navigation buttons
        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                scrollToImage(currentIndex - 1);
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                scrollToImage(currentIndex + 1);
            });
        }
        
        // Thumbnail clicks
        if (thumbnails.length) {
            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', () => {
                    const index = parseInt(thumb.getAttribute('data-index'));
                    scrollToImage(index);
                });
            });
        }
        
        // Scroll to specific image
        function scrollToImage(index) {
            // Handle wrapping
            if (index < 0) {
                index = totalImages - 1;
            } else if (index >= totalImages) {
                index = 0;
            }
            
            currentIndex = index;
            
            // Update image container
            if (slidesContainer) {
                slidesContainer.style.transform = `translateX(${-currentIndex * 100}%)`;
            }
            
            // Update dots
            if (navContainer) {
                const dots = navContainer.querySelectorAll('.product-gallery-dot');
                dots.forEach((dot, i) => {
                    if (i === currentIndex) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            }
            
            // Update thumbnails
            if (thumbnails.length) {
                thumbnails.forEach((thumb, i) => {
                    const img = thumb.querySelector('img');
                    if (i === currentIndex) {
                        img.classList.add('border-2', 'border-primary');
                    } else {
                        img.classList.remove('border-2', 'border-primary');
                    }
                });
            }
        }
    }
};

// Add some additional CSS for product gallery
const style = document.createElement('style');
style.textContent = `
    .product-gallery {
        position: relative;
        overflow: hidden;
        border-radius: var(--radius-md);
        height: 400px;
    }
    
    .product-gallery-slides {
        display: flex;
        transition: transform 0.5s ease;
        height: 100%;
        width: 100%;
    }
    
    .product-gallery-image {
        min-width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-gallery-prev,
    .product-gallery-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        background-color: var(--surface);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .product-gallery-prev:hover,
    .product-gallery-next:hover {
        background-color: var(--primary);
        color: white;
    }
    
    .product-gallery-prev {
        left: 10px;
    }
    
    .product-gallery-next {
        right: 10px;
    }
    
    .product-gallery-nav {
        position: absolute;
        bottom: 15px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 8px;
        z-index: 10;
    }
    
    .product-gallery-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: var(--surface);
        opacity: 0.6;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .product-gallery-dot.active {
        opacity: 1;
        background-color: var(--primary);
        transform: scale(1.2);
    }
    
    @media (max-width: 768px) {
        .product-gallery {
            height: 300px;
        }
    }
`;
document.head.appendChild(style);

export default ServiceDetailModule; 