<?php
require_once 'vendor/autoload.php';
// Looking for .env at the root directory
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once 'backend/config/db.php';
require_once 'backend/helpers/auth.php';


$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/sundarta';

switch ($request_uri) {
    case $base_path . '/':
        require 'frontend/home.php';
        break;
    case $base_path . '/products':
        require 'frontend/products.php';
        break;
    case $base_path . '/services':
        require 'frontend/services.php';
        break;
    case $base_path . '/cart':
        require 'frontend/cart.php';
        break;
    case $base_path . '/login':
        require 'frontend/login.php';
        break;
    case $base_path . '/register':
        require 'frontend/register.php';
        break;
    case $base_path . '/profile':
        requireLogin();
        require 'frontend/profile.php';
        break;
    default:
        http_response_code(404);
        require 'frontend/404.php';
        break;
}
?>