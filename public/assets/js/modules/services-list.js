/**
 * Services List Module
 * 
 * This module handles the services listing page:
 * - Fetching services with pagination
 * - Filtering and sorting services
 * - Displaying services in a grid
 * 
 * API Endpoints Used:
 * - GET /api/services - List all services with pagination
 * - GET /api/services/search - Search services
 * - GET /api/services/category - Filter services by category
 * - GET /api/categories - Get all categories for filtering
 */

import API from '../utils/api.js';
import UI from '../utils/ui.js';
import Filters from '../utils/filters.js';
import Pagination from '../utils/pagination.js';
import ServicesModule from './services.js';

const ServicesListModule = {
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
     * Initialize services list page
     * @param {Object} options - Configuration options
     */
    init: async (options = {}) => {
        const {
            filtersContainerId = 'filters-container',
            servicesContainerId = 'services-grid',
            paginationContainerId = 'pagination',
            servicesPerPage = 12
        } = options;
        
        const filtersContainer = document.getElementById(filtersContainerId);
        const servicesContainer = document.getElementById(servicesContainerId);
        const paginationContainer = document.getElementById(paginationContainerId);
        
        if (!filtersContainer || !servicesContainer || !paginationContainer) {
            console.error('Required containers not found');
            return;
        }
        
        // Show loading state
        servicesContainer.innerHTML = `
            <div class="text-center py-8">
                <div class="loading-spinner">
                    <div class="spinner-container">
                        <div class="spinner"></div>
                    </div>
                </div>
                <p class="mt-4 text-text-light">Loading services...</p>
            </div>
        `;
        
        // Fetch categories for filters
        const categories = await ServicesListModule.getCategories();
        
        // Initialize current page state
        let currentPage = 1;
        
        // Function to load services based on filters and page
        const loadServices = async (page = 1) => {
            // Show loading state
            UI.showLoading(servicesContainer);
            
            // Get filter state
            const filterState = Filters.getState();
            
            // Prepare options for API request
            const options = {
                page,
                limit: servicesPerPage,
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
            
            // Fetch services
            const result = await ServicesModule.getServices(options);
            
            // Hide loading state
            UI.hideLoading(servicesContainer);

            console.log(result);
            
            // Render services
            ServicesModule.renderServicesGrid(result.services, servicesContainer);
            
            // Update pagination
            Pagination.update(paginationContainer, {
                currentPage: page,
                totalPages: result.pagination.pages,
                totalItems: result.pagination.total,
                itemsPerPage: result.pagination.limit
            });
            
            // Update current page
            currentPage = page;
            
            // Scroll to top of services if not on first page
            if (page > 1) {
                servicesContainer.scrollIntoView({ behavior: 'smooth' });
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
            await loadServices(1);
            
            // Update URL with filter state
            const queryString = Filters.buildQueryString();
            const newUrl = `${window.location.pathname}${queryString ? `?${queryString}` : ''}`;
            window.history.pushState({}, '', newUrl);
        });
        
        // Initialize pagination
        Pagination.init(paginationContainer, {
            currentPage: 1,
            totalPages: 1,
            itemsPerPage: servicesPerPage
        }, async (paginationState) => {
            await loadServices(paginationState.currentPage);
            
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
        
        // Load initial services
        await loadServices(currentPage);
        
        // Add event listener for browser back/forward buttons
        window.addEventListener('popstate', async () => {
            // Parse new URL params
            const newFilterState = Filters.parseQueryString(window.location.search);
            const newUrlParams = new URLSearchParams(window.location.search);
            const newPage = newUrlParams.has('page') ? parseInt(newUrlParams.get('page')) : 1;
            
            // Update filter state and UI
            Filters.state = newFilterState;
            Filters.updateUI(filtersContainer);
            
            // Load services with new state
            await loadServices(newPage);
        });
    }
};

export default ServicesListModule; 