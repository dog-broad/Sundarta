<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new ProductController();
    $controller->getAllProducts();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ProductController();
    $controller->createProduct();
} else {
    apiError('Method not allowed', 405);
} 