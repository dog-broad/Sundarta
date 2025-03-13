<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/ReviewController.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new ReviewController();
    $controller->getProductReviews();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ReviewController();
    $controller->createProductReview();
} else {
    apiError('Method not allowed', 405);
} 