/**
 * Testimonials Module
 * 
 * This module handles testimonial-related functionality:
 * - Fetching testimonials from the API
 * - Displaying testimonials in a slider or grid
 */

import API from '../utils/api.js';

const TestimonialsModule = {
    /**
     * Fetch testimonials
     * @param {number} limit - Number of testimonials to fetch
     * @returns {Promise<Array>} - Array of testimonials
     */
    getTestimonials: async (limit = 2) => {
        try {
            const response = await API.get('/reviews/featured', { limit });
            
            if (response.success) {
                return response.data;
            }
            
            // Fallback testimonials if API fails
            return [
                {
                    id: 1,
                    user: {
                        name: 'Priya Sharma',
                        location: 'Delhi',
                        avatar: 'https://img.freepik.com/free-photo/lifestyle-beauty-fashion-people-emotions-concept-young-asian-female-office-manager-ceo-with-pleased-expression-standing-white-background-smiling-with-arms-crossed-chest_1258-59329.jpg'
                    },
                    content: "I've been using Sundarta's skincare products for three months now, and the results are amazing! My skin feels rejuvenated and healthier than ever.",
                    rating: 5
                },
                {
                    id: 2,
                    user: {
                        name: 'Rahul Mehta',
                        location: 'Mumbai',
                        avatar: 'https://img.freepik.com/free-photo/smiling-beautiful-woman-shows-heart-gesture-near-chest-express-like-sympathy-passionate-about-smth-standing-against-white-wall-t-shirt_176420-40420.jpg'
                    },
                    content: "The wellness services at Sundarta have transformed my self-care routine. The staff is knowledgeable and professional. Highly recommended!",
                    rating: 4.5
                }
            ];
        } catch (error) {
            console.error('Error fetching testimonials:', error);
            
            // Fallback testimonials if API fails
            return [
                {
                    id: 1,
                    user: {
                        name: 'Priya Sharma',
                        location: 'Delhi',
                        avatar: 'https://img.freepik.com/free-photo/lifestyle-beauty-fashion-people-emotions-concept-young-asian-female-office-manager-ceo-with-pleased-expression-standing-white-background-smiling-with-arms-crossed-chest_1258-59329.jpg'
                    },
                    content: "I've been using Sundarta's skincare products for three months now, and the results are amazing! My skin feels rejuvenated and healthier than ever.",
                    rating: 5
                },
                {
                    id: 2,
                    user: {
                        name: 'Rahul Mehta',
                        location: 'Mumbai',
                        avatar: 'https://img.freepik.com/free-photo/smiling-beautiful-woman-shows-heart-gesture-near-chest-express-like-sympathy-passionate-about-smth-standing-against-white-wall-t-shirt_176420-40420.jpg'
                    },
                    content: "The wellness services at Sundarta have transformed my self-care routine. The staff is knowledgeable and professional. Highly recommended!",
                    rating: 4.5
                }
            ];
        }
    },
    
    /**
     * Render testimonial HTML
     * @param {Object} testimonial - Testimonial data
     * @returns {string} - HTML for testimonial
     */
    renderTestimonial: (testimonial) => {
        return `
            <div class="testimonial">
                <img src="${testimonial.user.avatar}" alt="${testimonial.user.name}" class="testimonial-avatar">
                <div class="testimonial-quote">
                    <p class="mb-4">${testimonial.content}</p>
                    <p class="font-semibold">- ${testimonial.user.name}</p>
                    <p class="text-sm text-text-light">${testimonial.user.location}</p>
                </div>
            </div>
        `;
    },
    
    /**
     * Render testimonials grid
     * @param {Array} testimonials - Array of testimonial objects
     * @param {HTMLElement} container - Container element
     */
    renderTestimonialsGrid: (testimonials, container) => {
        if (!container) return;
        
        if (testimonials.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-quote-right text-4xl text-text-light mb-4"></i>
                    <p class="text-text-light">No testimonials found.</p>
                </div>
            `;
            return;
        }
        
        const testimonialsHtml = testimonials.map(testimonial => 
            TestimonialsModule.renderTestimonial(testimonial)
        ).join('');
        
        container.innerHTML = testimonialsHtml;
    },
    
    /**
     * Initialize testimonials section
     * @param {string} containerId - ID of container element
     * @param {number} limit - Number of testimonials to display
     */
    initTestimonials: async (containerId = 'testimonials', limit = 2) => {
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
                <p class="mt-4 text-text-light">Loading testimonials...</p>
            </div>
        `;
        
        // Fetch testimonials
        const testimonials = await TestimonialsModule.getTestimonials(limit);
        
        // Render testimonials
        TestimonialsModule.renderTestimonialsGrid(testimonials, container);
    }
};

export default TestimonialsModule; 