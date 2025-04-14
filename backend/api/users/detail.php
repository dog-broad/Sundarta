<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/UserController.php';

$controller = new UserController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $controller->getUserById();
        break;
    case 'PUT':
        $controller->updateUser();
        break;
    case 'DELETE':
        $controller->deleteUser();
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