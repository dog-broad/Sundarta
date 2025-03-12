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
    $controller->getCategory();
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $controller = new CategoryController();
    $controller->updateCategory();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $controller = new CategoryController();
    $controller->deleteCategory();
} else {
    apiError('Method not allowed', 405);
} 