<?php
require_once __DIR__ . '/BaseModel.php';

class CartModel extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'cart';
    }

    /**
     * Add a product to the cart
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @param int $quantity Quantity
     * @return int|false Cart item ID or false on failure
     */
    public function addItem($userId, $productId, $quantity = 1) {
        // Check if product already exists in cart
        $existingItem = $this->getCartItem($userId, $productId);
        
        if ($existingItem) {
            // Update quantity instead of adding new item
            return $this->updateQuantity($existingItem['id'], $existingItem['quantity'] + $quantity);
        }
        
        $sql = "INSERT INTO {$this->table} (user_id, product_id, quantity) VALUES (?, ?, ?)";
        return $this->insert($sql, [$userId, $productId, $quantity], 'iii');
    }

    /**
     * Add a service to the cart
     * 
     * @param int $userId User ID
     * @param int $serviceId Service ID
     * @param int $quantity Quantity
     * @return int|false Cart item ID or false on failure
     */
    public function addServiceItem($userId, $serviceId, $quantity = 1) {
        // Check if service already exists in cart
        $existingItem = $this->getCartServiceItem($userId, $serviceId);
        
        if ($existingItem) {
            // Update quantity instead of adding new item
            return $this->updateQuantity($existingItem['id'], $existingItem['quantity'] + $quantity);
        }
        
        $sql = "INSERT INTO {$this->table} (user_id, service_id, quantity) VALUES (?, ?, ?)";
        return $this->insert($sql, [$userId, $serviceId, $quantity], 'iii');
    }

    /**
     * Update cart item quantity
     * 
     * @param int $id Cart item ID
     * @param int $quantity New quantity
     * @return bool True on success, false on failure
     */
    public function updateQuantity($id, $quantity) {
        if ($quantity <= 0) {
            // If quantity is 0 or negative, remove the item
            return $this->deleteById($id);
        }
        
        $sql = "UPDATE {$this->table} SET quantity = ? WHERE id = ?";
        return $this->updateOrDelete($sql, [$quantity, $id], 'ii') !== false;
    }

    /**
     * Get cart items for a user
     * 
     * @param int $userId User ID
     * @return array Array of cart items with product details
     */
    public function getByUser($userId) {
        // Get product items
        $productSql = "SELECT c.*, p.name, p.price, p.images, p.stock,
                (c.quantity * p.price) as subtotal, 'product' as item_type
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ? AND c.product_id IS NOT NULL";
        $productItems = $this->select($productSql, [$userId], 'i');
        
        // Get service items
        $serviceSql = "SELECT c.*, s.name, s.price, s.images,
                (c.quantity * s.price) as subtotal, 'service' as item_type
                FROM {$this->table} c
                JOIN services s ON c.service_id = s.id
                WHERE c.user_id = ? AND c.service_id IS NOT NULL";
        $serviceItems = $this->select($serviceSql, [$userId], 'i');
        
        // Combine and return all items
        return array_merge($productItems, $serviceItems);
    }

    /**
     * Get a specific cart item
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @return array|null Cart item or null if not found
     */
    public function getCartItem($userId, $productId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        $result = $this->select($sql, [$userId, $productId], 'ii');
        return $result[0] ?? null;
    }

    /**
     * Get a specific cart service item
     * 
     * @param int $userId User ID
     * @param int $serviceId Service ID
     * @return array|null Cart item or null if not found
     */
    public function getCartServiceItem($userId, $serviceId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND service_id = ?";
        $result = $this->select($sql, [$userId, $serviceId], 'ii');
        return $result[0] ?? null;
    }

    /**
     * Clear all items from a user's cart
     * 
     * @param int $userId User ID
     * @return bool True on success, false on failure
     */
    public function clearCart($userId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        return $this->updateOrDelete($sql, [$userId], 'i') !== false;
    }

    /**
     * Get cart summary for a user
     * 
     * @param int $userId User ID
     * @return array Cart summary (total items, total price)
     */
    public function getCartSummary($userId) {
        // Get product totals
        $productSql = "SELECT 
                SUM(c.quantity) as product_items,
                SUM(c.quantity * p.price) as product_price
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ? AND c.product_id IS NOT NULL";
        $productResult = $this->select($productSql, [$userId], 'i');
        
        // Get service totals
        $serviceSql = "SELECT 
                SUM(c.quantity) as service_items,
                SUM(c.quantity * s.price) as service_price
                FROM {$this->table} c
                JOIN services s ON c.service_id = s.id
                WHERE c.user_id = ? AND c.service_id IS NOT NULL";
        $serviceResult = $this->select($serviceSql, [$userId], 'i');
        
        // Calculate totals
        $productItems = (int)($productResult[0]['product_items'] ?? 0);
        $productPrice = (float)($productResult[0]['product_price'] ?? 0);
        $serviceItems = (int)($serviceResult[0]['service_items'] ?? 0);
        $servicePrice = (float)($serviceResult[0]['service_price'] ?? 0);
        
        return [
            'total_items' => $productItems + $serviceItems,
            'total_price' => $productPrice + $servicePrice,
            'product_items' => $productItems,
            'product_price' => $productPrice,
            'service_items' => $serviceItems,
            'service_price' => $servicePrice
        ];
    }

    /**
     * Check if all items in the cart are in stock
     * 
     * @param int $userId User ID
     * @return array Array of items that are out of stock or have insufficient stock
     */
    public function checkStock($userId) {
        $sql = "SELECT c.*, p.name, p.stock
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ? AND c.product_id IS NOT NULL AND c.quantity > p.stock";
        return $this->select($sql, [$userId], 'i');
    }
} 