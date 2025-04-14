<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/UserController.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Only allow GET and PUT requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new UserController();
    $controller->getProfile();
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $controller = new UserController();
    $controller->updateProfile();
} else {
    apiError('Method not allowed', 405);
} 