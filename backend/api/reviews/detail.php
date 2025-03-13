<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/ReviewController.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $controller = new ReviewController();
    $controller->updateReview();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $controller = new ReviewController();
    $controller->deleteReview();
} else {
    apiError('Method not allowed', 405);
} 