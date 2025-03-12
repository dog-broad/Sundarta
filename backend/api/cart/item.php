<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/CartController.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $controller = new CartController();
    $controller->updateCartItem();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $controller = new CartController();
    $controller->removeFromCart();
} else {
    apiError('Method not allowed', 405);
} 