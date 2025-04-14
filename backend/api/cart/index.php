<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/CartController.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new CartController();
    $controller->getCart();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CartController();
    $controller->addToCart();
} else {
    apiError('Method not allowed', 405);
} 