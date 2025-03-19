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
?>

<div class="container mx-auto py-8">
    <!-- Product Filters Section -->
    <div class="filters-container">
        <!-- Category filter will be populated by JS -->
    </div>

    <!-- Products Grid -->
    <div class="products-grid">
        <!-- Products will be populated by JS -->
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <!-- Pagination will be handled by JS -->
    </div>
</div>

<!-- Product Details Modal -->
<!-- <div class="card card-product">
    <img src="https://hips.hearstapps.com/hmg-prod/images/ghk-digital-index-haircolor-449-640a4807297b5.jpg?crop=0.668xw:1.00xh;0.167xw,0&resize=480:*" alt="Product" class="card-product-image">
    <div class="card-product-body">
        <span class='badege badge-primary mb-2 w-35 rounded'>New</span>
        <h3 class="font-heading text-lg mb-1">The General Kit</h3>
        <p class="text-text-light mb-2">A complete kit for your haircare needs</p>
        <div class="flex justify-between items-center">
            <span class="font-bold text-lg">₹1,299.00</span>
            <button class="btn btn-primary">Add to Cart</button>
    </div>
</div> -->



<script type="module">
    import ProductModule from '/sundarta/assets/js/modules/products.js';

    // Initialize the product module
    document.addEventListener('DOMContentLoaded', async () => {
        const response = await ProductModule.getProducts();
        const products = response.products;
        console.log(products);
        

        const container=document.querySelector('.products-grid');
        ProductModule.renderProductsGrid(products,container);
        
    });


</script>

<?php
require 'partials/footer.php';
?> 