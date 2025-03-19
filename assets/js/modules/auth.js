/**
 * Authentication Module
 * 
 * This module handles login and registration form submissions.
 * It integrates with the auth utility for API calls.
 */

import Auth from '../utils/auth.js';
import Validation from '../utils/validation.js';
import UI from '../utils/ui.js';

const AuthModule = {
    /**
     * Initialize login form
     * @param {string} formSelector - CSS selector for login form
     * @param {string} errorSelector - CSS selector for error message container
     */
    initLoginForm: (formSelector = '#login-form', errorSelector = '#alerts-conatiner') => {
        const form = document.querySelector(formSelector);
        
        if (!form) return;
        
        UI.handleFormSubmit(form, async (form) => {
            // Clear previous errors
            Validation.clearValidationErrors(form);
            
            // Get form data
            const formData = new FormData(form);
            const email = formData.get('email');
            const password = formData.get('password');
            
            // Validate form
            const validationResult = Validation.validateForm(form, {
                email: {
                    required: true,
                    label: 'Email',
                    validator: Validation.isValidEmail,
                    message: 'Please enter a valid email address'
                },
                password: {
                    required: true,
                    label: 'Password',
                    minLength: 8
                }
            });
            
            if (!validationResult.isValid) {
                Validation.showValidationErrors(form, validationResult.errors);
                return;
            }
            
            // Submit login request
            const result = await Auth.login(email, password);
            
            if (result.success) {
                // Redirect to dashboard or previous page
                const redirectUrl = new URLSearchParams(window.location.search).get('redirect') || '/sundarta/';
                window.location.href = redirectUrl;
            } else {
                // Show error message
                UI.showAlert(result.message, 'error');
            }
        });
    },
    
    /**
     * Initialize registration form
     * @param {string} formSelector - CSS selector for registration form
     * @param {string} errorSelector - CSS selector for error message container
     */
    initRegisterForm: (formSelector = '#register-form', errorSelector = '#register-error') => {
        const form = document.querySelector(formSelector);
        
        if (!form) return;
        
        UI.handleFormSubmit(form, async (form) => {
            // Clear previous errors
            Validation.clearValidationErrors(form);
            
            // Get form data
            const formData = new FormData(form);
            const userData = {
                username: formData.get('username'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                password: formData.get('password'),
                roles: [formData.get('role')]
            };
            
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
                },
                password: {
                    required: true,
                    label: 'Password',
                    minLength: 8,
                    validator: Validation.isStrongPassword,
                    message: 'Password must be at least 8 characters and include uppercase, lowercase, and numbers'
                },
                confirm_password: {
                    required: true,
                    label: 'Confirm Password',
                    match: 'password',
                    matchMessage: 'Passwords do not match'
                }
            });
            
            if (!validationResult.isValid) {
                Validation.showValidationErrors(form, validationResult.errors);
                return;
            }
            
            // Submit registration request
            const result = await Auth.register(userData);
            
            if (result.success) {
                // Show success message and redirect to login
                UI.showSuccess('Registration successful! Redirecting to login...');
                
                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = '/sundarta/login';
                }, 2000);
            } else {
                // Show error message
                UI.showAlert(result.message, 'error');
            }
        });
    },
    
    /**
     * Initialize logout functionality
     * @param {string} selector - CSS selector for logout link
     */
    initLogout: (selector = 'a[href="/sundarta/logout"]') => {
        const logoutLinks = document.querySelectorAll(selector);
        
        logoutLinks.forEach(link => {
            link.addEventListener('click', async (e) => {
                e.preventDefault();
                
                // Show confirmation dialog
                UI.showModal({
                    title: 'Confirm Logout',
                    content: 'Are you sure you want to log out?',
                    buttons: [
                        {
                            text: 'Cancel',
                            class: 'btn-secondary'
                        },
                        {
                            text: 'Logout',
                            class: 'btn-primary',
                            callback: async () => {
                                const result = await Auth.logout();
                                
                                if (result.success) {
                                    // Redirect to home page
                                    window.location.href = '/sundarta/';
                                } else {
                                    UI.showError(result.message);
                                }
                            }
                        }
                    ]
                });
            });
        });
    },
    
    /**
     * Initialize all auth functionality
     */
    init: () => {
        // Initialize login form if present
        AuthModule.initLoginForm();
        
        // Initialize registration form if present
        AuthModule.initRegisterForm();
        
        // Initialize logout functionality
        AuthModule.initLogout();
    }
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', AuthModule.init);

export default AuthModule; 