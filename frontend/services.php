<?php
/**
 * Services Page
 * 
 * This page displays all available services with filtering and search capabilities.
 * It integrates with the following API endpoints:
 * - GET /api/services - List all services with pagination
 * - GET /api/services/search - Search services
 * - GET /api/services/category - Filter services by category
 * - GET /api/categories - Get all categories for filtering
 * 
 * Required JS Modules:
 * - modules/services.js - Handles service listing and filtering
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
    <h1 class="font-heading text-3xl mb-6 text-center">Our Services</h1>
    
    <!-- Service Filters Section -->
    <div id="filters-container" class="mb-8">
        <!-- Filters will be populated by JS -->
    </div>

    <!-- Services Grid -->
    <div id="services-grid" class="mb-8">
        <!-- Services will be populated by JS -->
    </div>

    <!-- Pagination -->
    <div id="pagination" class="my-8">
        <!-- Pagination will be handled by JS -->
    </div>
    
    <!-- Alerts Container -->
    <div class="alerts-container hidden"></div>
</div>

<!-- Include JavaScript Files -->
<script type="module" src="/sundarta/assets/js/services-page.js"></script>

<?php
require 'partials/footer.php';
?> 