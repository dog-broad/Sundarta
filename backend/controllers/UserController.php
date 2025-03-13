<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/PermissionModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class UserController extends BaseController {
    private $userModel;
    private $roleModel;
    private $permissionModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
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
        
        // Get role names if provided, otherwise default to 'customer'
        $roleIds = null;
        if (isset($data['roles']) && is_array($data['roles'])) {
            // If roles are provided as names
            if (is_string($data['roles'][0])) {
                $roleNames = $data['roles'];
                $roleIds = [];
                foreach ($roleNames as $roleName) {
                    $role = $this->roleModel->findByName($roleName);
                    if ($role) {
                        $roleIds[] = $role['id'];
                    }
                }
            } else {
                // If roles are provided as IDs (backward compatibility)
                $roleIds = $data['roles'];
            }
        }
        
        // Create user
        $userId = $this->userModel->create(
            $data['username'],
            $data['email'],
            $data['phone'],
            $data['password'],
            $roleIds,
            $data['avatar'] ?? null
        );
        
        if (!$userId) {
            $this->sendError('Failed to create user', 500);
        }
        
        $user = $this->userModel->getUserWithRoles($userId);
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
            $this->sendError('Email address not found', 401);
        }
        
        // Verify password
        if (!password_verify($data['password'], $user['password'])) {
            $this->sendError('Password is incorrect', 401);
        }
        
        // Check if user is active
        if (isset($user['is_active']) && !$user['is_active']) {
            $this->sendError('Your account is inactive. Please contact support.', 403);
        }
        
        // Get user roles
        $roles = $this->roleModel->getRolesByUserId($user['id']);
        $roleNames = array_column($roles, 'name');
        
        // Get user permissions
        $permissions = $this->permissionModel->getByUserId($user['id']);
        $permissionNames = array_column($permissions, 'name');
        
        // Login user
        login($user['id'], $user['username'], $roleNames, $permissionNames);
        
        // Add roles to user data
        $user['roles'] = $roles;
        
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
        $user = $this->userModel->getUserWithRoles($userId);
        
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
        
        $user = $this->userModel->getUserWithRoles($userId);
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
        
        requirePermission('view_users');
        
        $pagination = $this->getPaginationParams();
        $page = $pagination['page'];
        $perPage = $pagination['per_page'];
        
        $role = $this->getQueryParam('role');
        
        if ($role) {
            $users = $this->userModel->getByRole($role, $page, $perPage);
            $totalUsers = $this->userModel->countByRole($role);
        } else {
            $users = $this->userModel->getAllPaginated($page, $perPage);
            $totalUsers = $this->userModel->countAll();
        }
        
        // Add roles to each user
        foreach ($users as &$user) {
            $user['roles'] = $this->roleModel->getRolesByUserId($user['id']);
            unset($user['password']); // Remove password from response
        }
        
        $totalPages = ceil($totalUsers / $perPage);
        $offset = ($page - 1) * $perPage;
        
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

    /**
     * Get a user by ID (admin only)
     */
    public function getUserById() {
        $this->ensureMethodAllowed('GET');
        
        requirePermission('view_users');
        
        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('User ID is required', 400);
        }
        
        $user = $this->userModel->getUserWithRoles($id);
        if (!$user) {
            $this->sendError('User not found', 404);
        }
        
        unset($user['password']); // Remove password from response
        
        $this->sendSuccess($user, 'User retrieved successfully');
    }

    /**
     * Update a user (admin only)
     */
    public function updateUser() {
        $this->ensureMethodAllowed('PUT');
        
        requirePermission('manage_users');
        
        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('User ID is required', 400);
        }
        
        $user = $this->userModel->getById($id);
        if (!$user) {
            $this->sendError('User not found', 404);
        }
        
        $data = $this->getJsonData();
        
        // Update user profile using admin method
        $success = $this->userModel->updateProfileByAdmin($id, $data);
        
        if (!$success) {
            $this->sendError('Failed to update user', 500);
        }
        
        // Update roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            // If roles are provided as names
            if (!empty($data['roles']) && is_string($data['roles'][0])) {
                $success = $this->roleModel->assignRolesToUserByName($id, $data['roles']);
            } else {
                // If roles are provided as IDs (backward compatibility)
                $success = $this->userModel->assignRoles($id, $data['roles']);
            }
            
            if (!$success) {
                $this->sendError('Failed to assign roles', 500);
            }
        }
        
        $updatedUser = $this->userModel->getUserWithRoles($id);
        unset($updatedUser['password']); // Remove password from response
        
        $this->sendSuccess($updatedUser, 'User updated successfully');
    }

    /**
     * Delete a user (admin only)
     */
    public function deleteUser() {
        $this->ensureMethodAllowed('DELETE');
        
        requirePermission('manage_users');
        
        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('User ID is required', 400);
        }
        
        // Prevent deleting yourself
        if ($id == getCurrentUserId()) {
            $this->sendError('Cannot delete your own account', 403);
        }
        
        $user = $this->userModel->getById($id);
        if (!$user) {
            $this->sendError('User not found', 404);
        }
        
        $success = $this->userModel->delete($id);
        if (!$success) {
            $this->sendError('Failed to delete user', 500);
        }
        
        $this->sendSuccess(null, 'User deleted successfully');
    }

    /**
     * Assign roles to a user (admin only)
     */
    public function assignRoles() {
        $this->ensureMethodAllowed('POST');
        
        requirePermission('assign_roles');
        
        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('User ID is required', 400);
        }
        
        $user = $this->userModel->getById($id);
        if (!$user) {
            $this->sendError('User not found', 404);
        }
        
        $data = $this->getJsonData();
        if (!isset($data['roles']) || !is_array($data['roles'])) {
            $this->sendError('Roles array is required', 400);
        }
        
        // Check if roles are provided as names or IDs
        if (!empty($data['roles']) && is_string($data['roles'][0])) {
            // Roles provided as names
            $success = $this->roleModel->assignRolesToUserByName($id, $data['roles']);
        } else {
            // Roles provided as IDs (backward compatibility)
            $success = $this->userModel->assignRoles($id, $data['roles']);
        }
        
        if (!$success) {
            $this->sendError('Failed to assign roles', 500);
        }
        
        $updatedUser = $this->userModel->getUserWithRoles($id);
        unset($updatedUser['password']); // Remove password from response
        
        $this->sendSuccess($updatedUser, 'Roles assigned successfully');
    }
} 