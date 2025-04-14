/**
 * Profile Module
 * 
 * This module handles the user profile page functionality:
 * - Loading and displaying user profile information
 * - Updating profile information
 * - Changing password
 * - Displaying order history
 */

import Auth from '../utils/auth.js';
import Validation from '../utils/validation.js';
import UI from '../utils/ui.js';
import API from '../utils/api.js';

const ProfileModule = {
    /**
     * Initialize profile tabs
     */
    initTabs: () => {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove all classes from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('hidden'));
                
                // Add active class to clicked button
                button.classList.add('active');
                
                // Show corresponding content
                const tabId = button.getAttribute('data-tab');
                const tabContent = document.getElementById(tabId);
                if (tabContent) {
                    tabContent.classList.add('active');
                    
                    // Load orders if orders tab is selected
                    if (tabId === 'orders') {
                        ProfileModule.loadOrders();
                    }
                }
            });
        });
    },
    
    /**
     * Load user profile data
     */
    loadProfile: async () => {
        try {
            const profile = await Auth.getProfile();
            
            // Populate form fields
            const usernameInput = document.getElementById('username');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const avatarPreview = document.getElementById('avatar-preview');
            const userRoles = document.getElementById('user-roles');
            
            if (usernameInput) usernameInput.value = profile.username || '';
            if (emailInput) emailInput.value = profile.email || '';
            if (phoneInput) phoneInput.value = profile.phone || '';
            
            // Set avatar preview
            if (avatarPreview) {
                avatarPreview.src = profile.avatar || 'https://www.gravatar.com/avatar/?d=mp';
            }
            
            // Display user roles
            if (userRoles && profile.roles) {
                userRoles.textContent = profile.roles.map(role => role.name).join(', ');
            }
        } catch (error) {
            UI.showError('Failed to load profile. Please try again.');
            console.error('Load profile error:', error);
        }
    },
    
    /**
     * Initialize profile form
     */
    initProfileForm: () => {
        const form = document.getElementById('profile-form');
        
        if (!form) return;
        
        // Handle avatar upload preview
        const avatarUpload = document.getElementById('avatar-upload');
        const avatarPreview = document.getElementById('avatar-preview');
        
        if (avatarUpload && avatarPreview) {
            avatarUpload.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        avatarPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Handle form submission
        UI.handleFormSubmit(form, async (form) => {
            // Clear validation errors
            Validation.clearValidationErrors(form);
            
            // Get form data
            const formData = new FormData(form);
            const userData = {
                username: formData.get('username'),
                email: formData.get('email'),
                phone: formData.get('phone')
            };
            
            // Handle avatar upload
            const avatarFile = formData.get('avatar');
            if (avatarFile && avatarFile.size > 0) {
                // In a real implementation, you would upload the file to a server
                // and get back a URL. For now, we'll just use a placeholder.
                userData.avatar = avatarPreview.src;
            }
            
            // Validate form
            const validationResult = Validation.validateForm(form, {
                username: {
                    required: true,
                    label: 'Username',
                    validator: Validation.isValidUsername,
                    message: 'Username must be 3-20 characters and can only contain letters, numbers, underscores, and hyphens'
                },
                email: {
                    required: true,
                    label: 'Email',
                    validator: Validation.isValidEmail,
                    message: 'Please enter a valid email address'
                },
                phone: {
                    required: true,
                    label: 'Phone',
                    validator: Validation.isValidPhone,
                    message: 'Please enter a valid phone number'
                }
            });
            
            if (!validationResult.isValid) {
                Validation.showValidationErrors(form, validationResult.errors);
                return;
            }
            
            // Submit update request
            const result = await Auth.updateProfile(userData);
            
            if (result.success) {
                // Show success message
                UI.showAlert('Profile updated successfully!', 'success');
                
                // Scroll to top of form
                form.scrollIntoView({ behavior: 'smooth' });
            } else {
                // Show error message
                alertContainer.className = 'alert alert-error';
                alertContainer.textContent = result.message;
                alertContainer.classList.remove('hidden');
            }
        });
    },
    
    /**
     * Initialize password form
     */
    initPasswordForm: () => {
        const form = document.getElementById('password-form');
        
        if (!form) return;
        
        UI.handleFormSubmit(form, async (form) => {
            // Clear previous errors
            alertContainer.classList.add('hidden');
            alertContainer.textContent = '';
            Validation.clearValidationErrors(form);
            
            // Get form data
            const formData = new FormData(form);
            const currentPassword = formData.get('current_password');
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('confirm_password');
            
            // Validate form
            const validationResult = Validation.validateForm(form, {
                current_password: {
                    required: true,
                    label: 'Current Password'
                },
                new_password: {
                    required: true,
                    label: 'New Password',
                    minLength: 8,
                    validator: Validation.isStrongPassword,
                    message: 'Password must be at least 8 characters and include uppercase, lowercase, and numbers'
                },
                confirm_password: {
                    required: true,
                    label: 'Confirm Password',
                    match: 'new_password',
                    matchMessage: 'Passwords do not match'
                }
            });
            
            if (!validationResult.isValid) {
                Validation.showValidationErrors(form, validationResult.errors);
                return;
            }
            
            // Submit update request
            const result = await Auth.updatePassword(currentPassword, newPassword);
            
            if (result.success) {
                // Show success message
                alertContainer.className = 'alert alert-success';
                alertContainer.textContent = 'Password updated successfully!';
                alertContainer.classList.remove('hidden');
                
                // Clear form
                form.reset();
                
                // Scroll to top of form
                form.scrollIntoView({ behavior: 'smooth' });
            } else {
                // Show error message
                alertContainer.className = 'alert alert-error';
                alertContainer.textContent = result.message;
                alertContainer.classList.remove('hidden');
            }
        });
    },
    
    /**
     * Load user orders
     */
    loadOrders: async () => {
        const ordersContainer = document.getElementById('orders-container');
        
        if (!ordersContainer) return;
        
        try {
            // Show loading state
            ordersContainer.innerHTML = `
                <div class="text-center py-8">
                    <div class="loading-spinner">
                        <div class="spinner-container">
                            <div class="spinner"></div>
                        </div>
                    </div>
                    <p class="mt-4 text-text-light">Loading your orders...</p>
                </div>
            `;
            
            // Fetch orders
            const response = await API.get('/orders/my-orders');
            
            if (response.success && response.data.length > 0) {
                // Render orders
                ordersContainer.innerHTML = `
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 px-4 text-left">Order ID</th>
                                    <th class="py-2 px-4 text-left">Date</th>
                                    <th class="py-2 px-4 text-left">Total</th>
                                    <th class="py-2 px-4 text-left">Status</th>
                                    <th class="py-2 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${response.data.map(order => `
                                    <tr class="border-b">
                                        <td class="py-2 px-4">#${order.id}</td>
                                        <td class="py-2 px-4">${new Date(order.created_at).toLocaleDateString()}</td>
                                        <td class="py-2 px-4">₹${
                                            order.items.reduce((sum, item) => sum + parseFloat(item.total_price), 0).toFixed(2)
                                        }</td>
                                        <td class="py-2 px-4">
                                            <span class="badge badge-${ProfileModule.getStatusBadgeClass(order.status)}">
                                                ${order.status}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4">
                                            <a href="/order/${order.id}" class="text-primary hover:underline">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                // No orders found
                ordersContainer.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-bag text-4xl text-text-light mb-4"></i>
                        <p class="text-text-light">You haven't placed any orders yet.</p>
                        <a href="/products" class="btn btn-primary mt-4">Start Shopping</a>
                    </div>
                `;
            }
        } catch (error) {
            // Show error message
            ordersContainer.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-4"></i>
                    <p class="text-text-light">Failed to load orders. Please try again.</p>
                    <button id="retry-orders" class="btn btn-primary mt-4">Retry</button>
                </div>
            `;
            
            // Add retry button functionality
            const retryButton = document.getElementById('retry-orders');
            if (retryButton) {
                retryButton.addEventListener('click', ProfileModule.loadOrders);
            }
            
            console.error('Load orders error:', error);
        }
    },
    
    /**
     * Get badge class based on order status
     * @param {string} status 
     * @returns {string} Badge class
     */
    getStatusBadgeClass: (status) => {
        switch (status.toLowerCase()) {
            case 'completed':
                return 'success';
            case 'processing':
                return 'primary';
            case 'pending':
                return 'warning';
            case 'cancelled':
                return 'error';
            default:
                return 'secondary';
        }
    },
    
    /**
     * Initialize profile module
     */
    init: () => {
        // Initialize tabs
        ProfileModule.initTabs();
        
        // Load profile data
        ProfileModule.loadProfile();
        
        // Initialize profile form
        ProfileModule.initProfileForm();
        
        // Initialize password form
        ProfileModule.initPasswordForm();
    }
};

export default ProfileModule; 