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
    $controller->getProduct();
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $controller = new ProductController();
    $controller->updateProduct();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $controller = new ProductController();
    $controller->deleteProduct();
} else {
    apiError('Method not allowed', 405);
} 