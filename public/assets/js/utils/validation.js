/**
 * Validation Utility Module
 * 
 * This module provides form validation functions for client-side validation.
 * It includes validators for common input types and a form validation system.
 */

const Validation = {
    /**
     * Validate email format
     * @param {string} email 
     * @returns {boolean}
     */
    isValidEmail: (email) => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    /**
     * Validate password strength
     * @param {string} password 
     * @returns {boolean}
     */
    isStrongPassword: (password) => {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        return passwordRegex.test(password);
    },

    /**
     * Validate phone number format
     * @param {string} phone 
     * @returns {boolean}
     */
    isValidPhone: (phone) => {
        // Basic phone validation - can be customized for specific formats
        const phoneRegex = /^\+?[0-9]{10,15}$/;
        return phoneRegex.test(phone.replace(/[\s()-]/g, ''));
    },

    /**
     * Validate username format
     * @param {string} username 
     * @returns {boolean}
     */
    isValidUsername: (username) => {
        // Alphanumeric, 3-20 characters, can include underscore and hyphen
        const usernameRegex = /^[a-zA-Z0-9_-]{3,20}$/;
        return usernameRegex.test(username);
    },

    /**
     * Check if passwords match
     * @param {string} password 
     * @param {string} confirmPassword 
     * @returns {boolean}
     */
    passwordsMatch: (password, confirmPassword) => {
        return password === confirmPassword;
    },

    /**
     * Validate form inputs
     * @param {HTMLFormElement} form 
     * @param {Object} rules - Validation rules
     * @returns {Object} - Validation result with errors
     */
    validateForm: (form, rules) => {
        const formData = new FormData(form);
        const errors = {};
        let isValid = true;

        // Process each field according to its rules
        for (const [field, fieldRules] of Object.entries(rules)) {
            const value = formData.get(field);

            // Required field validation
            if (fieldRules.required && (!value || value.trim() === '')) {
                errors[field] = `${fieldRules.label || field} is required`;
                isValid = false;
                continue;
            }

            // Skip other validations if field is empty and not required
            if (!value || value.trim() === '') continue;

            // Custom validator function
            if (fieldRules.validator && typeof fieldRules.validator === 'function') {
                if (!fieldRules.validator(value)) {
                    errors[field] = fieldRules.message || `${fieldRules.label || field} is invalid`;
                    isValid = false;
                }
            }

            // Minimum length validation
            if (fieldRules.minLength && value.length < fieldRules.minLength) {
                errors[field] = `${fieldRules.label || field} must be at least ${fieldRules.minLength} characters`;
                isValid = false;
            }

            // Maximum length validation
            if (fieldRules.maxLength && value.length > fieldRules.maxLength) {
                errors[field] = `${fieldRules.label || field} must be at most ${fieldRules.maxLength} characters`;
                isValid = false;
            }

            // Match validation (e.g., password confirmation)
            if (fieldRules.match) {
                const matchValue = formData.get(fieldRules.match);
                if (value !== matchValue) {
                    errors[field] = fieldRules.matchMessage || `${fieldRules.label || field} does not match`;
                    isValid = false;
                }
            }
        }

        return {
            isValid,
            errors
        };
    },

    /**
     * Display validation errors in the form
     * @param {HTMLFormElement} form 
     * @param {Object} errors 
     */
    showValidationErrors: (form, errors) => {
        // Clear previous error messages
        form.querySelectorAll('.validation-error').forEach(el => el.remove());

        // Display new error messages
        for (const [field, message] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                // Add error class to input
                input.classList.add('error');

                // Create error message element
                const errorElement = document.createElement('div');
                errorElement.className = 'validation-error text-red-500 text-sm mt-1';
                errorElement.textContent = message;

                // Insert error message after input
                input.parentNode.insertBefore(errorElement, input.nextSibling);
            }
        }
    },

    /**
     * Clear validation errors from form
     * @param {HTMLFormElement} form 
     */
    clearValidationErrors: (form) => {
        form.querySelectorAll('.validation-error').forEach(el => el.remove());
        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
    }
};

export default Validation; 