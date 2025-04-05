<?php
/**
 * Orders Page
 * 
 * This page displays the user's order history.
 * It integrates with the following API endpoints:
 * - GET /api/orders/my-orders - Get user's order history
 * 
 * Required JS Modules:
 * - orders-page.js - Handles displaying and managing orders
 */

require_once __DIR__ . '/../backend/helpers/auth.php';

// Redirect to login if user is not authenticated
if (!isAuthenticated()) {
    header('Location: /sundarta/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Now we can safely include header and output content
require 'partials/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="font-heading text-4xl mb-8">My Orders</h1>
    
    <div class="card p-6">
        <div id="orders-container">
            <!-- Orders will be loaded dynamically -->
            <div class="loading-spinner">
                <div class="spinner-container">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
        
        <!-- Empty state for no orders -->
        <div id="no-orders" class="text-center py-10 hidden">
            <div class="text-7xl text-text-light mb-4">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h3 class="text-xl font-medium mb-2">No orders yet</h3>
            <p class="text-text-light mb-6">You haven't placed any orders yet.</p>
            <a href="/sundarta/products" class="btn btn-primary">Start Shopping</a>
        </div>
    </div>
</div>

<!-- Include JavaScript Files -->
<script type="module" src="/sundarta/assets/js/orders-page.js"></script>

<?php
require 'partials/footer.php';
?> 