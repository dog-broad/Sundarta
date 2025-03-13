<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/PermissionModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class RoleController extends BaseController {
    private $roleModel;
    private $permissionModel;

    public function __construct() {
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
    }

    /**
     * Get all roles
     */
    public function getAll() {
        $this->ensureMethodAllowed('GET');
        requirePermission('view_roles');

        $roles = $this->roleModel->getAll();
        $this->sendSuccess($roles, 'Roles retrieved successfully');
    }

    /**
     * Get a role by ID
     */
    public function getById() {
        $this->ensureMethodAllowed('GET');
        requirePermission('view_roles');

        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('Role ID is required', 400);
        }

        $role = $this->roleModel->getById($id);
        if (!$role) {
            $this->sendError('Role not found', 404);
        }

        // Get permissions for the role
        $role['permissions'] = $this->roleModel->getPermissions($id);

        $this->sendSuccess($role, 'Role retrieved successfully');
    }

    /**
     * Create a new role
     */
    public function create() {
        $this->ensureMethodAllowed('POST');
        requirePermission('manage_roles');

        $data = $this->getJsonData();

        // Validate required fields
        $requiredFields = ['name', 'description'];
        $missingFields = $this->validateRequired($data, $requiredFields);

        if (!empty($missingFields)) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $missingFields
            ]);
        }

        // Check if role already exists
        if ($this->roleModel->findByName($data['name'])) {
            $this->sendError('Role already exists', 409);
        }

        // Create role
        $roleId = $this->roleModel->create(
            $data['name'],
            $data['description'],
            $data['is_system'] ?? false
        );

        if (!$roleId) {
            $this->sendError('Failed to create role', 500);
        }

        // Assign permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            // Check if permissions are provided as names or IDs
            if (!empty($data['permissions']) && is_string($data['permissions'][0])) {
                // Permissions provided as names
                $this->roleModel->assignPermissionsByName($roleId, $data['permissions']);
            } else {
                // Permissions provided as IDs (backward compatibility)
                $this->roleModel->assignPermissions($roleId, $data['permissions']);
            }
        }

        $role = $this->roleModel->getById($roleId);
        $role['permissions'] = $this->roleModel->getPermissions($roleId);

        $this->sendSuccess($role, 'Role created successfully', 201);
    }

    /**
     * Update a role
     */
    public function update() {
        $this->ensureMethodAllowed('PUT');
        requirePermission('manage_roles');

        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('Role ID is required', 400);
        }

        $role = $this->roleModel->getById($id);
        if (!$role) {
            $this->sendError('Role not found', 404);
        }

        $data = $this->getJsonData();

        // Update role
        $success = $this->roleModel->update($id, [
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null
        ]);

        if (!$success) {
            $this->sendError('Failed to update role', 500);
        }

        // Assign permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            // Check if permissions are provided as names or IDs
            if (!empty($data['permissions']) && is_string($data['permissions'][0])) {
                // Permissions provided as names
                $success = $this->roleModel->assignPermissionsByName($id, $data['permissions']);
            } else {
                // Permissions provided as IDs (backward compatibility)
                $success = $this->roleModel->assignPermissions($id, $data['permissions']);
            }
            
            if (!$success) {
                $this->sendError('Failed to assign permissions', 500);
            }
        }

        $updatedRole = $this->roleModel->getById($id);
        $updatedRole['permissions'] = $this->roleModel->getPermissions($id);

        $this->sendSuccess($updatedRole, 'Role updated successfully');
    }

    /**
     * Delete a role
     */
    public function delete() {
        $this->ensureMethodAllowed('DELETE');
        requirePermission('manage_roles');

        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('Role ID is required', 400);
        }

        $role = $this->roleModel->getById($id);
        if (!$role) {
            $this->sendError('Role not found', 404);
        }

        // Check if role is a system role
        if ($role['is_system']) {
            $this->sendError('Cannot delete system role', 403);
        }

        $success = $this->roleModel->delete($id);
        if (!$success) {
            $this->sendError('Failed to delete role', 500);
        }

        $this->sendSuccess(null, 'Role deleted successfully');
    }

    /**
     * Get permissions for a role
     */
    public function getPermissions() {
        $this->ensureMethodAllowed('GET');
        requirePermission('view_roles');

        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('Role ID is required', 400);
        }

        $role = $this->roleModel->getById($id);
        if (!$role) {
            $this->sendError('Role not found', 404);
        }

        $permissions = $this->roleModel->getPermissions($id);
        $this->sendSuccess($permissions, 'Permissions retrieved successfully');
    }

    /**
     * Assign permissions to a role
     */
    public function assignPermissions() {
        $this->ensureMethodAllowed('POST');
        requirePermission('assign_permissions');

        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('Role ID is required', 400);
        }

        $role = $this->roleModel->getById($id);
        if (!$role) {
            $this->sendError('Role not found', 404);
        }

        $data = $this->getJsonData();
        if (!isset($data['permissions']) || !is_array($data['permissions'])) {
            $this->sendError('Permissions array is required', 400);
        }

        // Check if permissions are provided as names or IDs
        if (!empty($data['permissions']) && is_string($data['permissions'][0])) {
            // Permissions provided as names
            $success = $this->roleModel->assignPermissionsByName($id, $data['permissions']);
        } else {
            // Permissions provided as IDs (backward compatibility)
            $success = $this->roleModel->assignPermissions($id, $data['permissions']);
        }

        if (!$success) {
            $this->sendError('Failed to assign permissions', 500);
        }

        $updatedPermissions = $this->roleModel->getPermissions($id);
        $this->sendSuccess($updatedPermissions, 'Permissions assigned successfully');
    }

    /**
     * Get users with a specific role
     */
    public function getUsers() {
        $this->ensureMethodAllowed('GET');
        requirePermission('view_roles');

        $id = $this->getQueryParam('id');
        if (!$id) {
            $this->sendError('Role ID is required', 400);
        }

        $role = $this->roleModel->getById($id);
        if (!$role) {
            $this->sendError('Role not found', 404);
        }

        $users = $this->roleModel->getUsersByRoleId($id);
        
        // Remove passwords from response
        foreach ($users as &$user) {
            unset($user['password']);
        }

        $this->sendSuccess($users, 'Users retrieved successfully');
    }
} 