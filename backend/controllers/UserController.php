<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class UserController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    /**
     * Register a new user
     */
    public function register() {
        $this->ensureMethodAllowed('POST');
        
        $data = $this->getJsonData();
        
        // Validate required fields
        $requiredFields = ['username', 'email', 'phone', 'password'];
        $missingFields = $this->validateRequired($data, $requiredFields);
        
        if (!empty($missingFields)) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $missingFields
            ]);
        }
        
        // Validate email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->sendError('Invalid email address', 400);
        }
        
        // Check if email already exists
        if ($this->userModel->findByEmail($data['email'])) {
            $this->sendError('Email already exists', 409);
        }
        
        // Check if username already exists
        if ($this->userModel->findByUsername($data['username'])) {
            $this->sendError('Username already exists', 409);
        }
        
        // Create user
        $userId = $this->userModel->create(
            $data['username'],
            $data['email'],
            $data['phone'],
            $data['password'],
            $data['role'] ?? 'customer'
        );
        
        if (!$userId) {
            $this->sendError('Failed to create user', 500);
        }
        
        $user = $this->userModel->getById($userId);
        unset($user['password']); // Remove password from response
        
        $this->sendSuccess($user, 'User registered successfully', 201);
    }

    /**
     * Login a user
     */
    public function login() {
        $this->ensureMethodAllowed('POST');
        
        $data = $this->getJsonData();
        
        // Validate required fields
        $requiredFields = ['email', 'password'];
        $missingFields = $this->validateRequired($data, $requiredFields);
        
        if (!empty($missingFields)) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $missingFields
            ]);
        }
        
        // Find user by email
        $user = $this->userModel->findByEmail($data['email']);
        
        if (!$user) {
            $this->sendError('Invalid credentials', 401);
        }
        
        // Verify password
        if (!password_verify($data['password'], $user['password'])) {
            $this->sendError('Invalid credentials', 401);
        }
        
        // Login user
        login($user['id'], $user['username'], $user['role']);
        
        unset($user['password']); // Remove password from response
        
        $this->sendSuccess($user, 'Login successful');
    }

    /**
     * Logout a user
     */
    public function logout() {
        $this->ensureMethodAllowed('POST');
        
        logout();
        
        $this->sendSuccess([], 'Logout successful');
    }

    /**
     * Get user profile
     */
    public function getProfile() {
        $this->ensureMethodAllowed('GET');
        
        requireLogin();
        
        $userId = getCurrentUserId();
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            $this->sendError('User not found', 404);
        }
        
        unset($user['password']); // Remove password from response
        
        $this->sendSuccess($user, 'Profile retrieved successfully');
    }

    /**
     * Update user profile
     */
    public function updateProfile() {
        $this->ensureMethodAllowed('PUT');
        
        requireLogin();
        
        $userId = getCurrentUserId();
        $data = $this->getJsonData();
        
        // Update profile
        $success = $this->userModel->updateProfile($userId, $data);
        
        if (!$success) {
            $this->sendError('Failed to update profile', 500);
        }
        
        $user = $this->userModel->getById($userId);
        unset($user['password']); // Remove password from response
        
        $this->sendSuccess($user, 'Profile updated successfully');
    }

    /**
     * Update user password
     */
    public function updatePassword() {
        $this->ensureMethodAllowed('PUT');
        
        requireLogin();
        
        $userId = getCurrentUserId();
        $data = $this->getJsonData();
        
        // Validate required fields
        $requiredFields = ['current_password', 'new_password'];
        $missingFields = $this->validateRequired($data, $requiredFields);
        
        if (!empty($missingFields)) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $missingFields
            ]);
        }
        
        // Verify current password
        if (!$this->userModel->verifyPassword($userId, $data['current_password'])) {
            $this->sendError('Current password is incorrect', 401);
        }
        
        // Update password
        $success = $this->userModel->updatePassword($userId, $data['new_password']);
        
        if (!$success) {
            $this->sendError('Failed to update password', 500);
        }
        
        $this->sendSuccess([], 'Password updated successfully');
    }

    /**
     * Get all users (admin only)
     */
    public function getAllUsers() {
        $this->ensureMethodAllowed('GET');
        
        requireAdmin();
        
        $pagination = $this->getPaginationParams();
        $page = $pagination['page'];
        $perPage = $pagination['per_page'];
        
        $role = $this->getQueryParam('role');
        
        $offset = ($page - 1) * $perPage;
        
        if ($role) {
            $users = $this->userModel->getByRole($role);
        } else {
            $users = $this->userModel->getAll('id', 'DESC', $perPage, $offset);
        }
        
        // Remove passwords from response
        foreach ($users as &$user) {
            unset($user['password']);
        }
        
        $totalUsers = $this->userModel->count($role ? "role = '{$role}'" : '');
        $totalPages = ceil($totalUsers / $perPage);
        
        $this->sendSuccess([
            'users' => $users,
            'pagination' => [
                'total' => $totalUsers,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $totalPages,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $totalUsers)
            ]
        ], 'Users retrieved successfully');
    }
} 