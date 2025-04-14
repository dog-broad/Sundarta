<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/RoleController.php';

$controller = new RoleController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $controller->getAll();
        break;
    case 'POST':
        $controller->create();
        break;
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
}
?> 