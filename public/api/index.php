<?php
// Load environment variables
if (file_exists(__DIR__ . '/../../.env')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
    $dotenv->load();
}

// Set error reporting
if ($_ENV['APP_ENV'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set JSON content type
header('Content-Type: application/json');

// Set default timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Get the API route from the URL parameter
$apiRoute = $_GET['api_route'] ?? '';

// Map API routes to their respective files
$apiRoutes = [
    // Users
    'users/register' => '../../backend/api/users/register.php',
    'users/login' => '../../backend/api/users/login.php',
    'users/logout' => '../../backend/api/users/logout.php',
    'users/profile' => '../../backend/api/users/profile.php',
    'users/password' => '../../backend/api/users/password.php',
    'users/list' => '../../backend/api/users/list.php',
    'users/detail' => '../../backend/api/users/detail.php',
    'users/roles' => '../../backend/api/users/roles.php',
    
    // Roles
    'roles' => '../../backend/api/roles/index.php',
    'roles/detail' => '../../backend/api/roles/detail.php',
    'roles/permissions' => '../../backend/api/roles/permissions.php',
    'roles/users' => '../../backend/api/roles/users.php',

    // Permissions
    'permissions' => '../../backend/api/permissions/index.php',
    'permissions/detail' => '../../backend/api/permissions/detail.php',
    'permissions/roles' => '../../backend/api/permissions/roles.php',
    'permissions/check' => '../../backend/api/permissions/check.php',
    'permissions/user' => '../../backend/api/permissions/user.php',
    
    // Products
    'products' => '../../backend/api/products/index.php',
    'products/detail' => '../../backend/api/products/detail.php',
    'products/featured' => '../../backend/api/products/featured.php',
    'products/search' => '../../backend/api/products/search.php',
    'products/category' => '../../backend/api/products/category.php',
    
    // Services
    'services' => '../../backend/api/services/index.php',
    'services/detail' => '../../backend/api/services/detail.php',
    'services/featured' => '../../backend/api/services/featured.php',
    'services/search' => '../../backend/api/services/search.php',
    'services/category' => '../../backend/api/services/category.php',
    'services/seller' => '../../backend/api/services/seller.php',
    'services/my-services' => '../../backend/api/services/my-services.php',
    
    // Categories
    'categories' => '../../backend/api/categories/index.php',
    'categories/detail' => '../../backend/api/categories/detail.php',

    // Reviews
    'reviews/product' => '../../backend/api/reviews/product.php',
    'reviews/service' => '../../backend/api/reviews/service.php',
    'reviews/detail' => '../../backend/api/reviews/detail.php',
    'reviews/my-reviews' => '../../backend/api/reviews/my-reviews.php',
    'reviews/positive-reviews' => '../../backend/api/reviews/positive-reviews.php',
    
    // Orders
    'orders' => '../../backend/api/orders/index.php',
    'orders/detail' => '../../backend/api/orders/detail.php',
    'orders/my-orders' => '../../backend/api/orders/my-orders.php',
    'orders/statistics' => '../../backend/api/orders/statistics.php',
    'orders/checkout' => '../../backend/api/orders/checkout.php',
    
    // Cart
    'cart' => '../../backend/api/cart/index.php',
    'cart/item' => '../../backend/api/cart/item.php',
    'cart/clear' => '../../backend/api/cart/clear.php',
    'cart/check-stock' => '../../backend/api/cart/check-stock.php',
];

// Check if the route exists
if (isset($apiRoutes[$apiRoute])) {
    require_once $apiRoutes[$apiRoute];
    exit;
}

// If route not found, return 404
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'API endpoint not found: ' . $apiRoute
]);
exit; 