<?php
require_once __DIR__ . '/BaseModel.php';

class OrderModel extends BaseModel {
    protected $table;
    protected $itemsTable;
    public function __construct() {
        parent::__construct();
        $this->table = 'orders';
        $this->itemsTable = 'order_items';
    }

    /**
     * Create a new order
     * 
     * @param int $userId User ID
     * @param array $items Array of items (each item is an array with keys: product_id or service_id, quantity, total_price)
     * @param string $status Order status (default: pending)
     * @return int|false Order ID or false on failure
     */
    public function create($userId, $items, $status = 'pending') {
        $this->db->begin_transaction();
        try {
            $sql = "INSERT INTO {$this->table} (user_id, status) VALUES (?, ?)";
            $orderId = $this->insert($sql, [$userId, $status], 'is');
            if (!$orderId) {
                throw new Exception('Failed to create order for user ID ' . $userId);
            }

            foreach ($items as $item) {
                // Check if it's a product or service
                if (isset($item['product_id'])) {
                    $sql = "INSERT INTO {$this->itemsTable} (order_id, product_id, quantity, total_price) 
                            VALUES (?, ?, ?, ?)";
                    $result = $this->insert($sql, [$orderId, $item['product_id'], $item['quantity'], $item['total_price']], 'iiid');
                } elseif (isset($item['service_id'])) {
                    $sql = "INSERT INTO {$this->itemsTable} (order_id, service_id, quantity, total_price) 
                            VALUES (?, ?, ?, ?)";
                    $result = $this->insert($sql, [$orderId, $item['service_id'], $item['quantity'], $item['total_price']], 'iiid');
                } else {
                    throw new Exception('Item must have either product_id or service_id');
                }
                
                if (!$result) {
                    throw new Exception('Failed to create order item for order ID ' . $orderId);
                }
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
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
        // First get all orders for the user
        $ordersSql = "SELECT o.* FROM {$this->table} o WHERE o.user_id = ? ORDER BY o.created_at DESC";
        $orders = $this->select($ordersSql, [$userId], 'i');
        
        if (empty($orders)) {
            return [];
        }
        
        // Get order IDs
        $orderIds = array_column($orders, 'id');
        $orderIdsStr = implode(',', $orderIds);
        
        // Get product items for these orders
        $productItemsSql = "SELECT oi.*, oi.order_id, p.name as item_name, p.images as item_images, 'product' as item_type
                FROM {$this->itemsTable} oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id IN ({$orderIdsStr}) AND oi.product_id IS NOT NULL";
        $productItems = $this->select($productItemsSql);
        
        // Get service items for these orders
        $serviceItemsSql = "SELECT oi.*, oi.order_id, s.name as item_name, s.images as item_images, 'service' as item_type
                FROM {$this->itemsTable} oi
                JOIN services s ON oi.service_id = s.id
                WHERE oi.order_id IN ({$orderIdsStr}) AND oi.service_id IS NOT NULL";
        $serviceItems = $this->select($serviceItemsSql);
        
        // Combine all items
        $allItems = array_merge($productItems, $serviceItems);
        
        // Group items by order ID
        $itemsByOrder = [];
        foreach ($allItems as $item) {
            $orderId = $item['order_id'];
            if (!isset($itemsByOrder[$orderId])) {
                $itemsByOrder[$orderId] = [];
            }
            $itemsByOrder[$orderId][] = $item;
        }
        
        // Add items to each order
        foreach ($orders as &$order) {
            $order['items'] = $itemsByOrder[$order['id']] ?? [];
        }
        
        return $orders;
    }

    /**
     * Get orders by status
     * 
     * @param string $status Order status
     * @return array Array of orders
     */
    public function getByStatus($status) {
        // First get all orders with the specified status
        $ordersSql = "SELECT o.*, u.username, u.email 
                FROM {$this->table} o
                JOIN users u ON o.user_id = u.id
                WHERE o.status = ?
                ORDER BY o.created_at DESC";
        $orders = $this->select($ordersSql, [$status], 's');
        
        if (empty($orders)) {
            return [];
        }
        
        // Get order IDs
        $orderIds = array_column($orders, 'id');
        $orderIdsStr = implode(',', $orderIds);
        
        // Get product items for these orders
        $productItemsSql = "SELECT oi.*, oi.order_id, p.name as item_name, 'product' as item_type
                FROM {$this->itemsTable} oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id IN ({$orderIdsStr}) AND oi.product_id IS NOT NULL";
        $productItems = $this->select($productItemsSql);
        
        // Get service items for these orders
        $serviceItemsSql = "SELECT oi.*, oi.order_id, s.name as item_name, 'service' as item_type
                FROM {$this->itemsTable} oi
                JOIN services s ON oi.service_id = s.id
                WHERE oi.order_id IN ({$orderIdsStr}) AND oi.service_id IS NOT NULL";
        $serviceItems = $this->select($serviceItemsSql);
        
        // Combine all items
        $allItems = array_merge($productItems, $serviceItems);
        
        // Group items by order ID
        $itemsByOrder = [];
        foreach ($allItems as $item) {
            $orderId = $item['order_id'];
            if (!isset($itemsByOrder[$orderId])) {
                $itemsByOrder[$orderId] = [];
            }
            $itemsByOrder[$orderId][] = $item;
        }
        
        // Add items to each order
        foreach ($orders as &$order) {
            $order['items'] = $itemsByOrder[$order['id']] ?? [];
        }
        
        return $orders;
    }

    /**
     * Get order details
     * 
     * @param int $id Order ID
     * @return array|null Order details or null if not found
     */
    public function getDetails($id) {
        // First get the order basic information
        $orderSql = "SELECT o.*, u.username, u.email, u.phone
                FROM {$this->table} o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ?";
        $orderResult = $this->select($orderSql, [$id], 'i');
        
        if (empty($orderResult)) {
            return null;
        }
        
        $order = $orderResult[0];
        
        // Get product items
        $productItemsSql = "SELECT oi.*, p.name as item_name, p.description as item_description, 
                p.images as item_images, 'product' as item_type
                FROM {$this->itemsTable} oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ? AND oi.product_id IS NOT NULL";
        $productItems = $this->select($productItemsSql, [$id], 'i');
        
        // Get service items
        $serviceItemsSql = "SELECT oi.*, s.name as item_name, s.description as item_description, 
                s.images as item_images, 'service' as item_type
                FROM {$this->itemsTable} oi
                JOIN services s ON oi.service_id = s.id
                WHERE oi.order_id = ? AND oi.service_id IS NOT NULL";
        $serviceItems = $this->select($serviceItemsSql, [$id], 'i');
        
        // Combine items
        $order['items'] = array_merge($productItems, $serviceItems);
        
        return $order;
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
        
        // Get orders with pagination
        $sql = "SELECT o.*, u.username 
                FROM {$this->table} o
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
        
        if (!empty($orders)) {
            // Get order IDs
            $orderIds = array_column($orders, 'id');
            $orderIdsStr = implode(',', $orderIds);
            
            // Get product items for these orders
            $productItemsSql = "SELECT oi.*, oi.order_id, p.name as item_name, 'product' as item_type
                    FROM {$this->itemsTable} oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id IN ({$orderIdsStr}) AND oi.product_id IS NOT NULL";
            $productItems = $this->select($productItemsSql);
            
            // Get service items for these orders
            $serviceItemsSql = "SELECT oi.*, oi.order_id, s.name as item_name, 'service' as item_type
                    FROM {$this->itemsTable} oi
                    JOIN services s ON oi.service_id = s.id
                    WHERE oi.order_id IN ({$orderIdsStr}) AND oi.service_id IS NOT NULL";
            $serviceItems = $this->select($serviceItemsSql);
            
            // Combine all items
            $allItems = array_merge($productItems, $serviceItems);
            
            // Group items by order ID
            $itemsByOrder = [];
            foreach ($allItems as $item) {
                $orderId = $item['order_id'];
                if (!isset($itemsByOrder[$orderId])) {
                    $itemsByOrder[$orderId] = [];
                }
                $itemsByOrder[$orderId][] = $item;
            }
            
            // Add items to each order
            foreach ($orders as &$order) {
                $order['items'] = $itemsByOrder[$order['id']] ?? [];
            }
        }
        
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
                COUNT(DISTINCT o.id) as total_orders,
                SUM(oi.total_price) as total_revenue,
                COUNT(DISTINCT o.user_id) as total_customers,
                (SELECT COUNT(*) FROM {$this->table} WHERE status = 'pending') as pending_orders,
                (SELECT COUNT(*) FROM {$this->table} WHERE status = 'shipped') as shipped_orders,
                (SELECT COUNT(*) FROM {$this->table} WHERE status = 'delivered') as delivered_orders,
                (SELECT COUNT(*) FROM {$this->table} WHERE status = 'cancelled') as cancelled_orders,
                COUNT(DISTINCT oi.product_id) as total_products_ordered,
                COUNT(DISTINCT oi.service_id) as total_services_ordered
                FROM {$this->table} o
                JOIN {$this->itemsTable} oi ON o.id = oi.order_id";
        $result = $this->select($sql);
        return $result[0] ?? [
            'total_orders' => 0,
            'total_revenue' => 0,
            'total_customers' => 0,
            'pending_orders' => 0,
            'shipped_orders' => 0,
            'delivered_orders' => 0,
            'cancelled_orders' => 0,
            'total_products_ordered' => 0,
            'total_services_ordered' => 0
        ];
    }

    /**
     * Create an order from cart items
     * 
     * @param int $userId User ID
     * @param array $cartItems Array of cart items
     * @param string $status Order status (default: pending)
     * @return int|false Order ID or false on failure
     */
    public function createFromCart($userId, $cartItems, $status = 'pending') {
        $this->db->begin_transaction();
        try {
            $sql = "INSERT INTO {$this->table} (user_id, status) VALUES (?, ?)";
            $orderId = $this->insert($sql, [$userId, $status], 'is');
            if (!$orderId) {
                throw new Exception('Failed to create order for user ' . $userId);
            }

            foreach ($cartItems as $item) {
                // Check if it's a product or service
                if (isset($item['product_id'])) {
                    $sql = "INSERT INTO {$this->itemsTable} (order_id, product_id, quantity, total_price) 
                            VALUES (?, ?, ?, ?)";
                    $result = $this->insert($sql, [$orderId, $item['product_id'], $item['quantity'], $item['total_price']], 'iiid');
                } elseif (isset($item['service_id'])) {
                    $sql = "INSERT INTO {$this->itemsTable} (order_id, service_id, quantity, total_price) 
                            VALUES (?, ?, ?, ?)";
                    $result = $this->insert($sql, [$orderId, $item['service_id'], $item['quantity'], $item['total_price']], 'iiid');
                } else {
                    throw new Exception('Cart item must have either product_id or service_id');
                }

                if (!$result) {
                    throw new Exception('Failed to create order item for order ID ' . $orderId);
                }
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
}
