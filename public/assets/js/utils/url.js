/**
 * URL Utility Module
 * 
 * This module provides helper functions for URL management
 * in the Sundarta application.
 */

const URL = {
    /**
     * Base URL for the application
     * No subdirectory should be used
     */
    baseUrl: '',
    
    /**
     * Get full URL with correct base path
     * @param {string} path - Path to append to base URL
     * @returns {string} - Full URL
     */
    path: (path) => {
        // Ensure the path starts with a slash
        if (path && !path.startsWith('/')) {
            path = '/' + path;
        }
        
        console.log("Just assigned URL: ", URL.baseUrl + path);
        
        return URL.baseUrl + path;
    },
    
    /**
     * Redirect to a path within the application
     * @param {string} path - Path to redirect to
     */
    redirect: (path) => {
        console.log("Path redirect to: ", path);
        window.location.href = URL.path(path);
    },
    
    /**
     * Get URL parameter value
     * @param {string} name - Parameter name
     * @returns {string|null} - Parameter value or null
     */
    getParam: (name) => {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    },
    
    /**
     * Build URL with query parameters
     * @param {string} path - Base path
     * @param {Object} params - Query parameters
     * @returns {string} - URL with query parameters
     */
    buildQuery: (path, params = {}) => {
        const url = new URL(URL.path(path), window.location.origin);
        
        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== undefined) {
                url.searchParams.append(key, params[key]);
            }
        });
        
        return url.pathname + url.search;
    }
};

export default URL; 