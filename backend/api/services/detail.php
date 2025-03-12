<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/ServiceController.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new ServiceController();
    $controller->getService();
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $controller = new ServiceController();
    $controller->updateService();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $controller = new ServiceController();
    $controller->deleteService();
} else {
    apiError('Method not allowed', 405);
} 