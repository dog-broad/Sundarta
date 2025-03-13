<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PermissionModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class PermissionController extends BaseController {
    private $permissionModel;

    public function __construct() {
        $this->permissionModel = new PermissionModel();
    }

    /**
     * Get all permissions
     */
    public function getAll() {
        $this->ensureMethodAllowed('GET');
        requirePermission('view_permissions');

        $permissions = $this->permissionModel->getAll();
        $this->sendSuccess($permissions, 'Permissions retrieved successfully');
    }

    /**
     * Get a permission by ID
     */
    public function getById() {
        $this->ensureMethodAllowed('GET');
        requirePermission('view_permissions');

        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('Permission ID is required', 400);
        }

        $permission = $this->permissionModel->getById($id);
        if (!$permission) {
            $this->sendError('Permission not found', 404);
        }

        $this->sendSuccess($permission, 'Permission retrieved successfully');
    }

    /**
     * Create a new permission
     */
    public function create() {
        $this->ensureMethodAllowed('POST');
        requirePermission('manage_permissions');

        $data = $this->getJsonData();

        // Validate required fields
        $requiredFields = ['name', 'description'];
        $missingFields = $this->validateRequired($data, $requiredFields);

        if (!empty($missingFields)) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $missingFields
            ]);
        }

        // Check if permission already exists
        if ($this->permissionModel->findByName($data['name'])) {
            $this->sendError('Permission already exists', 409);
        }

        // Create permission
        $permissionId = $this->permissionModel->create(
            $data['name'],
            $data['description']
        );

        if (!$permissionId) {
            $this->sendError('Failed to create permission', 500);
        }

        $permission = $this->permissionModel->getById($permissionId);
        $this->sendSuccess($permission, 'Permission created successfully', 201);
    }

    /**
     * Update a permission
     */
    public function update() {
        $this->ensureMethodAllowed('PUT');
        requirePermission('manage_permissions');

        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('Permission ID is required', 400);
        }

        $permission = $this->permissionModel->getById($id);
        if (!$permission) {
            $this->sendError('Permission not found', 404);
        }

        $data = $this->getJsonData();

        // Update permission
        $success = $this->permissionModel->update($id, [
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null
        ]);

        if (!$success) {
            $this->sendError('Failed to update permission', 500);
        }

        $updatedPermission = $this->permissionModel->getById($id);
        $this->sendSuccess($updatedPermission, 'Permission updated successfully');
    }

    /**
     * Delete a permission
     */
    public function delete() {
        $this->ensureMethodAllowed('DELETE');
        requirePermission('manage_permissions');

        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('Permission ID is required', 400);
        }

        $permission = $this->permissionModel->getById($id);
        if (!$permission) {
            $this->sendError('Permission not found', 404);
        }

        $success = $this->permissionModel->delete($id);
        if (!$success) {
            $this->sendError('Failed to delete permission', 500);
        }

        $this->sendSuccess(null, 'Permission deleted successfully');
    }

    /**
     * Get roles with a specific permission
     */
    public function getRoles() {
        $this->ensureMethodAllowed('GET');
        requirePermission('view_permissions');

        $id = $this->getQueryParam('permission_id');
        if (!$id) {
            $this->sendError('Permission ID is required', 400);
        }

        $permission = $this->permissionModel->getById($id);
        if (!$permission) {
            $this->sendError('Permission not found', 404);
        }

        // Get roles with this permission
        $roles = $this->permissionModel->getRolesByPermissionId($id);

        $this->sendSuccess($roles, 'Roles retrieved successfully');
    }

    /**
     * Check if a user has a specific permission
     */
    public function checkUserPermission() {
        $this->ensureMethodAllowed('GET');
        requireLogin();

        $permissionName = $this->getQueryParam('permission');
        if (!$permissionName) {
            $this->sendError('Permission name is required', 400);
        }

        $userId = $this->getQueryParam('user_id') ?? getCurrentUserId();
        
        // Only admins can check permissions for other users
        if ($userId != getCurrentUserId()) {
            requirePermission('view_users');
        }

        $hasPermission = $this->permissionModel->userHasPermission($userId, $permissionName);
        $this->sendSuccess(['has_permission' => $hasPermission], 'Permission check completed');
    }

    /**
     * Get all permissions for a user
     */
    public function getUserPermissions() {
        $this->ensureMethodAllowed('GET');
        requireLogin();

        $userId = $this->getQueryParam('user_id') ?? getCurrentUserId();
        
        // Only admins can view permissions for other users
        if ($userId != getCurrentUserId()) {
            requirePermission('view_users');
        }

        $permissions = $this->permissionModel->getByUserId($userId);
        $this->sendSuccess($permissions, 'User permissions retrieved successfully');
    }
} 