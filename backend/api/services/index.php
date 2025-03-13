<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/ServiceController.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

try {
    // Handle different request methods
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new ServiceController();
        $controller->getAllServices();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ServiceController();
        $controller->createService();
    } else {
        throw new Exception('Method not allowed', 405);
    }
} catch (Exception $e) {
    apiError($e->getMessage(), $e->getCode());
}
