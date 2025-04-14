<?php
// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
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

// Include the bootstrap file which has helper functions
require_once __DIR__ . '/../backend/config/bootstrap.php';

// Handle frontend routes
$route = $_GET['route'] ?? '';

// Default route is home
if (empty($route)) {
    require_once '../frontend/home.php';
    exit;
}

// Map frontend routes to their respective files
$frontendRoutes = [
    // Core pages
    'template' => '../frontend/template.php',
    'home' => '../frontend/home.php',
    '404' => '../frontend/404.php',
    
    // Product related
    'products' => '../frontend/products.php',
    'product-detail' => '../frontend/product-detail.php',
    
    // Service related
    'services' => '../frontend/services.php',
    'service-detail' => '../frontend/service-detail.php',
    
    // Cart and checkout
    'cart' => '../frontend/cart.php',
    'checkout' => '../frontend/checkout.php',
    'order-confirmation' => '../frontend/order-confirmation.php',
    'orders' => '../frontend/orders.php',
    
    // User account
    'login' => '../frontend/login.php',
    'logout' => '../frontend/logout.php',
    'register' => '../frontend/register.php',
    'profile' => '../frontend/profile.php',
    
    // Admin section
    'admin' => '../frontend/admin/index.php',
    'admin/products' => '../frontend/admin/products.php',
    'admin/services' => '../frontend/admin/services.php',
    'admin/categories' => '../frontend/admin/categories.php',
    'admin/orders' => '../frontend/admin/orders.php',
    'admin/users' => '../frontend/admin/users.php',
    
    // Static pages
    'about' => '../frontend/about.php',
    'contact' => '../frontend/contact.php'
];

// Check if the route exists
if (isset($frontendRoutes[$route]) && file_exists($frontendRoutes[$route])) {
    require_once $frontendRoutes[$route];
    exit;
}

// If route not found, show 404 page
require_once '../frontend/404.php'; 