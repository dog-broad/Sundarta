<?php
/**
 * Products Page
 * 
 * This page displays the product catalog with filtering, sorting, and search capabilities.
 * It integrates with the following API endpoints:
 * - GET /api/products - List all products with pagination
 * - GET /api/products/search - Search products
 * - GET /api/products/category - Filter products by category
 * - GET /api/categories - Get all categories for filtering
 * 
 * Required JS Modules:
 * - modules/products.js - Handles product listing and filtering
 * - modules/cart.js - Handles add to cart functionality
 * - utils/pagination.js - Handles pagination
 * - utils/filters.js - Handles filter UI and state
 */

require_once __DIR__ . '/../backend/helpers/auth.php';
require 'partials/header.php';

// Add user-authenticated class to body if user is logged in
if (isLoggedIn()) {
    echo '<script>document.body.classList.add("user-authenticated");</script>';
}
?>

<div class="container mx-auto py-8">
    <h1 class="font-heading text-3xl mb-6 text-center">Our Products</h1>
    
    <!-- Product Filters Section -->
    <div id="filters-container" class="mb-8">
        <!-- Filters will be populated by JS -->
    </div>

    <!-- Products Grid -->
    <div id="products-grid" class="mb-8">
        <!-- Products will be populated by JS -->
    </div>

    <!-- Pagination -->
    <div id="pagination" class="my-8">
        <!-- Pagination will be handled by JS -->
    </div>
    
    <!-- Alerts Container -->
    <div class="alerts-container hidden"></div>
</div>

<!-- Include JavaScript Files -->
<script type="module" src="/assets/js/products-page.js"></script>

<?php
require 'partials/footer.php';
?> 