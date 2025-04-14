<?php
/**
 * Product Detail Page
 * 
 * This page displays detailed information about a specific product.
 * It integrates with the following API endpoints:
 * - GET /api/products/detail - Get product details
 * - GET /api/reviews/product - Get product reviews
 * - POST /api/reviews/product - Add product review (authenticated users only)
 * - POST /api/cart/item - Add to cart
 * 
 * Required JS Modules:
 * - modules/product-detail.js - Handles product detail view
 * - modules/reviews.js - Handles review display and submission
 * - modules/cart.js - Handles add to cart functionality
 * - utils/gallery.js - Handles product image gallery
 */

require_once __DIR__ . '/../backend/helpers/auth.php';
require 'partials/header.php';

// Get product ID from URL parameter
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Add user-authenticated class to body if user is logged in
if (isLoggedIn()) {
    echo '<script>document.body.classList.add("user-authenticated");</script>';
}
?>

<div class="container mx-auto py-8">
    <!-- Product Details Section -->
    <div id="product-detail" class="mb-12">
        <!-- Will be populated by JS -->
    </div>

    <!-- Reviews Section -->
    <div id="reviews-section" class="mt-12">
        <!-- Will be populated by JS -->
    </div>
    
    <!-- Alerts Container -->
    <div class="alerts-container hidden"></div>
</div>

<!-- Include JavaScript Files -->
<script type="module" src="/assets/js/product-detail-page.js"></script>

<?php
require 'partials/footer.php';
?> 