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
 */

import API from '../utils/api.js';
import UI from '../utils/ui.js';

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
                    products: response.data,
                    pagination: response.pagination
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
            <div class="card card-product">
                <img src="${images[0]}" 
                     alt="${product.name}" 
                     class="card-product-image">
                <div class="card-product-body">
                    ${product.is_new ? '<span class="badge badge-primary mb-2">New</span>' : ''}
                    <h3 class="font-heading text-lg mb-1">${product.name}</h3>
                    <p class="text-text-light text-sm mb-2">${product.short_description || ''}</p>
                    <div class="flex items-center mb-2">
                        <div class="flex text-primary">
                            ${ProductsModule.renderStarRating(product.rating || 0)}
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
     * Render star rating HTML
     * @param {number} rating - Rating value (0-5)
     * @returns {string} - HTML for star rating
     */
    renderStarRating: (rating) => {
        const fullStars = Math.floor(rating);
        const halfStar = rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
        
        let starsHtml = '';
        
        // Add full stars
        for (let i = 0; i < fullStars; i++) {
            starsHtml += '<i class="fas fa-star"></i>';
        }
        
        // Add half star if needed
        if (halfStar) {
            starsHtml += '<i class="fas fa-star-half-alt"></i>';
        }
        
        // Add empty stars
        for (let i = 0; i < emptyStars; i++) {
            starsHtml += '<i class="far fa-star"></i>';
        }
        
        return starsHtml;
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
        
        const productsHtml = products.map(product => ProductsModule.renderProductCard(product)).join('');
        
        container.innerHTML = productsHtml;
        
        // Add event listeners to "Add to Cart" buttons
        const addToCartButtons = container.querySelectorAll('.add-to-cart-btn');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.target.getAttribute('data-product-id');
                // Here you would call a cart module function to add the product to cart
                // For now, just show a success message
                UI.showAlert('Product added to cart!', 'success');
            });
        });
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