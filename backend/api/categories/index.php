<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/CategoryController.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new CategoryController();
    $controller->getAllCategories();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CategoryController();
    $controller->createCategory();
} else {
    apiError('Method not allowed', 405);
} 