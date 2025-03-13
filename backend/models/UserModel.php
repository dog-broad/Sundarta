<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/RoleModel.php';

class UserModel extends BaseModel {
    private $roleModel;

    public function __construct() {
        parent::__construct();
        $this->table = 'users';
        $this->roleModel = new RoleModel();
    }

    /**
     * Create a new user
     * 
     * @param string $username Username
     * @param string $email Email
     * @param string $phone Phone number
     * @param string $password Password (will be hashed)
     * @param array $roleIds Array of role IDs (default: customer role)
     * @return int|false User ID or false on failure
     */
    public function create($username, $email, $phone, $password, $roleIds = null) {
        // Check if email already exists
        if ($this->findByEmail($email)) {
            return false;
        }

        // Start transaction
        $this->db->begin_transaction();

        try {
            // Insert user
            $sql = "INSERT INTO {$this->table} (username, email, phone, password) 
                    VALUES (?, ?, ?, ?)";
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = $this->insert($sql, [$username, $email, $phone, $hashedPassword]);

            if (!$userId) {
                throw new Exception("Failed to create user");
            }

            // If no roles specified, assign default customer role
            if (empty($roleIds)) {
                $customerRole = $this->roleModel->findByName('customer');
                if ($customerRole) {
                    $roleIds = [$customerRole['id']];
                }
            }

            // Assign roles to user
            if (!empty($roleIds)) {
                $this->roleModel->assignRolesToUser($userId, $roleIds);
            }

            // Commit transaction
            $this->db->commit();
            return $userId;
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Find a user by email
     * 
     * @param string $email Email
     * @return array|null User data or null if not found
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $result = $this->select($sql, [$email], 's');
        return $result[0] ?? null;
    }

    /**
     * Find a user by username
     * 
     * @param string $username Username
     * @return array|null User data or null if not found
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $result = $this->select($sql, [$username], 's');
        return $result[0] ?? null;
    }

    /**
     * Update user profile
     * 
     * @param int $id User ID
     * @param array $data Data to update (username, email, phone)
     * @return bool True on success, false on failure
     */
    public function updateProfile($id, $data) {
        // Only allow these fields to be updated by regular users
        $allowedFields = ['username', 'email', 'phone'];
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
     * Update user profile by admin
     * 
     * @param int $id User ID
     * @param array $data Data to update (username, email, phone, is_active)
     * @return bool True on success, false on failure
     */
    public function updateProfileByAdmin($id, $data) {
        // Admins can update these additional fields
        $allowedFields = ['username', 'email', 'phone', 'is_active'];
        $updates = [];
        $params = [];
        $types = '';

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = ?";
                $params[] = $data[$field];
                $types .= $field === 'is_active' ? 'i' : 's';
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
     * Update user password
     * 
     * @param int $id User ID
     * @param string $password New password (will be hashed)
     * @return bool True on success, false on failure
     */
    public function updatePassword($id, $password) {
        $sql = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->updateOrDelete($sql, [$hashedPassword, $id], 'si') !== false;
    }

    /**
     * Verify user password
     * 
     * @param int $id User ID
     * @param string $password Password to verify
     * @return bool True if password is correct, false otherwise
     */
    public function verifyPassword($id, $password) {
        $user = $this->getById($id);
        if (!$user) {
            return false;
        }
        return password_verify($password, $user['password']);
    }

    /**
     * Get users by role
     * 
     * @param string $roleName Role name
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array Array of users
     */
    public function getByRole($roleName, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT DISTINCT u.* FROM {$this->table} u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                WHERE r.name = ?
                ORDER BY u.username ASC
                LIMIT ?, ?";
        
        return $this->select($sql, [$roleName, $offset, $perPage], 'sii');
    }

    /**
     * Count users by role
     * 
     * @param string $roleName Role name
     * @return int Number of users
     */
    public function countByRole($roleName) {
        $sql = "SELECT COUNT(DISTINCT u.id) as count FROM {$this->table} u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                WHERE r.name = ?";
        
        $result = $this->select($sql, [$roleName], 's');
        return $result[0]['count'] ?? 0;
    }

    /**
     * Get user with roles
     * 
     * @param int $id User ID
     * @return array|null User data with roles or null if not found
     */
    public function getUserWithRoles($id) {
        $user = $this->getById($id);
        if (!$user) {
            return null;
        }

        $user['roles'] = $this->roleModel->getRolesByUserId($id);
        return $user;
    }

    /**
     * Check if user has a specific role
     * 
     * @param int $id User ID
     * @param string $roleName Role name
     * @return bool True if user has the role, false otherwise
     */
    public function hasRole($id, $roleName) {
        return $this->roleModel->userHasRole($id, $roleName);
    }

    /**
     * Assign roles to a user
     * 
     * @param int $id User ID
     * @param array $roleIds Array of role IDs
     * @return bool True on success, false on failure
     */
    public function assignRoles($id, $roleIds) {
        return $this->roleModel->assignRolesToUser($id, $roleIds);
    }

    /**
     * Get all users with pagination
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array Array of users
     */
    public function getAllPaginated($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}
                ORDER BY username ASC
                LIMIT ?, ?";
        
        return $this->select($sql, [$offset, $perPage], 'ii');
    }

    /**
     * Count all users
     * 
     * @return int Number of users
     */
    public function countAll() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->select($sql);
        return $result[0]['count'] ?? 0;
    }

    /**
     * Delete a user
     * 
     * @param int $id User ID
     * @return bool True on success, false on failure
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->updateOrDelete($sql, [$id], 'i') !== false;
    }
} 