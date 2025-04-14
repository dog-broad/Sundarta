/**
 * Filters Utility Module
 * 
 * This module handles filter functionality:
 * - Managing filter state
 * - Building filter queries
 * - Handling filter UI
 */

const Filters = {
    /**
     * Current filter state
     */
    state: {
        category: null,
        search: '',
        sort: 'newest',
        priceRange: {
            min: null,
            max: null
        }
    },

    /**
     * Initialize filters
     * @param {HTMLElement} container - Container for filter UI
     * @param {Object} options - Configuration options
     * @param {Function} onFilterChange - Callback when filters change
     */
    init: (container, options = {}, onFilterChange = null) => {
        if (!container) return;
        
        // Merge provided options with defaults
        const config = {
            showCategories: true,
            showSort: true,
            showPriceRange: true,
            showSearch: true,
            categories: [],
            ...options
        };
        
        // Reset state
        Filters.state = {
            category: null,
            search: '',
            sort: 'newest',
            priceRange: {
                min: null,
                max: null
            }
        };
        
        // Initialize from URL if available
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('category')) {
            Filters.state.category = urlParams.get('category');
        }
        if (urlParams.has('sort')) {
            Filters.state.sort = urlParams.get('sort');
        }
        if (urlParams.has('search')) {
            Filters.state.search = urlParams.get('search');
        }
        if (urlParams.has('min_price')) {
            Filters.state.priceRange.min = parseFloat(urlParams.get('min_price'));
        }
        if (urlParams.has('max_price')) {
            Filters.state.priceRange.max = parseFloat(urlParams.get('max_price'));
        }
        
        // Build filter UI
        let filtersHtml = '<div class="filters">';
        
        // Search box
        if (config.showSearch) {
            filtersHtml += `
                <div class="filter-group mb-4">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" id="filter-search" 
                            placeholder="Search products..." 
                            value="${Filters.state.search}">
                    </div>
                </div>
            `;
        }
        
        // Filter controls wrapper
        filtersHtml += '<div class="flex flex-wrap gap-4 justify-between items-center mb-6">';
        
        // Categories filter
        if (config.showCategories && config.categories.length > 0) {
            filtersHtml += `
                <div class="filter-group">
                    <label class="input-label mb-2">Category</label>
                    <select id="filter-category" class="input-text">
                        <option value="">All Categories</option>
                        ${config.categories.map(cat => `
                            <option value="${cat.id}" ${Filters.state.category == cat.id ? 'selected' : ''}>
                                ${cat.name}
                            </option>
                        `).join('')}
                    </select>
                </div>
            `;
        }
        
        // Sort order
        if (config.showSort) {
            filtersHtml += `
                <div class="filter-group">
                    <label class="input-label mb-2">Sort By</label>
                    <select id="filter-sort" class="input-text">
                        <option value="newest" ${Filters.state.sort === 'newest' ? 'selected' : ''}>Newest</option>
                        <option value="price_low" ${Filters.state.sort === 'price_low' ? 'selected' : ''}>Price (Low to High)</option>
                        <option value="price_high" ${Filters.state.sort === 'price_high' ? 'selected' : ''}>Price (High to Low)</option>
                        <option value="rating" ${Filters.state.sort === 'rating' ? 'selected' : ''}>Rating</option>
                    </select>
                </div>
            `;
        }
        
        // Price range
        if (config.showPriceRange) {
            filtersHtml += `
                <div class="filter-group">
                    <label class="input-label mb-2">Price Range</label>
                    <div class="flex gap-2">
                        <input type="number" id="filter-price-min" class="input-text" 
                            placeholder="Min" min="0" step="10"
                            value="${Filters.state.priceRange.min || ''}">
                        <span class="py-2">to</span>
                        <input type="number" id="filter-price-max" class="input-text" 
                            placeholder="Max" min="0" step="10"
                            value="${Filters.state.priceRange.max || ''}">
                    </div>
                </div>
            `;
        }
        
        // Close filter controls wrapper
        filtersHtml += '</div>';
        
        // Active filters display & reset button
        filtersHtml += `
            <div class="active-filters mb-4 flex flex-wrap gap-2 items-center">
                <span class="text-sm text-text-light">Active Filters:</span>
                <div id="active-filters-list" class="flex flex-wrap gap-2">
                    <!-- Active filters will be populated here -->
                </div>
                <button id="reset-filters" class="btn py-1 px-3 text-sm btn-outline ml-auto ${Filters.hasActiveFilters() ? '' : 'hidden'}">
                    Reset All
                </button>
            </div>
        `;
        
        filtersHtml += '</div>';
        
        // Render filters
        container.innerHTML = filtersHtml;
        
        // Attach event listeners
        if (config.showSearch) {
            const searchInput = container.querySelector('#filter-search');
            searchInput.addEventListener('input', debounce((e) => {
                Filters.updateState('search', e.target.value);
                Filters.updateActiveFilters(container);
                if (onFilterChange) onFilterChange(Filters.getState());
            }, 500));
        }
        
        if (config.showCategories) {
            const categorySelect = container.querySelector('#filter-category');
            if (categorySelect) {
                categorySelect.addEventListener('change', (e) => {
                    Filters.updateState('category', e.target.value);
                    Filters.updateActiveFilters(container);
                    if (onFilterChange) onFilterChange(Filters.getState());
                });
            }
        }
        
        if (config.showSort) {
            const sortSelect = container.querySelector('#filter-sort');
            sortSelect.addEventListener('change', (e) => {
                Filters.updateState('sort', e.target.value);
                Filters.updateActiveFilters(container);
                if (onFilterChange) onFilterChange(Filters.getState());
            });
        }
        
        if (config.showPriceRange) {
            const minPrice = container.querySelector('#filter-price-min');
            const maxPrice = container.querySelector('#filter-price-max');
            
            minPrice.addEventListener('change', (e) => {
                Filters.updateState('priceRange', {
                    ...Filters.state.priceRange,
                    min: e.target.value ? parseFloat(e.target.value) : null
                });
                Filters.updateActiveFilters(container);
                if (onFilterChange) onFilterChange(Filters.getState());
            });
            
            maxPrice.addEventListener('change', (e) => {
                Filters.updateState('priceRange', {
                    ...Filters.state.priceRange,
                    max: e.target.value ? parseFloat(e.target.value) : null
                });
                Filters.updateActiveFilters(container);
                if (onFilterChange) onFilterChange(Filters.getState());
            });
        }
        
        // Reset filters button
        const resetBtn = container.querySelector('#reset-filters');
        resetBtn.addEventListener('click', () => {
            Filters.reset();
            Filters.updateUI(container);
            if (onFilterChange) onFilterChange(Filters.getState());
        });
        
        // Initialize active filters display
        Filters.updateActiveFilters(container, config);
    },

    /**
     * Update filter state
     * @param {string} key - State property to update
     * @param {*} value - New value
     */
    updateState: (key, value) => {
        if (key in Filters.state) {
            Filters.state[key] = value;
        }
    },

    /**
     * Get current filter state
     * @returns {Object} - Current filter state
     */
    getState: () => {
        return { ...Filters.state };
    },

    /**
     * Check if any filters are active
     * @returns {boolean} - True if any filters are active
     */
    hasActiveFilters: () => {
        return Filters.state.category || 
               Filters.state.search || 
               Filters.state.sort !== 'newest' ||
               Filters.state.priceRange.min !== null ||
               Filters.state.priceRange.max !== null;
    },

    /**
     * Reset filters to default state
     */
    reset: () => {
        Filters.state = {
            category: null,
            search: '',
            sort: 'newest',
            priceRange: {
                min: null,
                max: null
            }
        };
    },

    /**
     * Build query string from filter state
     * @returns {string} - Query string for API request
     */
    buildQueryString: () => {
        const params = new URLSearchParams();
        
        if (Filters.state.category) {
            params.append('category', Filters.state.category);
        }
        
        if (Filters.state.search) {
            params.append('search', Filters.state.search);
        }
        
        if (Filters.state.sort && Filters.state.sort !== 'newest') {
            params.append('sort', Filters.state.sort);
        }
        
        if (Filters.state.priceRange.min !== null) {
            params.append('min_price', Filters.state.priceRange.min);
        }
        
        if (Filters.state.priceRange.max !== null) {
            params.append('max_price', Filters.state.priceRange.max);
        }
        
        return params.toString();
    },

    /**
     * Parse query string into filter state
     * @param {string} queryString - URL query string
     * @returns {Object} - Filter state
     */
    parseQueryString: (queryString) => {
        const params = new URLSearchParams(queryString);
        const state = {
            category: null,
            search: '',
            sort: 'newest',
            priceRange: {
                min: null,
                max: null
            }
        };
        
        if (params.has('category')) {
            state.category = params.get('category');
        }
        
        if (params.has('search')) {
            state.search = params.get('search');
        }
        
        if (params.has('sort')) {
            state.sort = params.get('sort');
        }
        
        if (params.has('min_price')) {
            state.priceRange.min = parseFloat(params.get('min_price'));
        }
        
        if (params.has('max_price')) {
            state.priceRange.max = parseFloat(params.get('max_price'));
        }
        
        return state;
    },

    /**
     * Update filter UI based on state
     * @param {HTMLElement} container - Filter container element
     */
    updateUI: (container) => {
        if (!container) return;
        
        // Update search input
        const searchInput = container.querySelector('#filter-search');
        if (searchInput) {
            searchInput.value = Filters.state.search || '';
        }
        
        // Update category select
        const categorySelect = container.querySelector('#filter-category');
        if (categorySelect) {
            categorySelect.value = Filters.state.category || '';
        }
        
        // Update sort select
        const sortSelect = container.querySelector('#filter-sort');
        if (sortSelect) {
            sortSelect.value = Filters.state.sort || 'newest';
        }
        
        // Update price range inputs
        const minPrice = container.querySelector('#filter-price-min');
        const maxPrice = container.querySelector('#filter-price-max');
        
        if (minPrice) {
            minPrice.value = Filters.state.priceRange.min !== null ? Filters.state.priceRange.min : '';
        }
        
        if (maxPrice) {
            maxPrice.value = Filters.state.priceRange.max !== null ? Filters.state.priceRange.max : '';
        }
        
        // Update active filters display
        Filters.updateActiveFilters(container);
    },
    
    /**
     * Update active filters display
     * @param {HTMLElement} container - Filter container element
     * @param {Object} config - Configuration options with category labels
     */
    updateActiveFilters: (container, config = {}) => {
        if (!container) return;
        
        const activeFiltersContainer = container.querySelector('#active-filters-list');
        const resetBtn = container.querySelector('#reset-filters');
        
        if (!activeFiltersContainer) return;
        
        // Clear current active filters
        activeFiltersContainer.innerHTML = '';
        
        // Build active filters
        const activeFilters = [];
        
        if (Filters.state.category) {
            let categoryName = Filters.state.category;
            
            // Try to find category name from options
            if (config.categories) {
                const category = config.categories.find(c => c.id == Filters.state.category);
                if (category) {
                    categoryName = category.name;
                }
            }
            
            activeFilters.push({
                type: 'category',
                label: `Category: ${categoryName}`
            });
        }
        
        if (Filters.state.search) {
            activeFilters.push({
                type: 'search',
                label: `Search: ${Filters.state.search}`
            });
        }
        
        if (Filters.state.sort && Filters.state.sort !== 'newest') {
            let sortLabel = '';
            switch (Filters.state.sort) {
                case 'price_low':
                    sortLabel = 'Price (Low to High)';
                    break;
                case 'price_high':
                    sortLabel = 'Price (High to Low)';
                    break;
                case 'rating':
                    sortLabel = 'Rating';
                    break;
                default:
                    sortLabel = Filters.state.sort;
            }
            
            activeFilters.push({
                type: 'sort',
                label: `Sort: ${sortLabel}`
            });
        }
        
        if (Filters.state.priceRange.min !== null) {
            activeFilters.push({
                type: 'price_min',
                label: `Min Price: ₹${Filters.state.priceRange.min}`
            });
        }
        
        if (Filters.state.priceRange.max !== null) {
            activeFilters.push({
                type: 'price_max',
                label: `Max Price: ₹${Filters.state.priceRange.max}`
            });
        }
        
        // Add filter pills to UI
        activeFilters.forEach(filter => {
            const pill = document.createElement('div');
            pill.className = 'bg-sand-light rounded-full py-1 px-3 text-sm flex items-center';
            pill.innerHTML = `
                <span>${filter.label}</span>
                <button class="ml-2 text-primary" data-filter-type="${filter.type}">
                    <i class="fas fa-times-circle"></i>
                </button>
            `;
            
            // Add click handler to remove filter
            const removeBtn = pill.querySelector('button');
            removeBtn.addEventListener('click', () => {
                switch (filter.type) {
                    case 'category':
                        Filters.updateState('category', null);
                        break;
                    case 'search':
                        Filters.updateState('search', '');
                        break;
                    case 'sort':
                        Filters.updateState('sort', 'newest');
                        break;
                    case 'price_min':
                        Filters.updateState('priceRange', {
                            ...Filters.state.priceRange,
                            min: null
                        });
                        break;
                    case 'price_max':
                        Filters.updateState('priceRange', {
                            ...Filters.state.priceRange,
                            max: null
                        });
                        break;
                }
                
                // Update UI
                Filters.updateUI(container);
                
                // Trigger onchange callback if defined in parent scope
                if (container.filterChangeCallback) {
                    container.filterChangeCallback(Filters.getState());
                }
            });
            
            activeFiltersContainer.appendChild(pill);
        });
        
        // Show/hide reset button
        if (Filters.hasActiveFilters()) {
            resetBtn.classList.remove('hidden');
        } else {
            resetBtn.classList.add('hidden');
            
            // If no active filters, show "None" text
            const nonePill = document.createElement('div');
            nonePill.className = 'text-sm text-text-light';
            nonePill.textContent = 'None';
            activeFiltersContainer.appendChild(nonePill);
        }
    }
};

/**
 * Simple debounce function
 * @param {Function} func - Function to debounce
 * @param {number} wait - Milliseconds to wait
 * @returns {Function} - Debounced function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

export default Filters; 