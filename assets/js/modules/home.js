/**
 * Home Module
 * 
 * This module handles the home page functionality:
 * - Initializing featured products
 * - Initializing featured services
 * - Initializing testimonials
 * - Handling newsletter form
 */

import ProductsModule from './products.js';
import ServicesModule from './services.js';
import TestimonialsModule from './testimonials.js';
import UI from '../utils/ui.js';

const HomeModule = {    
    /**
     * Initialize newsletter form
     */
    initNewsletterForm: () => {
        const newsletterForm = document.querySelector('.newsletter-form');
        
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const email = newsletterForm.querySelector('input[type="email"]').value;
                
                // Here you would typically send the email to your API
                // For now, just show a success message
                UI.showAlert('Thank you for subscribing to our newsletter!', 'success');
                
                // Reset form
                newsletterForm.reset();
            });
        }
    },
    
    /**
     * Initialize all home page components
     */
    init: () => {
        // Initialize newsletter form
        HomeModule.initNewsletterForm();
        
        // Initialize featured products
        ProductsModule.initFeaturedProducts('featured-products', 3);
        
        // Initialize featured services
        ServicesModule.initFeaturedServices('featured-services', 3);
        
        // Initialize testimonials
        TestimonialsModule.initTestimonials('testimonials', 2);
    }
};

export default HomeModule; 