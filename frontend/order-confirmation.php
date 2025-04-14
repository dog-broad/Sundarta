<?php
/**
 * Order Confirmation Page
 * 
 * This page displays the order confirmation after successful checkout.
 * It integrates with the following API endpoints:
 * - GET /api/orders/detail - Get order details
 * 
 * Required JS Modules:
 * - order-confirmation-page.js - Handles order confirmation
 */

require_once __DIR__ . '/../backend/helpers/auth.php';

// Redirect to login if user is not authenticated
if (!isAuthenticated()) {
    header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Check for order ID
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    header('Location: /orders');
    exit;
}

// Now we can safely include header and output content
require 'partials/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <div class="inline-block bg-green-100 text-green-700 p-4 rounded-full mb-4">
            <i class="fas fa-check-circle text-5xl"></i>
        </div>
        <h1 class="font-heading text-4xl mb-2">Order Placed Successfully!</h1>
        <p class="text-text-light">Thank you for your order. We will process it soon.</p>
        <div class="mt-4 text-lg">
            <span class="font-medium">Order ID:</span> #<span id="order-id"><?php echo $order_id; ?></span>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-5xl mx-auto">
        <!-- Order Details -->
        <div class="card p-6">
            <h2 class="font-heading text-xl mb-4">Order Details</h2>
            <div id="order-details">
                <!-- Order details will be loaded dynamically -->
                <div class="loading-spinner">
                    <div class="spinner-container">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Shipping Information -->
        <div class="card p-6">
            <h2 class="font-heading text-xl mb-4">Shipping Information</h2>
            <div id="shipping-info">
                <!-- Shipping info will be loaded dynamically -->
                <div class="loading-spinner">
                    <div class="spinner-container">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-8">
        <a href="/products" class="btn btn-primary">Continue Shopping</a>
        <a href="/orders" class="btn btn-outline ml-4">View All Orders</a>
    </div>
</div>

<!-- Include JavaScript Files -->
<script type="module" src="/assets/js/order-confirmation-page.js"></script>

<?php
require 'partials/footer.php';
?> 