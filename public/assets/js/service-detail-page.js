/**
 * Service Detail Page
 * 
 * This script initializes the service detail page:
 * - Loads service details
 * - Enables adding service to cart
 * - Initializes the review system
 */

import ServiceDetailModule from './modules/service-detail.js';

document.addEventListener('DOMContentLoaded', function() {
    // Get service ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const serviceId = urlParams.get('id');
    
    if (!serviceId) {
        window.location.href = '/services';
        return;
    }
    
    // Check if user is authenticated
    const isAuthenticated = document.body.classList.contains('user-authenticated');
    
    // Initialize service detail page
    ServiceDetailModule.init('service-detail', serviceId, isAuthenticated);
}); 