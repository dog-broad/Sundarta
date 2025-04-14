/**
 * Product Detail Module
 * 
 * This module handles functionality for the product detail page:
 * - Fetching product details from the API
 * - Displaying product information including gallery
 * - Handling quantity selection and add to cart
 * - Integration with reviews
 * 
 * API Endpoints Used:
 * - GET /api/products/detail - Get product details
 * - POST /api/cart/item - Add to cart via Cart module
 */

import API from '../utils/api.js';
import UI from '../utils/ui.js';
import ReviewsModule from './reviews.js';
import Cart from './cart.js';

const ProductDetailModule = {
    /**
     * Fetch product details
     * @param {number} productId - Product ID
     * @returns {Promise<Object>} - Product details
     */
    getProductDetails: async (productId) => {
        try {
            const response = await API.get('/products/detail', { id: productId });
            
            if (response.success) {
                return response.data;
            }
            
            return null;
        } catch (error) {
            console.error('Error fetching product details:', error);
            return null;
        }
    },
    
    /**
     * Initialize product detail page
     * @param {string} containerId - ID of container element
     * @param {number} productId - Product ID
     * @param {boolean} isAuthenticated - Whether user is authenticated
     */
    init: async (containerId, productId, isAuthenticated = false) => {
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
                <p class="mt-4 text-text-light">Loading product details...</p>
            </div>
        `;
        
        // Fetch product details
        const product = await ProductDetailModule.getProductDetails(productId);
        
        if (!product) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-4xl text-primary mb-4"></i>
                    <p class="text-text-light mb-4">Product not found or unavailable.</p>
                    <a href="/products" class="btn btn-primary">Browse Products</a>
                </div>
            `;
            return;
        }
        
        // Parse product images
        const images = JSON.parse(product.images);
        
        // Render product details
        container.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Product Gallery -->
                <div class="product-gallery-wrapper">
                    ${images.length > 1 ? `
                        <div class="product-gallery">
                            <div class="product-gallery-slides">
                                ${images.map(image => `
                                    <img src="${image}" alt="${product.name}" class="product-gallery-image">
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
                        <img src="${images[0]}" alt="${product.name}" class="w-full h-auto rounded-md">
                    `}
                    
                    <!-- Additional Images Thumbnails for larger screens -->
                    ${images.length > 1 ? `
                        <div class="mt-4 hidden md:grid grid-cols-4 gap-2">
                            ${images.map((image, index) => `
                                <div class="thumbnail-container" data-index="${index}">
                                    <img src="${image}" alt="${product.name}" class="w-full h-24 object-cover rounded-md cursor-pointer hover:opacity-80 transition-opacity ${index === 0 ? 'border-2 border-primary' : ''}">
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
                
                <!-- Product Information -->
                <div class="product-info">
                    <div class="flex items-center justify-between">
                        <h1 class="font-heading text-3xl mb-2">${product.name}</h1>
                        <button class="wishlist-toggle" data-product-id="${product.id}">
                            <i class="far fa-heart text-2xl hover:text-primary transition-colors"></i>
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-primary">
                            ${ReviewsModule.renderStarRating(product.rating || 0)}
                        </div>
                        <span class="text-sm text-text-light">(${product.reviews.length || 0} reviews)</span>
                    </div>
                    
                    <div class="text-xl font-semibold mb-6">₹${parseFloat(product.price).toFixed(2)}</div>
                    
                    ${product.stock > 0 ? `
                        <div class="text-green-600 mb-4 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i> In Stock
                        </div>
                    ` : `
                        <div class="text-red-600 mb-4 flex items-center">
                            <i class="fas fa-times-circle mr-2"></i> Out of Stock
                        </div>
                    `}
                    
                    <div class="mb-6">
                        <p>${product.description}</p>
                    </div>
                    
                    ${product.features ? `
                        <div class="mb-6">
                            <h3 class="font-semibold mb-2">Key Features:</h3>
                            <ul class="list-disc pl-6 space-y-1">
                                ${product.features.split('|').map(feature => `
                                    <li>${feature.trim()}</li>
                                `).join('')}
                            </ul>
                        </div>
                    ` : ''}
                    
                    ${product.stock > 0 ? `
                        <div class="mb-6">
                            <label for="quantity" class="input-label mb-2">Quantity</label>
                            <div class="flex items-center">
                                <button class="quantity-btn decrement-btn px-3 py-2 bg-sand-light rounded-l-md">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" class="text-center w-16 py-2 border-y border-sand" value="1" min="1" max="${product.stock_quantity || 10}">
                                <button class="quantity-btn increment-btn px-3 py-2 bg-sand-light rounded-r-md">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex gap-4">
                            <button id="add-to-cart-btn" class="btn btn-primary flex-1">
                                <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                            </button>
                            <button id="buy-now-btn" class="btn btn-secondary flex-1">
                                Buy Now
                            </button>
                        </div>
                    ` : `
                        <button class="btn btn-outline w-full" disabled>
                            Out of Stock
                        </button>
                    `}
                    
                    <!-- Product Metadata -->
                    <div class="mt-8 pt-4 border-t">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            ${product.category_name ? `
                                <div>
                                    <span class="text-text-light">Category:</span>
                                    <a href="/products?category=${product.category_id}" class="text-primary hover-link ml-1">${product.category_name}</a>
                                </div>
                            ` : ''}
                            
                            ${product.sku ? `
                                <div>
                                    <span class="text-text-light">SKU:</span>
                                    <span class="ml-1">${product.sku}</span>
                                </div>
                            ` : ''}
                            
                            ${product.brand ? `
                                <div>
                                    <span class="text-text-light">Brand:</span>
                                    <span class="ml-1">${product.brand}</span>
                                </div>
                            ` : ''}
                            
                            <div>
                                <span class="text-text-light">Share:</span>
                                <div class="inline-flex gap-2 ml-1">
                                    <a href="#" class="text-primary hover:text-primary-light">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="text-primary hover:text-primary-light">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="text-primary hover:text-primary-light">
                                        <i class="fab fa-pinterest"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Tabs -->
            <div class="mt-12">
                <div class="border-b mb-6">
                    <div class="flex overflow-x-auto">
                        <button class="tab-btn active px-4 py-2 whitespace-nowrap" data-tab="description">
                            Description
                        </button>
                        <button class="tab-btn px-4 py-2 whitespace-nowrap" data-tab="specifications">
                            Specifications
                        </button>
                        <button class="tab-btn px-4 py-2 whitespace-nowrap" data-tab="instructions">
                            How to Use
                        </button>
                    </div>
                </div>
                
                <div class="tab-content active" id="tab-description">
                    <div class="prose max-w-none">
                        ${product.full_description || product.description}
                    </div>
                </div>
                
                <div class="tab-content hidden" id="tab-specifications">
                    <div class="specifications-list">
                        ${product.specifications ? (() => {
                            try {
                                const specsObj = typeof product.specifications === 'string' ? 
                                    JSON.parse(product.specifications) : product.specifications;
                                
                                return Object.entries(specsObj).map(([key, value]) => `
                                    <div class="specifications-item">
                                        <span class="specifications-key">${key}:</span>
                                        <span class="specifications-value">${value}</span>
                                    </div>
                                `).join('');
                            } catch (error) {
                                console.error('Error parsing specifications:', error);
                                return `<div class="text-center py-8 text-text-light">
                                    Error displaying specifications.
                                </div>`;
                            }
                        })() : `
                        <div class="text-center py-8 text-text-light">
                            No specifications available.
                        </div>
                    `}
                    </div>
                </div>
                
                <div class="tab-content hidden" id="tab-instructions">
                    ${product.instructions ? `
                        <div class="prose max-w-none">
                            ${product.instructions}
                        </div>
                    ` : `
                        <div class="text-center py-8 text-text-light">
                            No usage instructions available.
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
                
                // Show loading state
                const originalBtnText = addToCartBtn.innerHTML;
                addToCartBtn.disabled = true;
                addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Adding...';
                
                // Add to cart using Cart module
                const success = await Cart.addItem('product', productId, quantity);
                
                // Reset button state
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = originalBtnText;
                
                if (success) {
                    UI.showSuccess('Product added to cart!');
                } else {
                    UI.showError('Failed to add product to cart. Please try again.');
                }
            });
        }
        
        // Buy now button
        const buyNowBtn = container.querySelector('#buy-now-btn');
        
        if (buyNowBtn) {
            buyNowBtn.addEventListener('click', async () => {
                const quantity = parseInt(quantityInput.value);
                
                // Add to cart first using Cart module
                const success = await Cart.addItem('product', productId, quantity);
                
                if (success) {
                    // Redirect to checkout
                    window.location.href = '/checkout';
                } else {
                    UI.showError('Failed to proceed to checkout. Please try again.');
                }
            });
        }
        
        // Wishlist toggle
        const wishlistToggle = container.querySelector('.wishlist-toggle');
        
        if (wishlistToggle) {
            wishlistToggle.addEventListener('click', () => {
                const icon = wishlistToggle.querySelector('i');
                
                if (icon.classList.contains('far')) {
                    // Add to wishlist (change to solid heart)
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    icon.classList.add('text-primary');
                    
                    UI.showSuccess('Product added to wishlist!');
                } else {
                    // Remove from wishlist (change to outline heart)
                    icon.classList.remove('fas');
                    icon.classList.remove('text-primary');
                    icon.classList.add('far');
                    
                    UI.showSuccess('Product removed from wishlist!');
                }
            });
        }
        
        // Image gallery
        if (images.length > 1) {
            ProductDetailModule.initImageGallery(container, images, product.name);
        }
        
        // Initialize reviews section
        ReviewsModule.initReviews('reviews-section', productId, 'product', isAuthenticated);
    },
    
    /**
     * Initialize product image gallery
     * @param {HTMLElement} container - Container element
     * @param {Array} images - Array of image URLs
     * @param {string} productName - Product name for alt text
     */
    initImageGallery: (container, images, productName) => {
        const galleryWrapper = container.querySelector('.product-gallery-wrapper');
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

// Add some additional CSS for product detail page
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
        height: auto; /* Allow height to adjust based on aspect ratio */
        object-fit: cover; /* Maintain aspect ratio while covering the area */
        background: rgba(255, 255, 255, 0.5); /* Glass effect for voids */
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
    
    .quantity-btn {
        transition: all 0.2s ease;
    }
    
    .quantity-btn:hover {
        background-color: var(--primary);
        color: white;
    }
    
    @media (max-width: 768px) {
        .product-gallery {
            height: 300px;
        }
    }
`;
document.head.appendChild(style);

// Add CSS for specifications
const specsStyle = document.createElement('style');
specsStyle.textContent = `
    .specifications-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }
    
    .specifications-item {
        padding: 12px 16px;
        border-radius: 6px;
        background-color: var(--sand-light);
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .specifications-key {
        font-weight: 600;
        color: var(--primary);
    }
    
    .specifications-value {
        color: var(--text);
    }
    
    @media (max-width: 640px) {
        .specifications-list {
            grid-template-columns: 1fr;
        }
    }
`;
document.head.appendChild(specsStyle);

export default ProductDetailModule; 