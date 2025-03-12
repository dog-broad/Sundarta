<?php
require_once __DIR__ . '/../config/db.php';

class BaseModel {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = connectDB();
    }

    /**
     * Execute a prepared SQL query with parameters
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind to the query
     * @param string $types Types of parameters (i: integer, d: double, s: string, b: blob)
     * @return mysqli_stmt|false Statement object or false on failure
     */
    protected function executeQuery($sql, $params = [], $types = '') {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }

        if (!empty($params)) {
            if (empty($types)) {
                // Auto-determine types if not provided
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param) || is_double($param)) {
                        $types .= 'd';
                    } elseif (is_string($param)) {
                        $types .= 's';
                    } else {
                        $types .= 'b'; // Default to blob
                    }
                }
            }
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }
        return $stmt;
    }

    /**
     * Execute a SELECT query and return results as an associative array
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind to the query
     * @param string $types Types of parameters
     * @return array Array of results or empty array on failure
     */
    protected function select($sql, $params = [], $types = '') {
        $stmt = $this->executeQuery($sql, $params, $types);
        if (!$stmt) return [];
        
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }

    /**
     * Execute an INSERT query and return the last insert ID
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind to the query
     * @param string $types Types of parameters
     * @return int|false Last insert ID or false on failure
     */
    protected function insert($sql, $params = [], $types = '') {
        $stmt = $this->executeQuery($sql, $params, $types);
        if (!$stmt) return false;
        
        $insertId = $this->db->insert_id;
        $stmt->close();
        return $insertId;
    }

    /**
     * Execute an UPDATE or DELETE query and return the number of affected rows
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind to the query
     * @param string $types Types of parameters
     * @return int|false Number of affected rows or false on failure
     */
    protected function updateOrDelete($sql, $params = [], $types = '') {
        $stmt = $this->executeQuery($sql, $params, $types);
        if (!$stmt) return false;
        
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows;
    }

    /**
     * Get all records from the table
     * 
     * @param string $orderBy Column to order by
     * @param string $order Order direction (ASC or DESC)
     * @param int $limit Maximum number of records to return
     * @param int $offset Offset for pagination
     * @return array Array of records
     */
    public function getAll($orderBy = 'id', $order = 'ASC', $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table}";
        
        // Add ORDER BY clause
        $sql .= " ORDER BY {$orderBy} {$order}";
        
        // Add LIMIT and OFFSET for pagination
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params = [$limit];
            $types = 'i';
            
            if ($offset !== null) {
                $sql .= " OFFSET ?";
                $params[] = $offset;
                $types .= 'i';
            }
            
            return $this->select($sql, $params, $types);
        }
        
        return $this->select($sql);
    }

    /**
     * Get a record by ID
     * 
     * @param int $id ID of the record
     * @return array|null Record data or null if not found
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->select($sql, [$id], 'i');
        return $result[0] ?? null;
    }

    /**
     * Delete a record by ID
     * 
     * @param int $id ID of the record to delete
     * @return bool True on success, false on failure
     */
    public function deleteById($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->updateOrDelete($sql, [$id], 'i') !== false;
    }

    /**
     * Count total records in the table
     * 
     * @param string $whereClause Optional WHERE clause
     * @param array $params Parameters for the WHERE clause
     * @param string $types Types of parameters
     * @return int Total count
     */
    public function count($whereClause = '', $params = [], $types = '') {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($whereClause)) {
            $sql .= " WHERE {$whereClause}";
        }
        
        $result = $this->select($sql, $params, $types);
        return $result[0]['count'] ?? 0;
    }
} 