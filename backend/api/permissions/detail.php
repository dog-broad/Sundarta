<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/PermissionController.php';

$controller = new PermissionController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $controller->getById();
        break;
    case 'PUT':
        $controller->update();
        break;
    case 'DELETE':
        $controller->delete();
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