/**
 * Product Detail Page Initialization
 * 
 * This file initializes the product detail page functionality:
 * - Product information display
 * - Image gallery
 * - Add to cart
 * - Tabs
 * - Reviews
 */

import ProductDetailModule from './modules/product-detail.js';

document.addEventListener('DOMContentLoaded', () => {
    // Get product ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    
    // Check if user is authenticated
    const isAuthenticated = document.body.classList.contains('user-authenticated');
    
    if (productId) {
        // Initialize product detail
        ProductDetailModule.init('product-detail', productId, isAuthenticated);
    } else {
        // Handle missing product ID
        const container = document.getElementById('product-detail');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-4xl text-primary mb-4"></i>
                    <p class="text-text-light mb-4">Product ID is missing. Please go back to products page.</p>
                    <a href="/sundarta/products" class="btn btn-primary">Browse Products</a>
                </div>
            `;
        }
    }
}); 