/**
 * Reviews Module
 * 
 * This module handles product and service reviews:
 * - Fetching reviews from the API
 * - Displaying reviews with pagination
 * - Submitting new reviews
 * - Rating display and input
 * 
 * API Endpoints Used:
 * - GET /api/reviews/product - Get product reviews
 * - POST /api/reviews/product - Add product review
 * - GET /api/reviews/my-reviews - Get user's reviews
 * - GET /api/reviews/detail - Get review details (for editing)
 * - PUT /api/reviews/detail - Update a review
 * - DELETE /api/reviews/detail - Delete a review
 */

import API from '../utils/api.js';
import UI from '../utils/ui.js';
import Pagination from '../utils/pagination.js';

const ReviewsModule = {
    /**
     * Fetch reviews for a product
     * @param {number} productId - Product ID
     * @param {Object} options - Pagination options
     * @returns {Promise<Object>} - Reviews data
     */
    getProductReviews: async (productId, options = {}) => {
        const { page = 1, limit = 5 } = options;
        
        try {
            const response = await API.get('/reviews/product', { 
                product_id: productId,
                page,
                limit
            });
            
            if (response.success) {
                return response.data;
            }
            
            return {
                reviews: [],
                average_rating: 0,
                total_reviews: 0
            };
        } catch (error) {
            console.error('Error fetching product reviews:', error);
            return {
                reviews: [],
                average_rating: 0,
                total_reviews: 0
            };
        }
    },
    
    /**
     * Submit a new product review
     * @param {number} productId - Product ID
     * @param {number} rating - Rating (1-5)
     * @param {string} review - Review text
     * @returns {Promise<boolean>} - Success status
     */
    submitProductReview: async (productId, rating, review) => {
        try {
            const response = await API.post('/reviews/product', {
                product_id: productId,
                rating,
                review
            });
            
            return response.success;
        } catch (error) {
            console.error('Error submitting review:', error);
            return false;
        }
    },
    
    /**
     * Fetch user's reviews
     * @param {Object} options - Pagination options
     * @returns {Promise<Array>} - User's reviews
     */
    getMyReviews: async (options = {}) => {
        const { page = 1, limit = 5 } = options;
        
        try {
            const response = await API.get('/reviews/my-reviews', { page, limit });
            
            if (response.success) {
                return response.data;
            }
            
            return [];
        } catch (error) {
            console.error('Error fetching user reviews:', error);
            return [];
        }
    },
    
    /**
     * Delete a review
     * @param {number} reviewId - Review ID
     * @returns {Promise<boolean>} - Success status
     */
    deleteReview: async (reviewId) => {
        try {
            const response = await API.delete(`/reviews/detail?id=${reviewId}`);
            return response.success;
        } catch (error) {
            console.error('Error deleting review:', error);
            return false;
        }
    },
    
    /**
     * Update a review
     * @param {number} reviewId - Review ID
     * @param {number} rating - Rating (1-5)
     * @param {string} review - Review text
     * @returns {Promise<boolean>} - Success status
     */
    updateReview: async (reviewId, rating, review) => {
        try {
            const response = await API.put(`/reviews/detail?id=${reviewId}`, {
                rating,
                review
            });
            
            return response.success;
        } catch (error) {
            console.error('Error updating review:', error);
            return false;
        }
    },
    
    /**
     * Render star rating HTML
     * @param {number} rating - Rating value (0-5)
     * @param {boolean} editable - Whether the stars are clickable
     * @param {Function} onRatingChange - Callback when rating changes (for editable stars)
     * @returns {string} - HTML for star rating
     */
    renderStarRating: (rating, editable = false, onRatingChange = null) => {
        const fullStars = Math.floor(rating);
        const halfStar = rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
        
        if (editable) {
            // Render editable star rating
            let html = `<div class="star-rating editable" data-rating="${rating}">`;
            
            for (let i = 1; i <= 5; i++) {
                const activeClass = i <= rating ? 'active' : '';
                html += `<span class="star ${activeClass}" data-value="${i}"><i class="fas fa-star"></i></span>`;
            }
            
            html += '</div>';
            
            // Add event listener after rendering
            setTimeout(() => {
                const starRating = document.querySelector('.star-rating.editable');
                if (starRating) {
                    const stars = starRating.querySelectorAll('.star');
                    
                    stars.forEach(star => {
                        star.addEventListener('click', (e) => {
                            const value = parseInt(e.currentTarget.getAttribute('data-value'));
                            
                            // Update visual state
                            stars.forEach(s => {
                                const starValue = parseInt(s.getAttribute('data-value'));
                                if (starValue <= value) {
                                    s.classList.add('active');
                                } else {
                                    s.classList.remove('active');
                                }
                            });
                            
                            // Update rating value
                            starRating.setAttribute('data-rating', value);
                            
                            // Call callback if provided
                            if (onRatingChange) {
                                onRatingChange(value);
                            }
                        });
                    });
                }
            }, 0);
            
            return html;
        } else {
            // Render read-only star rating
            let starsHtml = '<div class="star-rating">';
            
            // Add full stars
            for (let i = 0; i < fullStars; i++) {
                starsHtml += '<i class="fas fa-star"></i>';
            }
            
            // Add half star if needed
            if (halfStar) {
                starsHtml += '<i class="fas fa-star-half-alt"></i>';
            }
            
            // Add empty stars
            for (let i = 0; i < emptyStars; i++) {
                starsHtml += '<i class="far fa-star"></i>';
            }
            
            starsHtml += '</div>';
            
            return starsHtml;
        }
    },
    
    /**
     * Format date for display
     * @param {string} dateString - ISO date string
     * @returns {string} - Formatted date
     */
    formatDate: (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-IN', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    },
    
    /**
     * Render reviews list
     * @param {Array} reviews - Array of review objects
     * @returns {string} - HTML for reviews list
     */
    renderReviewsList: (reviews) => {
        if (reviews.length === 0) {
            return `
                <div class="text-center py-8">
                    <i class="fas fa-comment-slash text-4xl text-text-light mb-4"></i>
                    <p class="text-text-light">No reviews yet. Be the first to review!</p>
                </div>
            `;
        }
        
        let html = '<div class="reviews-list">';
        
        reviews.forEach(review => {
            html += `
                <div class="card mb-4 review-card" data-review-id="${review.id}">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="flex items-center mb-1">
                                <span class="font-semibold mr-2">${review.username}</span>
                                ${ReviewsModule.renderStarRating(review.rating)}
                            </div>
                            <div class="text-sm text-text-light">
                                ${ReviewsModule.formatDate(review.created_at)}
                            </div>
                        </div>
                        ${review.is_owner ? `
                            <div class="review-actions">
                                <button class="text-text-light hover:text-primary edit-review-btn">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-text-light hover:text-primary ml-2 delete-review-btn">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        ` : ''}
                    </div>
                    <p class="review-text">${review.review}</p>
                </div>
            `;
        });
        
        html += '</div>';
        
        return html;
    },
    
    /**
     * Initialize reviews section
     * @param {string} containerId - ID of container element
     * @param {number} productId - Product ID
     * @param {boolean} isAuthenticated - Whether user is authenticated
     */
    initProductReviews: async (containerId, productId, isAuthenticated = false) => {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        // Create container sections
        container.innerHTML = `
            <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Customer Reviews</h2>
            
            <div class="review-summary mb-8">
                <!-- Will be populated with summary data -->
            </div>
            
            <div class="reviews-list-container">
                <!-- Will be populated with reviews -->
            </div>
            
            <div class="reviews-pagination">
                <!-- Will be populated with pagination controls -->
            </div>
            
            ${isAuthenticated ? `
                <div class="review-form-container mt-8">
                    <h3 class="font-heading text-xl mb-4">Write a Review</h3>
                    <div class="card">
                        <form id="review-form" class="review-form">
                            <div class="mb-4">
                                <label class="block mb-2">Your Rating</label>
                                <div id="rating-stars-container">
                                    <!-- Will be populated with star rating -->
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="review-text" class="input-label">Your Review</label>
                                <textarea id="review-text" class="input-text" rows="4" placeholder="Share your experience with this product..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                </div>
            ` : `
                <div class="mt-8 text-center p-6 bg-sand-light rounded-md">
                    <p>Please <a href="/sundarta/login" class="text-primary hover-link">sign in</a> to write a review.</p>
                </div>
            `}
        `;
        
        // Initialize review form if authenticated
        if (isAuthenticated) {
            const ratingContainer = document.getElementById('rating-stars-container');
            let selectedRating = 5;
            
            // Initialize star rating
            ratingContainer.innerHTML = ReviewsModule.renderStarRating(selectedRating, true, (newRating) => {
                selectedRating = newRating;
            });
            
            // Add form submit handler
            const form = document.getElementById('review-form');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const reviewText = document.getElementById('review-text').value.trim();
                
                if (!reviewText) {
                    UI.showError('Please write your review before submitting.');
                    return;
                }
                
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';
                
                // Submit review
                const success = await ReviewsModule.submitProductReview(
                    productId,
                    selectedRating,
                    reviewText
                );
                
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
                
                if (success) {
                    // Show success message
                    UI.showSuccess('Your review has been submitted!');
                    
                    // Reset form
                    form.reset();
                    
                    // Reset rating
                    selectedRating = 5;
                    ratingContainer.innerHTML = ReviewsModule.renderStarRating(selectedRating, true, (newRating) => {
                        selectedRating = newRating;
                    });
                    
                    // Reload reviews
                    loadReviews(1);
                } else {
                    UI.showError('Failed to submit review. Please try again.');
                }
            });
        }
        
        // Load reviews (function defined below)
        const loadReviews = async (page = 1, limit = 5) => {
            const reviewsContainer = container.querySelector('.reviews-list-container');
            const paginationContainer = container.querySelector('.reviews-pagination');
            const summaryContainer = container.querySelector('.review-summary');
            
            // Show loading state
            reviewsContainer.innerHTML = `
                <div class="text-center py-8">
                    <div class="loading-spinner">
                        <div class="spinner-container">
                            <div class="spinner"></div>
                        </div>
                    </div>
                    <p class="mt-4 text-text-light">Loading reviews...</p>
                </div>
            `;
            
            // Fetch reviews
            const data = await ReviewsModule.getProductReviews(productId, { page, limit });
            
            // Update summary
            summaryContainer.innerHTML = `
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center">
                        <div class="text-4xl font-semibold mr-4">${parseFloat(data.average_rating).toFixed(1)}</div>
                        <div>
                            <div class="text-xl">
                                ${ReviewsModule.renderStarRating(data.average_rating)}
                            </div>
                            <div class="text-sm text-text-light mt-1">
                                Based on ${data.total_reviews} review${data.total_reviews !== 1 ? 's' : ''}
                            </div>
                        </div>
                    </div>
                    
                    <div class="rating-distribution">
                        <!-- Rating distribution bars would go here if data is available -->
                    </div>
                </div>
            `;
            
            // Update reviews list
            reviewsContainer.innerHTML = ReviewsModule.renderReviewsList(data.reviews || []);
            
            // Add event listeners for edit/delete buttons
            const editButtons = reviewsContainer.querySelectorAll('.edit-review-btn');
            const deleteButtons = reviewsContainer.querySelectorAll('.delete-review-btn');
            
            editButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    const reviewCard = e.target.closest('.review-card');
                    const reviewId = reviewCard.getAttribute('data-review-id');
                    const reviewText = reviewCard.querySelector('.review-text').textContent;
                    const ratingStars = reviewCard.querySelectorAll('.star-rating i.fas, .star-rating i.fa-star-half-alt').length;
                    
                    // Show edit form modal
                    UI.showModal({
                        title: 'Edit Your Review',
                        content: `
                            <div class="mb-4">
                                <label class="block mb-2">Your Rating</label>
                                <div class="edit-rating-container">
                                    ${ReviewsModule.renderStarRating(ratingStars, true)}
                                </div>
                            </div>
                            <div>
                                <label class="input-label">Your Review</label>
                                <textarea class="input-text edit-review-text" rows="4">${reviewText}</textarea>
                            </div>
                        `,
                        buttons: [
                            {
                                text: 'Cancel',
                                class: 'btn-outline'
                            },
                            {
                                text: 'Save Changes',
                                class: 'btn-primary',
                                callback: async (modal) => {
                                    const updatedRating = parseInt(modal.querySelector('.star-rating').getAttribute('data-rating'));
                                    const updatedText = modal.querySelector('.edit-review-text').value.trim();
                                    
                                    if (!updatedText) {
                                        UI.showError('Review text cannot be empty.');
                                        return false; // Keep modal open
                                    }
                                    
                                    const success = await ReviewsModule.updateReview(
                                        reviewId,
                                        updatedRating,
                                        updatedText
                                    );
                                    
                                    if (success) {
                                        UI.showSuccess('Your review has been updated.');
                                        loadReviews(page); // Reload current page
                                    } else {
                                        UI.showError('Failed to update review.');
                                    }
                                }
                            }
                        ]
                    });
                });
            });
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    const reviewCard = e.target.closest('.review-card');
                    const reviewId = reviewCard.getAttribute('data-review-id');
                    
                    // Show confirmation modal
                    UI.showModal({
                        title: 'Delete Review',
                        content: 'Are you sure you want to delete your review? This action cannot be undone.',
                        buttons: [
                            {
                                text: 'Cancel',
                                class: 'btn-outline'
                            },
                            {
                                text: 'Delete',
                                class: 'btn-primary',
                                callback: async () => {
                                    const success = await ReviewsModule.deleteReview(reviewId);
                                    
                                    if (success) {
                                        UI.showSuccess('Your review has been deleted.');
                                        loadReviews(page); // Reload current page
                                    } else {
                                        UI.showError('Failed to delete review.');
                                    }
                                }
                            }
                        ]
                    });
                });
            });
            
            // Initialize pagination
            if (data.total_reviews > 0) {
                const totalPages = Math.ceil(data.total_reviews / limit);
                
                Pagination.init(paginationContainer, {
                    currentPage: page,
                    totalPages: totalPages,
                    totalItems: data.total_reviews,
                    itemsPerPage: limit
                }, (pagination) => {
                    loadReviews(pagination.currentPage, limit);
                });
            } else {
                paginationContainer.innerHTML = '';
            }
        };
        
        // Initial reviews load
        loadReviews(1);
    }
};

// Add CSS for star rating
const style = document.createElement('style');
style.textContent = `
    .star-rating {
        display: inline-flex;
        color: var(--primary);
    }
    
    .star-rating.editable {
        cursor: pointer;
    }
    
    .star-rating.editable .star {
        color: var(--text-light);
        margin-right: 5px;
        transition: color 0.2s ease;
    }
    
    .star-rating.editable .star.active {
        color: var(--primary);
    }
    
    .star-rating.editable .star:hover {
        color: var(--primary-light);
    }
    
    .review-card {
        transition: transform 0.2s ease;
    }
    
    .review-card:hover {
        transform: translateY(-2px);
    }
`;
document.head.appendChild(style);

export default ReviewsModule; 