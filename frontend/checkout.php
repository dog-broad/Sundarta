<?php
/**
 * Checkout Page
 * 
 * This page handles the checkout process for products and services.
 * It integrates with the following API endpoints:
 * - GET /api/cart - Get cart contents
 * - POST /api/orders/checkout - Process checkout
 * 
 * Required JS Modules:
 * - modules/cart.js - Handles cart operations
 * - checkout-page.js - Handles the checkout process
 */

require_once __DIR__ . '/../backend/helpers/auth.php';

// Redirect to login if user is not authenticated
if (!isAuthenticated()) {
    header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Now we can safely include header and output content
require 'partials/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="font-heading text-4xl mb-8">Checkout</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <form id="checkout-form" class="space-y-6">
                <!-- Shipping Information -->
                <div class="card p-6">
                    <h2 class="font-heading text-xl mb-4">Shipping Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="input-group">
                            <label for="name" class="input-label">Full Name</label>
                            <input type="text" id="name" name="name" class="input-text" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="email" class="input-label">Email</label>
                            <input type="email" id="email" name="email" class="input-text" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="phone" class="input-label">Phone</label>
                            <input type="tel" id="phone" name="phone" class="input-text" required>
                        </div>
                        
                        <div class="input-group md:col-span-2">
                            <label for="address" class="input-label">Address</label>
                            <textarea id="address" name="address" class="input-text" rows="3" required></textarea>
                        </div>
                        
                        <div class="input-group">
                            <label for="city" class="input-label">City</label>
                            <input type="text" id="city" name="city" class="input-text" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="state" class="input-label">State</label>
                            <input type="text" id="state" name="state" class="input-text" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="pincode" class="input-label">Pincode</label>
                            <input type="text" id="pincode" name="pincode" class="input-text" required>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="card p-6">
                    <h2 class="font-heading text-xl mb-4">Payment Method</h2>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="radio" name="payment_method" id="cod" value="cod" class="mr-2" checked>
                            <label for="cod">
                                <span class="font-medium">Cash on Delivery</span>
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="radio" name="payment_method" id="online" value="online" class="mr-2">
                            <label for="online">
                                <span class="font-medium">Online Payment</span>
                                <span class="text-sm text-text-light ml-2">(Coming Soon)</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-full py-3">Place Order</button>
                
                <div class="text-center mt-4">
                    <a href="/orders" class="text-sm text-primary hover:underline">
                        <i class="fas fa-history mr-1"></i> View Order History
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Order Summary -->
        <div>
            <div class="card p-6 sticky top-4" id="order-summary">
                <!-- Order summary will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Include JavaScript Files -->
<script type="module" src="/assets/js/checkout-page.js"></script>

<?php
require 'partials/footer.php';
?> 