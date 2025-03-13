<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/PermissionController.php';

$controller = new PermissionController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $controller->getUserPermissions();
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