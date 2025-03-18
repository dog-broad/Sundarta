/**
 * API Utility Module
 * 
 * This module provides a centralized way to make API calls to the backend.
 * It handles common functionality like:
 * - Adding authentication headers
 * - Error handling
 * - Response parsing
 * - Request/response interceptors
 */

const API = {
    baseUrl: '/sundarta/api',
    
    /**
     * Make API request with proper headers and error handling
     * @param {string} endpoint - API endpoint
     * @param {Object} options - Request options
     * @returns {Promise} - API response
     */
    request: async (endpoint, options = {}) => {
        const url = `${API.baseUrl}${endpoint}`;
        
        // Default headers
        const headers = {
            'Content-Type': 'application/json',
            ...options.headers
        };
        
        try {
            const response = await fetch(url, {
                ...options,
                headers,
                credentials: 'include' // Include cookies for session authentication
            });
            
            const data = await response.json();
            
            // Check for API error responses
            if (!response.ok) {
                const error = new Error(data.message || 'An error occurred');
                error.status = response.status;
                error.data = data;
                throw error;
            }
            
            return data;
        } catch (error) {
            // Handle network errors
            if (!error.status) {
                error.message = 'Network error. Please check your connection.';
            }
            
            // Log error for debugging
            console.error('API Error:', error);
            
            throw error;
        }
    },

    /**
     * GET request helper
     * @param {string} endpoint - API endpoint
     * @param {Object} params - Query parameters
     * @returns {Promise} - API response
     */
    get: async (endpoint, params = {}) => {
        // Add query parameters to URL if provided
        const queryString = Object.keys(params).length 
            ? `?${new URLSearchParams(params).toString()}`
            : '';
            
        return API.request(`${endpoint}${queryString}`, {
            method: 'GET'
        });
    },

    /**
     * POST request helper
     * @param {string} endpoint - API endpoint
     * @param {Object} data - Request body
     * @returns {Promise} - API response
     */
    post: async (endpoint, data = {}) => {
        return API.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    /**
     * PUT request helper
     * @param {string} endpoint - API endpoint
     * @param {Object} data - Request body
     * @returns {Promise} - API response
     */
    put: async (endpoint, data = {}) => {
        return API.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    /**
     * DELETE request helper
     * @param {string} endpoint - API endpoint
     * @returns {Promise} - API response
     */
    delete: async (endpoint) => {
        return API.request(endpoint, {
            method: 'DELETE'
        });
    }
};

export default API; 