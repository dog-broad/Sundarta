<?php
// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    require_once __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
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

// Set default timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Actual URL beig called: http://localhost/sundarta/api/users/login
// What index.php sees: http://localhost/sundarta/index.php?api_route=users/login

// Handle API requests
if (isset($_GET['api_route'])) {
    $apiRoute = $_GET['api_route'];
    
    // Map API routes to their respective files
    $apiRoutes = [
        // Users
        'users/register' => 'backend/api/users/register.php',
        'users/login' => 'backend/api/users/login.php',
        'users/logout' => 'backend/api/users/logout.php',
        'users/profile' => 'backend/api/users/profile.php',
        'users/password' => 'backend/api/users/password.php',
        'users/list' => 'backend/api/users/list.php',
        'users/detail' => 'backend/api/users/detail.php',
        'users/roles' => 'backend/api/users/roles.php',
        
        // Roles
        'roles' => 'backend/api/roles/index.php',
        'roles/detail' => 'backend/api/roles/detail.php',
        'roles/permissions' => 'backend/api/roles/permissions.php',
        'roles/users' => 'backend/api/roles/users.php',

        // Permissions
        'permissions' => 'backend/api/permissions/index.php',
        'permissions/detail' => 'backend/api/permissions/detail.php',
        'permissions/roles' => 'backend/api/permissions/roles.php',
        'permissions/check' => 'backend/api/permissions/check.php',
        'permissions/user' => 'backend/api/permissions/user.php',
        
        // Products
        'products' => 'backend/api/products/index.php',
        'products/detail' => 'backend/api/products/detail.php',
        'products/featured' => 'backend/api/products/featured.php',
        'products/search' => 'backend/api/products/search.php',
        'products/category' => 'backend/api/products/category.php',
        
        // Services
        'services' => 'backend/api/services/index.php',
        'services/detail' => 'backend/api/services/detail.php',
        'services/featured' => 'backend/api/services/featured.php',
        'services/search' => 'backend/api/services/search.php',
        'services/category' => 'backend/api/services/category.php',
        'services/seller' => 'backend/api/services/seller.php',
        'services/my-services' => 'backend/api/services/my-services.php',
        
        // Categories
        'categories' => 'backend/api/categories/index.php',
        'categories/detail' => 'backend/api/categories/detail.php',

        // Reviews
        'reviews/product' => 'backend/api/reviews/product.php',
        'reviews/service' => 'backend/api/reviews/service.php',
        'reviews/detail' => 'backend/api/reviews/detail.php',
        'reviews/my-reviews' => 'backend/api/reviews/my-reviews.php',
        'reviews/positive-reviews' => 'backend/api/reviews/positive-reviews.php',
        
        // Orders
        'orders' => 'backend/api/orders/index.php',
        'orders/detail' => 'backend/api/orders/detail.php',
        'orders/my-orders' => 'backend/api/orders/my-orders.php',
        'orders/statistics' => 'backend/api/orders/statistics.php',
        'orders/checkout' => 'backend/api/orders/checkout.php',
        
        // Cart
        'cart' => 'backend/api/cart/index.php',
        'cart/item' => 'backend/api/cart/item.php',
        'cart/clear' => 'backend/api/cart/clear.php',
        'cart/check-stock' => 'backend/api/cart/check-stock.php',
    ];
    
    // Check if the route exists
    if (isset($apiRoutes[$apiRoute])) {
        require_once $apiRoutes[$apiRoute];
        exit;
    }

    // If route not found, return 404
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'API endpoint not found'
    ]);
    exit;
}

// Actual URL beig called: http://localhost/sundarta/template
// What index.php sees: http://localhost/sundarta/index.php?route=template

// Handle frontend routes
$route = $_GET['route'] ?? '';

// Default route is home
if (empty($route)) {
    require_once 'frontend/home.php';
    exit;
}

// Map frontend routes to their respective files
$frontendRoutes = [
    'template' => 'frontend/template.php',
    'home' => 'frontend/home.php',
    'products' => 'frontend/products.php',
    'product' => 'frontend/product-detail.php',
    'services' => 'frontend/services.php',
    'service' => 'frontend/service-detail.php',
    'cart' => 'frontend/cart.php',
    'checkout' => 'frontend/checkout.php',
    'login' => 'frontend/login.php',
    'register' => 'frontend/register.php',
    'profile' => 'frontend/profile.php',
    'orders' => 'frontend/orders.php',
    'admin' => 'frontend/admin/index.php',
    'admin/products' => 'frontend/admin/products.php',
    'admin/services' => 'frontend/admin/services.php',
    'admin/categories' => 'frontend/admin/categories.php',
    'admin/orders' => 'frontend/admin/orders.php',
    'admin/users' => 'frontend/admin/users.php',
    'seller' => 'frontend/seller/index.php',
    'seller/services' => 'frontend/seller/services.php',
];

// Check if the route exists
if (isset($frontendRoutes[$route]) && file_exists($frontendRoutes[$route])) {
    require_once $frontendRoutes[$route];
    exit;
}

// If route not found, show 404 page
require_once 'frontend/404.php';
?>

