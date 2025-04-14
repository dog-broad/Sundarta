<?php
require_once __DIR__ . '/BaseModel.php';

class ReviewModel extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'reviews';
    }

    /**
     * Create a new product review
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @param int $rating Rating (1-5)
     * @param string $review Review text
     * @return int|false Review ID or false on failure
     */
    public function createProductReview($userId, $productId, $rating, $review) {
        // Check if user has already reviewed this product
        if ($this->hasUserReviewedProduct($userId, $productId)) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (user_id, product_id, rating, review) 
                VALUES (?, ?, ?, ?)";
        return $this->insert($sql, [$userId, $productId, $rating, $review], 'iiis');
    }

    /**
     * Create a new service review
     * 
     * @param int $userId User ID
     * @param int $serviceId Service ID
     * @param int $rating Rating (1-5)
     * @param string $review Review text
     * @return int|false Review ID or false on failure
     */
    public function createServiceReview($userId, $serviceId, $rating, $review) {
        // Check if user has already reviewed this service
        if ($this->hasUserReviewedService($userId, $serviceId)) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (user_id, service_id, rating, review) 
                VALUES (?, ?, ?, ?)";
        return $this->insert($sql, [$userId, $serviceId, $rating, $review], 'iiis');
    }

    /**
     * Update a review
     * 
     * @param int $id Review ID
     * @param int $rating Rating (1-5)
     * @param string $review Review text
     * @return bool True on success, false on failure
     */
    public function update($id, $rating, $review) {
        $sql = "UPDATE {$this->table} SET rating = ?, review = ? WHERE id = ?";
        return $this->updateOrDelete($sql, [$rating, $review, $id], 'isi') !== false;
    }

    /**
     * Check if a user has already reviewed a product
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @return bool True if user has already reviewed the product, false otherwise
     */
    public function hasUserReviewedProduct($userId, $productId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        $result = $this->select($sql, [$userId, $productId], 'ii');
        return ($result[0]['count'] ?? 0) > 0;
    }

    /**
     * Check if a user has already reviewed a service
     * 
     * @param int $userId User ID
     * @param int $serviceId Service ID
     * @return bool True if user has already reviewed the service, false otherwise
     */
    public function hasUserReviewedService($userId, $serviceId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND service_id = ?";
        $result = $this->select($sql, [$userId, $serviceId], 'ii');
        return ($result[0]['count'] ?? 0) > 0;
    }

    /**
     * Get reviews for a product
     * 
     * @param int $productId Product ID
     * @return array Array of reviews
     */
    public function getProductReviews($productId) {
        $sql = "SELECT r.*, u.username 
                FROM {$this->table} r
                JOIN users u ON r.user_id = u.id
                WHERE r.product_id = ?
                ORDER BY r.created_at DESC";
        return $this->select($sql, [$productId], 'i');
    }

    /**
     * Get reviews for a service
     * 
     * @param int $serviceId Service ID
     * @return array Array of reviews
     */
    public function getServiceReviews($serviceId) {
        $sql = "SELECT r.*, u.username 
                FROM {$this->table} r
                JOIN users u ON r.user_id = u.id
                WHERE r.service_id = ?
                ORDER BY r.created_at DESC";
        return $this->select($sql, [$serviceId], 'i');
    }

    /**
     * Get average rating for a product
     * 
     * @param int $productId Product ID
     * @return float Average rating
     */
    public function getProductAverageRating($productId) {
        $sql = "SELECT AVG(rating) as avg_rating FROM {$this->table} WHERE product_id = ?";
        $result = $this->select($sql, [$productId], 'i');
        return round($result[0]['avg_rating'] ?? 0, 1);
    }

    /**
     * Get average rating for a service
     * 
     * @param int $serviceId Service ID
     * @return float Average rating
     */
    public function getServiceAverageRating($serviceId) {
        $sql = "SELECT AVG(rating) as avg_rating FROM {$this->table} WHERE service_id = ?";
        $result = $this->select($sql, [$serviceId], 'i');
        return round($result[0]['avg_rating'] ?? 0, 1);
    }

    /**
     * Get reviews by user
     * 
     * @param int $userId User ID
     * @return array Array of reviews
     */
    public function getByUser($userId) {
        $sql = "SELECT r.*, 
                p.name as product_name, 
                s.name as service_name
                FROM {$this->table} r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN services s ON r.service_id = s.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC";
        return $this->select($sql, [$userId], 'i');
    }
} 