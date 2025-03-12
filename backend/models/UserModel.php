<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'users';
    }

    /**
     * Create a new user
     * 
     * @param string $username Username
     * @param string $email Email
     * @param string $phone Phone number
     * @param string $password Password (will be hashed)
     * @param string $role Role (admin, customer, seller)
     * @return int|false User ID or false on failure
     */
    public function create($username, $email, $phone, $password, $role = 'customer') {
        // Check if email already exists
        if ($this->findByEmail($email)) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (username, email, phone, password, role) 
                VALUES (?, ?, ?, ?, ?)";
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->insert($sql, [$username, $email, $phone, $hashedPassword, $role]);
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
     * @param string $role Role (admin, customer, seller)
     * @return array Array of users
     */
    public function getByRole($role) {
        $sql = "SELECT * FROM {$this->table} WHERE role = ?";
        return $this->select($sql, [$role], 's');
    }
} 