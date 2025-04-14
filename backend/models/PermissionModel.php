<?php
require_once __DIR__ . '/BaseModel.php';

error_reporting(E_ALL);
ini_set("display_errors","On");

class PermissionModel extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'permissions';
    }

    /**
     * Create a new permission
     * 
     * @param string $name Permission name
     * @param string $description Permission description
     * @return int|false Permission ID or false on failure
     */
    public function create($name, $description) {
        // Check if permission already exists
        if ($this->findByName($name)) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (name, description) 
                VALUES (?, ?)";
        return $this->insert($sql, [$name, $description]);
    }

    /**
     * Find a permission by name
     * 
     * @param string $name Permission name
     * @return array|null Permission data or null if not found
     */
    public function findByName($name) {
        $sql = "SELECT * FROM {$this->table} WHERE name = ?";
        $result = $this->select($sql, [$name], 's');
        return $result[0] ?? null;
    }

    /**
     * Update permission
     * 
     * @param int $id Permission ID
     * @param array $data Data to update (name, description)
     * @return bool True on success, false on failure
     */
    public function update($id, $data) {
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
     * Delete a permission
     * 
     * @param int $id Permission ID
     * @return bool True on success, false on failure
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->updateOrDelete($sql, [$id], 'i') !== false;
    }

    /**
     * Get all permissions
     * 
     * @param string $orderBy Column to order by
     * @param string $order Order direction (ASC or DESC)
     * @param int|null $limit Limit of records to fetch
     * @param int|null $offset Offset for records to fetch
     * @return array Array of permissions
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
     * Get permissions by role ID
     * 
     * @param int $roleId Role ID
     * @return array Array of permissions
     */
    public function getByRoleId($roleId) {
        $sql = "SELECT p.* FROM {$this->table} p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?
                ORDER BY p.name ASC";
        return $this->select($sql, [$roleId], 'i');
    }

    /**
     * Check if a role has a specific permission
     * 
     * @param int $roleId Role ID
     * @param string $permissionName Permission name
     * @return bool True if role has the permission, false otherwise
     */
    public function roleHasPermission($roleId, $permissionName) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ? AND p.name = ?";
        $result = $this->select($sql, [$roleId, $permissionName], 'is');
        return ($result[0]['count'] ?? 0) > 0;
    }

    /**
     * Check if a user has a specific permission
     * 
     * @param int $userId User ID
     * @param string $permissionName Permission name
     * @return bool True if user has the permission, false otherwise
     */
    public function userHasPermission($userId, $permissionName) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} p
                JOIN role_permissions rp ON p.id = rp.permission_id
                JOIN user_roles ur ON rp.role_id = ur.role_id
                WHERE ur.user_id = ? AND p.name = ?";
        $result = $this->select($sql, [$userId, $permissionName], 'is');
        return ($result[0]['count'] ?? 0) > 0;
    }

    /**
     * Get all permissions for a user
     * 
     * @param int $userId User ID
     * @return array Array of permissions
     */
    public function getByUserId($userId) {
        $sql = "SELECT DISTINCT p.* FROM {$this->table} p
                JOIN role_permissions rp ON p.id = rp.permission_id
                JOIN user_roles ur ON rp.role_id = ur.role_id
                WHERE ur.user_id = ?
                ORDER BY p.name ASC";
        return $this->select($sql, [$userId], 'i');
    }

    /**
     * Get all roles for a permission
     * 
     * @param int $permissionId Permission ID
     * @return array Array of roles
     */
    public function getRolesByPermissionId($permissionId) {
        $sql = "SELECT r.* FROM roles r
                JOIN role_permissions rp ON r.id = rp.role_id
                WHERE rp.permission_id = ?
                ORDER BY r.name ASC";
        return $this->select($sql, [$permissionId], 'i');
    }
} 