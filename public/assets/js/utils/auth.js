/**
 * Authentication Utility Module
 * 
 * This module handles client-side authentication state and operations.
 * It works in conjunction with the PHP auth helper to maintain session state.
 * 
 * API Endpoints Used:
 * - POST /api/users/login
 * - POST /api/users/logout
 * - GET /api/users/profile
 * - GET /api/permissions/check
 */

import API from './api.js';
import UI from './ui.js';

const Auth = {
    /**
     * Current user state
     */
    user: null,
    
    /**
     * Initialize auth state
     * Checks if user is logged in and loads profile
     */
    init: async () => {
        try {
            // Try to get user profile
            const response = await API.get('/users/profile');
            
            if (response.success) {
                Auth.user = response.data;
                return true;
            }
        } catch (error) {
            // User is not logged in or session expired
            Auth.user = null;
        }
        
        return false;
    },

    /**
     * Login user
     * @param {string} email 
     * @param {string} password 
     * @returns {Promise}
     */
    login: async (email, password) => {
        try {
            const response = await API.post('/users/login', {
                email,
                password
            });
            
            if (response.success) {
                Auth.user = response.data;
                return {
                    success: true,
                    user: response.data
                };
            }
            
            return {
                success: false,
                message: response.message || 'Login failed'
            };
        } catch (error) {
            return {
                success: false,
                message: error.message || 'Login failed. Please try again.'
            };
        }
    },

    /**
     * Logout user
     * @returns {Promise}
     */
    logout: async () => {
        try {
            const response = await API.post('/users/logout');
            
            // Clear user state regardless of response
            Auth.user = null;
            
            return {
                success: true
            };
        } catch (error) {
            console.error('Logout error:', error);
            
            // Still clear user state
            Auth.user = null;
            
            return {
                success: false,
                message: error.message || 'Logout failed. Please try again.'
            };
        }
    },

    /**
     * Check if user has specific permission
     * @param {string} permission 
     * @returns {Promise<boolean>}
     */
    hasPermission: async (permission) => {
        try {
            const response = await API.get('/permissions/check', {
                permission
            });
            
            return response.success && response.data.hasPermission;
        } catch (error) {
            console.error('Permission check error:', error);
            return false;
        }
    },

    /**
     * Check if user has specific role
     * @param {string} role 
     * @returns {boolean}
     */
    hasRole: (role) => {
        if (!Auth.user || !Auth.user.roles) {
            return false;
        }
        
        return Auth.user.roles.some(r => 
            r.name === role || r.id === role
        );
    },

    /**
     * Get current user profile
     * @returns {Promise<Object>}
     */
    getProfile: async () => {
        try {
            const response = await API.get('/users/profile');
            
            if (response.success) {
                Auth.user = response.data;
                return Auth.user;
            }
            
            throw new Error(response.message || 'Failed to get profile');
        } catch (error) {
            console.error('Get profile error:', error);
            throw error;
        }
    },

    /**
     * Update user profile
     * @param {Object} data 
     * @returns {Promise}
     */
    updateProfile: async (data) => {
        try {
            const response = await API.put('/users/profile', data);
            
            if (response.success) {
                // Update local user state
                Auth.user = {
                    ...Auth.user,
                    ...data
                };
                
                return {
                    success: true,
                    user: Auth.user
                };
            }
            
            return {
                success: false,
                message: response.message || 'Profile update failed'
            };
        } catch (error) {
            return {
                success: false,
                message: error.message || 'Profile update failed. Please try again.'
            };
        }
    },

    /**
     * Update user password
     * @param {string} currentPassword 
     * @param {string} newPassword 
     * @returns {Promise}
     */
    updatePassword: async (currentPassword, newPassword) => {
        try {
            const response = await API.put('/users/password', {
                current_password: currentPassword,
                new_password: newPassword
            });
            
            return {
                success: response.success,
                message: response.message
            };
        } catch (error) {
            return {
                success: false,
                message: error.message || 'Password update failed. Please try again.'
            };
        }
    },

    /**
     * Register new user
     * @param {Object} userData 
     * @returns {Promise}
     */
    register: async (userData) => {
        try {
            const response = await API.post('/users/register', userData);
            
            if (response.success) {
                return {
                    success: true,
                    user: response.data
                };
            }
            
            return {
                success: false,
                message: response.message || 'Registration failed'
            };
        } catch (error) {
            return {
                success: false,
                message: error.message || 'Registration failed. Please try again.'
            };
        }
    },

    /**
     * Check if user is logged in
     * @returns {boolean}
     */
    isLoggedIn: () => {
        return Auth.user !== null;
    }
};

export default Auth; 