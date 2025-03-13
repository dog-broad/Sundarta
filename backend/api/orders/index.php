<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/OrderController.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new OrderController();
    $controller->getAllOrders();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new OrderController();
    $controller->createOrder();
} else {
    apiError('Method not allowed', 405);
} 