<?php
/**
 * Service Detail Page
 * 
 * This page displays detailed information about a specific service.
 * It integrates with the following API endpoints:
 * - GET /api/services/detail - Get service details
 * - GET /api/reviews/service - Get service reviews
 * - POST /api/reviews/service - Add service review (authenticated users only)
 * - POST /api/appointments/create - Book a service appointment
 * 
 * Required JS Modules:
 * - modules/service-detail.js - Handles service detail view
 * - modules/reviews.js - Handles review display and submission
 * - utils/ui.js - Handles UI elements and modals
 */

require_once __DIR__ . '/../backend/helpers/auth.php';
require 'partials/header.php';

// Get service ID from URL parameter
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Add user-authenticated class to body if user is logged in
if (isLoggedIn()) {
    echo '<script>document.body.classList.add("user-authenticated");</script>';
}
?>

<div class="container mx-auto py-8">
    <!-- Service Details Section -->
    <div id="service-detail" class="mb-12">
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
<script type="module" src="/assets/js/service-detail-page.js"></script>

<?php
require 'partials/footer.php';
?> 