<?php
require_once __DIR__ . '/BaseModel.php';

class OrderModel extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'orders';
    }

    /**
     * Create a new order
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @param int $quantity Quantity
     * @param float $totalPrice Total price
     * @param string $status Order status (default: pending)
     * @return int|false Order ID or false on failure
     */
    public function create($userId, $productId, $quantity, $totalPrice, $status = 'pending') {
        $sql = "INSERT INTO {$this->table} (user_id, product_id, quantity, total_price, status) 
                VALUES (?, ?, ?, ?, ?)";
        return $this->insert($sql, [$userId, $productId, $quantity, $totalPrice, $status], 'iidss');
    }

    /**
     * Update order status
     * 
     * @param int $id Order ID
     * @param string $status New status
     * @return bool True on success, false on failure
     */
    public function updateStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        return $this->updateOrDelete($sql, [$status, $id], 'si') !== false;
    }

    /**
     * Get orders by user
     * 
     * @param int $userId User ID
     * @return array Array of orders
     */
    public function getByUser($userId) {
        $sql = "SELECT o.*, p.name as product_name, p.images as product_images 
                FROM {$this->table} o
                JOIN products p ON o.product_id = p.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC";
        return $this->select($sql, [$userId], 'i');
    }

    /**
     * Get orders by status
     * 
     * @param string $status Order status
     * @return array Array of orders
     */
    public function getByStatus($status) {
        $sql = "SELECT o.*, p.name as product_name, u.username, u.email 
                FROM {$this->table} o
                JOIN products p ON o.product_id = p.id
                JOIN users u ON o.user_id = u.id
                WHERE o.status = ?
                ORDER BY o.created_at DESC";
        return $this->select($sql, [$status], 's');
    }

    /**
     * Get order details
     * 
     * @param int $id Order ID
     * @return array|null Order details or null if not found
     */
    public function getDetails($id) {
        $sql = "SELECT o.*, p.name as product_name, p.description as product_description, 
                p.images as product_images, u.username, u.email, u.phone
                FROM {$this->table} o
                JOIN products p ON o.product_id = p.id
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ?";
        $result = $this->select($sql, [$id], 'i');
        return $result[0] ?? null;
    }

    /**
     * Get orders with pagination and optional filtering
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @param int|null $userId Optional user filter
     * @param string|null $status Optional status filter
     * @return array Array with orders and pagination info
     */
    public function getWithPagination($page = 1, $perPage = 10, $userId = null, $status = null) {
        $offset = ($page - 1) * $perPage;
        $whereClause = [];
        $params = [];
        $types = '';
        
        if ($userId !== null) {
            $whereClause[] = "o.user_id = ?";
            $params[] = $userId;
            $types .= 'i';
        }
        
        if ($status !== null) {
            $whereClause[] = "o.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $sql = "SELECT o.*, p.name as product_name, u.username 
                FROM {$this->table} o
                JOIN products p ON o.product_id = p.id
                JOIN users u ON o.user_id = u.id";
        $countSql = "SELECT COUNT(*) as count FROM {$this->table} o";
        
        if (!empty($whereClause)) {
            $whereString = implode(' AND ', $whereClause);
            $sql .= " WHERE {$whereString}";
            $countSql .= " WHERE {$whereString}";
        }
        
        $sql .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
        
        $orders = $this->select($sql, $params, $types);
        
        // Get total count for pagination
        $countResult = $this->select($countSql, array_slice($params, 0, -2), substr($types, 0, -2));
        $totalCount = $countResult[0]['count'] ?? 0;
        $totalPages = ceil($totalCount / $perPage);
        
        return [
            'orders' => $orders,
            'pagination' => [
                'total' => $totalCount,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $totalPages,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $totalCount)
            ]
        ];
    }

    /**
     * Get order statistics
     * 
     * @return array Order statistics
     */
    public function getStatistics() {
        $sql = "SELECT 
                COUNT(*) as total_orders,
                SUM(total_price) as total_revenue,
                COUNT(DISTINCT user_id) as total_customers,
                (SELECT COUNT(*) FROM {$this->table} WHERE status = 'pending') as pending_orders,
                (SELECT COUNT(*) FROM {$this->table} WHERE status = 'shipped') as shipped_orders,
                (SELECT COUNT(*) FROM {$this->table} WHERE status = 'delivered') as delivered_orders,
                (SELECT COUNT(*) FROM {$this->table} WHERE status = 'cancelled') as cancelled_orders
                FROM {$this->table}";
        $result = $this->select($sql);
        return $result[0] ?? [
            'total_orders' => 0,
            'total_revenue' => 0,
            'total_customers' => 0,
            'pending_orders' => 0,
            'shipped_orders' => 0,
            'delivered_orders' => 0,
            'cancelled_orders' => 0
        ];
    }
} 