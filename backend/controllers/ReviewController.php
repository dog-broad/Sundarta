<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class ReviewController extends BaseController {
    private $reviewModel;

    public function __construct() {
        $this->reviewModel = new ReviewModel();
    }

    /**
     * Get reviews for a product
     */
    public function getProductReviews() {
        $this->ensureMethodAllowed('GET');
        
        $productId = $this->getQueryParam('product_id');
        
        if (!$productId) {
            $this->sendError('Product ID is required', 400);
        }
        
        $reviews = $this->reviewModel->getProductReviews($productId);
        $averageRating = $this->reviewModel->getProductAverageRating($productId);
        
        $this->sendSuccess([
            'reviews' => $reviews,
            'average_rating' => $averageRating,
            'total_reviews' => count($reviews)
        ], 'Product reviews retrieved successfully');
    }

    /**
     * Get reviews for a service
     */
    public function getServiceReviews() {
        $this->ensureMethodAllowed('GET');
        
        $serviceId = $this->getQueryParam('service_id');
        
        if (!$serviceId) {
            $this->sendError('Service ID is required', 400);
        }
        
        $reviews = $this->reviewModel->getServiceReviews($serviceId);
        $averageRating = $this->reviewModel->getServiceAverageRating($serviceId);
        
        $this->sendSuccess([
            'reviews' => $reviews,
            'average_rating' => $averageRating,
            'total_reviews' => count($reviews)
        ], 'Service reviews retrieved successfully');
    }

    /**
     * Create a review for a product
     */
    public function createProductReview() {
        $this->ensureMethodAllowed('POST');
        
        requireLogin();
        
        $data = $this->getJsonData();
        
        // Validate required fields
        $requiredFields = ['product_id', 'rating', 'review'];
        $missingFields = $this->validateRequired($data, $requiredFields);
        
        if (!empty($missingFields)) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $missingFields
            ]);
        }
        
        // Validate rating
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            $this->sendError('Rating must be between 1 and 5', 400);
        }
        
        $userId = getCurrentUserId();
        
        // Check if user has already reviewed this product
        if ($this->reviewModel->hasUserReviewedProduct($userId, $data['product_id'])) {
            $this->sendError('You have already reviewed this product', 409);
        }
        
        // Create review
        $reviewId = $this->reviewModel->createProductReview(
            $userId,
            $data['product_id'],
            $data['rating'],
            $data['review']
        );
        
        if (!$reviewId) {
            $this->sendError('Failed to create review', 500);
        }
        
        $this->sendSuccess(['id' => $reviewId], 'Review created successfully', 201);
    }

    /**
     * Create a review for a service
     */
    public function createServiceReview() {
        $this->ensureMethodAllowed('POST');
        
        requireLogin();
        
        $data = $this->getJsonData();
        
        // Validate required fields
        $requiredFields = ['service_id', 'rating', 'review'];
        $missingFields = $this->validateRequired($data, $requiredFields);
        
        if (!empty($missingFields)) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $missingFields
            ]);
        }
        
        // Validate rating
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            $this->sendError('Rating must be between 1 and 5', 400);
        }
        
        $userId = getCurrentUserId();
        
        // Check if user has already reviewed this service
        if ($this->reviewModel->hasUserReviewedService($userId, $data['service_id'])) {
            $this->sendError('You have already reviewed this service', 409);
        }
        
        // Create review
        $reviewId = $this->reviewModel->createServiceReview(
            $userId,
            $data['service_id'],
            $data['rating'],
            $data['review']
        );
        
        if (!$reviewId) {
            $this->sendError('Failed to create review', 500);
        }
        
        $this->sendSuccess(['id' => $reviewId], 'Review created successfully', 201);
    }

    /**
     * Update a review
     */
    public function updateReview() {
        $this->ensureMethodAllowed('PUT');
        
        requireLogin();
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Review ID is required', 400);
        }
        
        $data = $this->getJsonData();
        
        // Validate required fields
        $requiredFields = ['rating', 'review'];
        $missingFields = $this->validateRequired($data, $requiredFields);
        
        if (!empty($missingFields)) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $missingFields
            ]);
        }
        
        // Validate rating
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            $this->sendError('Rating must be between 1 and 5', 400);
        }
        
        // Update review
        $success = $this->reviewModel->update($id, $data['rating'], $data['review']);
        
        if (!$success) {
            $this->sendError('Failed to update review', 500);
        }
        
        $this->sendSuccess([], 'Review updated successfully');
    }

    /**
     * Delete a review
     */
    public function deleteReview() {
        $this->ensureMethodAllowed('DELETE');
        
        requireLogin();
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Review ID is required', 400);
        }
        
        // Delete review
        $success = $this->reviewModel->deleteById($id);
        
        if (!$success) {
            $this->sendError('Failed to delete review', 500);
        }
        
        $this->sendSuccess([], 'Review deleted successfully');
    }

    /**
     * Get reviews by user
     */
    public function getMyReviews() {
        $this->ensureMethodAllowed('GET');
        
        requireLogin();
        
        $userId = getCurrentUserId();
        $reviews = $this->reviewModel->getByUser($userId);
        
        $this->sendSuccess($reviews, 'Your reviews retrieved successfully');
    }
} 