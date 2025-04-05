/**
 * Products List Module
 * 
 * This module handles the products listing page:
 * - Fetching products with pagination
 * - Filtering and sorting products
 * - Displaying products in a grid
 * - Integration with cart functionality
 * 
 * API Endpoints Used:
 * - GET /api/products - List all products with pagination
 * - GET /api/products/search - Search products
 * - GET /api/products/category - Filter products by category
 * - GET /api/categories - Get all categories for filtering
 * - POST /api/cart/item - Add to cart
 */

import API from '../utils/api.js';
import UI from '../utils/ui.js';
import Filters from '../utils/filters.js';
import Pagination from '../utils/pagination.js';
import ProductsModule from './products.js';

const ProductsListModule = {
    /**
     * Fetch categories for filtering
     * @returns {Promise<Array>} - Array of categories
     */
    getCategories: async () => {
        try {
            const response = await API.get('/categories');
            
            if (response.success) {
                return response.data;
            }
            
            return [];
        } catch (error) {
            console.error('Error fetching categories:', error);
            return [];
        }
    },
    
    /**
     * Initialize products list page
     * @param {Object} options - Configuration options
     */
    init: async (options = {}) => {
        const {
            filtersContainerId = 'filters-container',
            productsContainerId = 'products-grid',
            paginationContainerId = 'pagination',
            productsPerPage = 12
        } = options;
        
        const filtersContainer = document.getElementById(filtersContainerId);
        const productsContainer = document.getElementById(productsContainerId);
        const paginationContainer = document.getElementById(paginationContainerId);
        
        if (!filtersContainer || !productsContainer || !paginationContainer) {
            console.error('Required containers not found');
            return;
        }
        
        // Show loading state
        productsContainer.innerHTML = `
            <div class="text-center py-8">
                <div class="loading-spinner">
                    <div class="spinner-container">
                        <div class="spinner"></div>
                    </div>
                </div>
                <p class="mt-4 text-text-light">Loading products...</p>
            </div>
        `;
        
        // Fetch categories for filters
        const categories = await ProductsListModule.getCategories();
        
        // Initialize current page state
        let currentPage = 1;
        
        // Function to load products based on filters and page
        const loadProducts = async (page = 1) => {
            // Show loading state
            UI.showLoading(productsContainer);
            
            // Get filter state
            const filterState = Filters.getState();
            
            // Prepare options for API request
            const options = {
                page,
                limit: productsPerPage,
                sort: filterState.sort
            };
            
            // Add category filter if selected
            if (filterState.category) {
                options.category = filterState.category;
            }
            
            // Add search term if provided
            if (filterState.search) {
                options.search = filterState.search;
            }
            
            // Add price range if provided
            if (filterState.priceRange.min !== null) {
                options.min_price = filterState.priceRange.min;
            }
            
            if (filterState.priceRange.max !== null) {
                options.max_price = filterState.priceRange.max;
            }
            
            // Fetch products
            const result = await ProductsModule.getProducts(options);
            
            console.log(result);

            // Hide loading state
            UI.hideLoading(productsContainer);
            
            // Render products
            ProductsModule.renderProductsGrid(result.products, productsContainer);
            
            // Update pagination
            Pagination.update(paginationContainer, {
                currentPage: page,
                totalPages: result.pagination.pages,
                totalItems: result.pagination.total,
                itemsPerPage: result.pagination.limit
            });
            
            // Update current page
            currentPage = page;
            
            // Scroll to top of products if not on first page
            if (page > 1) {
                productsContainer.scrollIntoView({ behavior: 'smooth' });
            }
        };
        
        // Initialize filters
        Filters.init(filtersContainer, {
            showCategories: true,
            showSort: true,
            showPriceRange: true,
            showSearch: true,
            categories
        }, async (filterState) => {
            // When filters change, reset to page 1
            await loadProducts(1);
            
            // Update URL with filter state
            const queryString = Filters.buildQueryString();
            const newUrl = `${window.location.pathname}${queryString ? `?${queryString}` : ''}`;
            window.history.pushState({}, '', newUrl);
        });
        
        // Initialize pagination
        Pagination.init(paginationContainer, {
            currentPage: 1,
            totalPages: 1,
            itemsPerPage: productsPerPage
        }, async (paginationState) => {
            await loadProducts(paginationState.currentPage);
            
            // Update URL with page parameter
            const filterState = Filters.getState();
            const queryParams = new URLSearchParams(window.location.search);
            queryParams.set('page', paginationState.currentPage);
            const newUrl = `${window.location.pathname}?${queryParams.toString()}`;
            window.history.pushState({}, '', newUrl);
        });
        
        // Check URL for initial filters and page
        const urlParams = new URLSearchParams(window.location.search);
        
        // Initialize filter state from URL
        const initialFilterState = Filters.parseQueryString(window.location.search);
        
        // Set initial page from URL or default to 1
        currentPage = urlParams.has('page') ? parseInt(urlParams.get('page')) : 1;
        
        // Update filter UI to match URL params
        Filters.state = initialFilterState;
        Filters.updateUI(filtersContainer);
        
        // Load initial products
        await loadProducts(currentPage);
        
        // Add event listener for browser back/forward buttons
        window.addEventListener('popstate', async () => {
            // Parse new URL params
            const newFilterState = Filters.parseQueryString(window.location.search);
            const newUrlParams = new URLSearchParams(window.location.search);
            const newPage = newUrlParams.has('page') ? parseInt(newUrlParams.get('page')) : 1;
            
            // Update filter state and UI
            Filters.state = newFilterState;
            Filters.updateUI(filtersContainer);
            
            // Load products with new state
            await loadProducts(newPage);
        });
    }
};

export default ProductsListModule; 