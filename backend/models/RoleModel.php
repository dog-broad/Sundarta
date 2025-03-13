<?php
require_once __DIR__ . '/BaseModel.php';

class RoleModel extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'roles';
    }

    /**
     * Create a new role
     * 
     * @param string $name Role name
     * @param string $description Role description
     * @param bool $isSystem Whether this is a system role
     * @return int|false Role ID or false on failure
     */
    public function create($name, $description, $isSystem = false) {
        // Check if role already exists
        if ($this->findByName($name)) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (name, description, is_system) 
                VALUES (?, ?, ?)";
        return $this->insert($sql, [$name, $description, $isSystem ? 1 : 0]);
    }

    /**
     * Find a role by name
     * 
     * @param string $name Role name
     * @return array|null Role data or null if not found
     */
    public function findByName($name) {
        $sql = "SELECT * FROM {$this->table} WHERE name = ?";
        $result = $this->select($sql, [$name], 's');
        return $result[0] ?? null;
    }

    /**
     * Update role
     * 
     * @param int $id Role ID
     * @param array $data Data to update (name, description)
     * @return bool True on success, false on failure
     */
    public function update($id, $data) {
        // Check if role is a system role
        $role = $this->getById($id);
        if (!$role) {
            return false;
        }

        // System roles can only have their description updated
        if ($role['is_system'] && isset($data['name'])) {
            return false;
        }

        $allowedFields = ['name', 'description'];
        $updates = [];
        $params = [];
        $types = '';

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = ?";
                $params[] = $data[$field];
                $types .= 's';
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';

        return $this->updateOrDelete($sql, $params, $types) !== false;
    }

    /**
     * Delete a role
     * 
     * @param int $id Role ID
     * @return bool True on success, false on failure
     */
    public function delete($id) {
        // Check if role is a system role
        $role = $this->getById($id);
        if (!$role || $role['is_system']) {
            return false;
        }

        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->updateOrDelete($sql, [$id], 'i') !== false;
    }

    /**
     * Get all roles
     * 
     * @param string $orderBy Column to order by
     * @param string $order Order direction (ASC or DESC)
     * @param int|null $limit Limit of records
     * @param int|null $offset Offset of records
     * @return array Array of roles
     */
    public function getAll($orderBy = 'id', $order = 'ASC', $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order}";
        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }
        return $this->select($sql);
    }

    /**
     * Assign permissions to a role
     * 
     * @param int $roleId Role ID
     * @param array $permissionIds Array of permission IDs
     * @return bool True on success, false on failure
     */
    public function assignPermissions($roleId, $permissionIds) {
        // Start transaction
        $this->db->begin_transaction();

        try {
            // Remove existing permissions
            $sql = "DELETE FROM role_permissions WHERE role_id = ?";
            $this->updateOrDelete($sql, [$roleId], 'i');

            // Add new permissions
            if (!empty($permissionIds)) {
                $values = [];
                $params = [];
                $types = '';

                foreach ($permissionIds as $permissionId) {
                    $values[] = "(?, ?)";
                    $params[] = $roleId;
                    $params[] = $permissionId;
                    $types .= 'ii';
                }

                $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES " . implode(', ', $values);
                $this->insert($sql, $params, $types);
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Get permissions for a role
     * 
     * @param int $roleId Role ID
     * @return array Array of permissions
     */
    public function getPermissions($roleId) {
        $sql = "SELECT p.* FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?
                ORDER BY p.name ASC";
        return $this->select($sql, [$roleId], 'i');
    }

    /**
     * Get roles for a user
     * 
     * @param int $userId User ID
     * @return array Array of roles
     */
    public function getRolesByUserId($userId) {
        $sql = "SELECT r.* FROM {$this->table} r
                JOIN user_roles ur ON r.id = ur.role_id
                WHERE ur.user_id = ?
                ORDER BY r.name ASC";
        return $this->select($sql, [$userId], 'i');
    }

    /**
     * Check if a user has a specific role
     * 
     * @param int $userId User ID
     * @param string $roleName Role name
     * @return bool True if user has the role, false otherwise
     */
    public function userHasRole($userId, $roleName) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} r
                JOIN user_roles ur ON r.id = ur.role_id
                WHERE ur.user_id = ? AND r.name = ?";
        $result = $this->select($sql, [$userId, $roleName], 'is');
        return ($result[0]['count'] ?? 0) > 0;
    }

    /**
     * Assign roles to a user
     * 
     * @param int $userId User ID
     * @param array $roleIds Array of role IDs
     * @return bool True on success, false on failure
     */
    public function assignRolesToUser($userId, $roleIds) {
        // Start transaction
        $this->db->begin_transaction();

        try {
            // Remove existing roles
            $sql = "DELETE FROM user_roles WHERE user_id = ?";
            $this->updateOrDelete($sql, [$userId], 'i');

            // Add new roles
            if (!empty($roleIds)) {
                $values = [];
                $params = [];
                $types = '';

                foreach ($roleIds as $roleId) {
                    $values[] = "(?, ?)";
                    $params[] = $userId;
                    $params[] = $roleId;
                    $types .= 'ii';
                }

                $sql = "INSERT INTO user_roles (user_id, role_id) VALUES " . implode(', ', $values);
                $this->insert($sql, $params, $types);
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Get users with a specific role
     * 
     * @param int $roleId Role ID
     * @return array Array of users
     */
    public function getUsersByRoleId($roleId) {
        $sql = "SELECT u.* FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                WHERE ur.role_id = ?
                ORDER BY u.username ASC";
        return $this->select($sql, [$roleId], 'i');
    }

    /**
     * Assign roles to a user by role names
     * 
     * @param int $userId User ID
     * @param array $roleNames Array of role names
     * @return bool True on success, false on failure
     */
    public function assignRolesToUserByName($userId, $roleNames) {
        // Start transaction
        $this->db->begin_transaction();

        try {
            // Remove existing roles
            $sql = "DELETE FROM user_roles WHERE user_id = ?";
            $this->updateOrDelete($sql, [$userId], 'i');

            // Add new roles
            if (!empty($roleNames)) {
                // Get role IDs from names
                $roleIds = [];
                foreach ($roleNames as $roleName) {
                    $role = $this->findByName($roleName);
                    if ($role) {
                        $roleIds[] = $role['id'];
                    }
                }

                if (!empty($roleIds)) {
                    $values = [];
                    $params = [];
                    $types = '';

                    foreach ($roleIds as $roleId) {
                        $values[] = "(?, ?)";
                        $params[] = $userId;
                        $params[] = $roleId;
                        $types .= 'ii';
                    }

                    $sql = "INSERT INTO user_roles (user_id, role_id) VALUES " . implode(', ', $values);
                    $this->insert($sql, $params, $types);
                }
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Assign permissions to a role by permission names
     * 
     * @param int $roleId Role ID
     * @param array $permissionNames Array of permission names
     * @return bool True on success, false on failure
     */
    public function assignPermissionsByName($roleId, $permissionNames) {
        // Start transaction
        $this->db->begin_transaction();

        try {
            // Remove existing permissions
            $sql = "DELETE FROM role_permissions WHERE role_id = ?";
            $this->updateOrDelete($sql, [$roleId], 'i');

            // Add new permissions
            if (!empty($permissionNames)) {
                // Get permission IDs from names
                $permissionIds = [];
                $permissionModel = new PermissionModel();
                
                foreach ($permissionNames as $permissionName) {
                    $permission = $permissionModel->findByName($permissionName);
                    if ($permission) {
                        $permissionIds[] = $permission['id'];
                    }
                }

                if (!empty($permissionIds)) {
                    $values = [];
                    $params = [];
                    $types = '';

                    foreach ($permissionIds as $permissionId) {
                        $values[] = "(?, ?)";
                        $params[] = $roleId;
                        $params[] = $permissionId;
                        $types .= 'ii';
                    }

                    $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES " . implode(', ', $values);
                    $this->insert($sql, $params, $types);
                }
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Get role names for a user
     * 
     * @param int $userId User ID
     * @return array Array of role names
     */
    public function getRoleNamesByUserId($userId) {
        $roles = $this->getRolesByUserId($userId);
        return array_column($roles, 'name');
    }
} 