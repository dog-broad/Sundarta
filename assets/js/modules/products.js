/**
 * Products Module
 * 
 * This module handles all product-related functionality including:
 * - Listing products with filtering and pagination
 * - Product detail view
 * - Product reviews
 * - Adding products to cart
 * 
 * API Endpoints Used:
 * - GET /api/products
 * - GET /api/products/detail
 * - GET /api/products/search
 * - GET /api/products/category
 * - GET /api/reviews/product
 * - POST /api/reviews/product
 * - POST /api/cart/item

 * This module handles product-related functionality:
 * - Fetching products from the API
 * - Displaying products in a grid
 * - Filtering and sorting products
 * - Product search
 * - Adding products to cart
 */

import API from '../utils/api.js';
import UI from '../utils/ui.js';
import ReviewsModule from './reviews.js';
import Cart from './cart.js';
import URL from '../utils/url.js';

const ProductsModule = {
    /**
     * Fetch featured products
     * @param {number} limit - Number of products to fetch
     * @returns {Promise<Array>} - Array of products
     */
    getFeaturedProducts: async (limit = 6) => {
        try {
            const response = await API.get('/products/featured', { limit });
            
            if (response.success) {
                return response.data;
            }
            
            return [];
        } catch (error) {
            console.error('Error fetching featured products:', error);
            return [];
        }
    },
    
    /**
     * Fetch products with pagination
     * @param {Object} options - Pagination and filter options
     * @returns {Promise<Object>} - Products and pagination info
     */
    getProducts: async (options = {}) => {
        const {
            page = 1,
            limit = 12,
            category = null,
            sort = 'newest',
            search = null
        } = options;
        
        try {
            let endpoint = '/products';
            const params = { page, limit };
            
            // Add category filter if provided
            if (category) {
                params.category = category;
            }
            
            // Add sort parameter
            params.sort = sort;
            
            // Use search endpoint if search term is provided
            if (search) {
                endpoint = '/products/search';
                params.query = search;
            }
            
            const response = await API.get(endpoint, params);
            
            if (response.success) {
                return {
                    products: response.data.products,
                    pagination: response.data.pagination
                };
            }
            
            return {
                products: [],
                pagination: {
                    total: 0,
                    page: 1,
                    limit: limit,
                    pages: 0
                }
            };
        } catch (error) {
            console.error('Error fetching products:', error);
            return {
                products: [],
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
     * Render product card HTML
     * @param {Object} product - Product data
     * @returns {string} - HTML for product card
     */
    renderProductCard: (product) => {
        const images = JSON.parse(product.images);
        return `
            <div class="card card-product product-card" data-product-id="${product.id}">
                <img src="${images[0]}" 
                     alt="${product.name}" 
                     class="card-product-image">
                <div class="card-product-body">
                    ${product.is_new ? '<span class="badge badge-primary mb-2">New</span>' : ''}
                    <h3 class="font-heading text-lg mb-1">${product.name}</h3>
                    <p class="text-text-light text-sm mb-2">${product.short_description || ''}</p>
                    <div class="flex items-center mb-2">
                        <div class="flex text-primary">
                            ${ReviewsModule.renderStarRating(product.rating || 0)}
                        </div>
                        <span class="text-xs ml-1">(${parseFloat(product.rating).toFixed(1) || 0})</span>
                    </div>
                </div>
                <div class="card-product-footer flex justify-between items-center">
                    <span class="font-semibold">₹${parseFloat(product.price).toFixed(2)}</span>
                    <button class="btn btn-primary py-1 px-3 text-sm add-to-cart-btn" data-product-id="${product.id}">
                        Add to Cart
                    </button>
                </div>
            </div>
        `;
    },
    
    /**
     * Render products grid
     * @param {Array} products - Array of product objects
     * @param {HTMLElement} container - Container element
     */
    renderProductsGrid: (products, container) => {
        if (!container) return;
        
        if (products.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-box-open text-4xl text-text-light mb-4"></i>
                    <p class="text-text-light">No products found.</p>
                </div>
            `;
            return;
        }
        
        // Create a grid container
        let gridHtml = `<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">`;
        
        // Add each product card to the grid
        products.forEach(product => {
            gridHtml += `<div class="product-grid-item">${ProductsModule.renderProductCard(product)}</div>`;
        });
        
        // Close the grid container
        gridHtml += `</div>`;
        
        container.innerHTML = gridHtml;
        
        // Add event listeners to "Add to Cart" buttons
        const addToCartButtons = container.querySelectorAll('.add-to-cart-btn');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', async (e) => {
                e.stopPropagation(); // Prevent triggering the card click
                
                const productId = e.target.getAttribute('data-product-id');
                const originalBtnText = e.target.innerHTML;
                
                // Show loading state
                e.target.disabled = true;
                e.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                // Add to cart
                const success = await Cart.addItem('product', productId, 1);
                
                // Reset button state
                e.target.disabled = false;
                e.target.innerHTML = originalBtnText;
                
                if (success) {
                    UI.showSuccess('Product added to cart!');
                } else {
                    UI.showError('Failed to add product to cart');
                }
            });
        });
        
        // Add click event to product cards for navigation to detail page
        const productCards = container.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.style.cursor = 'pointer'; // Show pointer cursor on hover
            card.addEventListener('click', () => {
                const productId = card.getAttribute('data-product-id');
                URL.redirect(`product-detail?id=${productId}`);
            });
        });
        
        // Add some styling for the grid items
        const style = document.createElement('style');
        style.textContent = `
            .product-grid-item .card-product {
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            
            .product-grid-item .card-product-body {
                flex-grow: 1;
            }
            
            .product-grid-item .card-product-image {
                height: 200px;
                object-fit: cover;
                width: 100%;
                border-top-left-radius: 0.375rem;
                border-top-right-radius: 0.375rem;
            }
            
            .card-product {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .card-product:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }
        `;
        document.head.appendChild(style);
    },
    
    /**
     * Initialize featured products section
     * @param {string} containerId - ID of container element
     * @param {number} limit - Number of products to display
     */
    initFeaturedProducts: async (containerId = 'featured-products', limit = 6) => {
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
                <p class="mt-4 text-text-light">Loading products...</p>
            </div>
        `;
        
        // Fetch featured products
        const products = await ProductsModule.getFeaturedProducts(limit);
        
        // Render products
        ProductsModule.renderProductsGrid(products, container);
    }
};

export default ProductsModule; 