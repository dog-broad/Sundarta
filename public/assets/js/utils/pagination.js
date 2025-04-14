/**
 * Pagination Utility Module
 * 
 * This module handles pagination functionality:
 * - Renders pagination controls
 * - Manages pagination state
 * - Supports both offset-based and cursor-based pagination
 */

const Pagination = {
    /**
     * Default pagination state
     */
    defaultState: {
        currentPage: 1,
        totalPages: 1,
        totalItems: 0,
        itemsPerPage: 12,
        pageRangeDisplayed: 3,
        cursor: null,
        type: 'offset' // 'offset' or 'cursor'
    },

    /**
     * Initialize pagination
     * @param {HTMLElement} container - Container for pagination UI
     * @param {Object} options - Pagination configuration
     * @param {Function} onPageChange - Callback when page changes
     */
    init: (container, options = {}, onPageChange = null) => {
        if (!container) return;

        // Create pagination state in container element
        container.paginationState = {
            ...Pagination.defaultState,
            ...options
        };

        // Store callback in container
        container.onPageChange = onPageChange;

        // Initial render
        Pagination.render(container);
    },

    /**
     * Render pagination controls
     * @param {HTMLElement} container - Container element
     */
    render: (container) => {
        if (!container || !container.paginationState) return;

        const { currentPage, totalPages, pageRangeDisplayed, type } = container.paginationState;

        // Don't render if only one page
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        // Create pagination HTML
        let paginationHtml = '<div class="pagination flex items-center justify-center gap-2 my-8">';

        // Previous button
        paginationHtml += `
            <button class="pagination-btn prev ${currentPage <= 1 ? 'disabled' : ''}" 
                ${currentPage <= 1 ? 'disabled' : ''} data-page="${currentPage - 1}">
                <i class="fas fa-chevron-left"></i>
            </button>
        `;

        // Calculate range of pages to show
        const halfRange = Math.floor(pageRangeDisplayed / 2);
        let startPage = Math.max(currentPage - halfRange, 1);
        let endPage = Math.min(startPage + pageRangeDisplayed - 1, totalPages);

        if (endPage - startPage + 1 < pageRangeDisplayed) {
            startPage = Math.max(endPage - pageRangeDisplayed + 1, 1);
        }

        // First page
        if (startPage > 1) {
            paginationHtml += `
                <button class="pagination-btn" data-page="1">1</button>
            `;

            if (startPage > 2) {
                paginationHtml += `
                    <span class="pagination-ellipsis">...</span>
                `;
            }
        }

        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <button class="pagination-btn ${i === currentPage ? 'active' : ''}" 
                    data-page="${i}">${i}</button>
            `;
        }

        // Last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHtml += `
                    <span class="pagination-ellipsis">...</span>
                `;
            }

            paginationHtml += `
                <button class="pagination-btn" data-page="${totalPages}">${totalPages}</button>
            `;
        }

        // Next button
        paginationHtml += `
            <button class="pagination-btn next ${currentPage >= totalPages ? 'disabled' : ''}" 
                ${currentPage >= totalPages ? 'disabled' : ''} data-page="${currentPage + 1}">
                <i class="fas fa-chevron-right"></i>
            </button>
        `;

        paginationHtml += '</div>';

        // Set HTML
        container.innerHTML = paginationHtml;

        // Add event listeners
        const buttons = container.querySelectorAll('.pagination-btn:not(.disabled)');
        buttons.forEach(button => {
            button.addEventListener('click', (e) => {
                const page = parseInt(e.currentTarget.getAttribute('data-page'));
                if (page) {
                    Pagination.goToPage(container, page);
                }
            });
        });
    },

    /**
     * Go to specific page
     * @param {HTMLElement} container - Container element
     * @param {number} page - Page number
     */
    goToPage: (container, page) => {
        if (!container || !container.paginationState) return;

        const { totalPages, type } = container.paginationState;
        
        // Validate page
        if (page < 1 || page > totalPages) return;
        
        // Update state
        container.paginationState.currentPage = page;
        
        // Render updated pagination
        Pagination.render(container);
        
        // Call onPageChange callback if defined
        if (container.onPageChange) {
            container.onPageChange(container.paginationState);
        }
    },

    /**
     * Update pagination with new data
     * @param {HTMLElement} container - Container element
     * @param {Object} data - New pagination data
     */
    update: (container, data) => {
        if (!container || !container.paginationState) return;

        // Merge new data with existing state
        container.paginationState = {
            ...container.paginationState,
            ...data
        };

        // Rerender pagination
        Pagination.render(container);
    },

    /**
     * Calculate total pages based on total items and items per page
     * @param {number} totalItems - Total number of items
     * @param {number} itemsPerPage - Number of items per page
     * @returns {number} - Total number of pages
     */
    calculateTotalPages: (totalItems, itemsPerPage) => {
        return Math.ceil(totalItems / itemsPerPage);
    },

    /**
     * Get current pagination state
     * @param {HTMLElement} container - Container element
     * @returns {Object} - Current pagination state
     */
    getState: (container) => {
        if (!container || !container.paginationState) return Pagination.defaultState;
        return { ...container.paginationState };
    }
};

// Add CSS for pagination
const style = document.createElement('style');
style.textContent = `
    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 8px;
        border-radius: 4px;
        background-color: var(--surface);
        border: 1px solid var(--border);
        color: var(--text);
        transition: all 0.2s ease;
    }

    .pagination-btn:hover:not(.disabled, .active) {
        background-color: var(--sand-light);
        border-color: var(--primary);
        color: var(--primary);
    }

    .pagination-btn.active {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .pagination-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-ellipsis {
        padding: 0 8px;
        color: var(--text-light);
    }
`;
document.head.appendChild(style);

export default Pagination; 