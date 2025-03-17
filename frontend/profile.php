<?php
require_once __DIR__ . '/../backend/helpers/auth.php';
require 'partials/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: /sundarta/login?redirect=/sundarta/profile');
    exit;
}
?>

<div class="container mx-auto py-8 px-4">
    <h1 class="font-heading text-3xl mb-8 text-center">My Profile</h1>
    
    <div class="max-w-4xl mx-auto">
        <!-- Profile Tabs -->
        <div class="mb-8">
            <div class="flex border-b overflow-x-auto">
                <!-- Tab buttons with accessible titles/labels -->
                <button 
                    title="View Profile Information"
                    type="button"
                    class="tab-btn active py-2 px-4 font-medium" 
                    data-tab="profile-info" 
                    aria-label="View Profile Information">
                    Profile Information
                </button>
                <button 
                    title="Change Password"
                    type="button"
                    class="tab-btn py-2 px-4 font-medium" 
                    data-tab="change-password" 
                    aria-label="Change Password">
                    Change Password
                </button>
                <button 
                    title="View My Orders"
                    type="button"
                    class="tab-btn py-2 px-4 font-medium" 
                    data-tab="orders" 
                    aria-label="View My Orders">
                    My Orders
                </button>
            </div>
        </div>
        
        <!-- Alert Container -->
        <div class="alerts-container mb-6 hidden"></div>
        
        <!-- Profile Information Tab -->
        <div class="tab-content active" id="profile-info">
            <div class="card p-6">
                <form id="profile-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 flex flex-col items-center mb-4">
                        <div class="relative mb-4">
                            <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 mb-2">
                                <img id="avatar-preview" src="" alt="Profile Avatar" class="w-full h-full object-cover">
                            </div>
                            <label for="avatar-upload" class="absolute bottom-0 right-0 bg-primary text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="username" class="input-label">Username</label>
                        <input type="text" id="username" name="username" class="input-text" placeholder="Your username">
                    </div>
                    
                    <div class="input-group">
                        <label for="email" class="input-label">Email Address</label>
                        <input type="email" id="email" name="email" class="input-text" placeholder="Your email address">
                    </div>
                    
                    <div class="input-group">
                        <label for="phone" class="input-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="input-text" placeholder="Your phone number">
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">Account Type</label>
                        <div id="user-roles" class="py-2 text-text-light"></div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <button type="submit" class="btn btn-primary w-full md:w-auto">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Change Password Tab -->
        <div class="tab-content hidden" id="change-password">
            <div class="card p-6">
                <form id="password-form" class="grid grid-cols-1 gap-6">
                    <div class="input-group">
                        <label for="current-password" class="input-label">Current Password</label>
                        <input type="password" id="current-password" name="current_password" class="input-text" placeholder="Enter your current password">
                    </div>
                    
                    <div class="input-group">
                        <label for="new-password" class="input-label">New Password</label>
                        <input type="password" id="new-password" name="new_password" class="input-text" placeholder="Enter your new password">
                    </div>
                    
                    <div class="input-group">
                        <label for="confirm-password" class="input-label">Confirm New Password</label>
                        <input type="password" id="confirm-password" name="confirm_password" class="input-text" placeholder="Confirm your new password">
                    </div>
                    
                    <div>
                        <button type="submit" class="btn btn-primary w-full md:w-auto">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Orders Tab -->
        <div class="tab-content hidden" id="orders">
            <div class="card p-6">
                <div id="orders-container">
                    <div class="text-center py-8">
                        <div class="loading-spinner">
                            <div class="spinner-container">
                                <div class="spinner"></div>
                            </div>
                        </div>
                        <p class="mt-4 text-text-light">Loading your orders...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import ProfileModule from '/sundarta/assets/js/modules/profile.js';
    
    // Initialize profile module
    document.addEventListener('DOMContentLoaded', () => {
        ProfileModule.init();
    });
</script>

<?php
require 'partials/footer.php';
?> 