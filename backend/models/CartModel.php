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
        $sql = "SELECT c.*, p.name, p.price, p.images, p.stock,
                (c.quantity * p.price) as subtotal
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?
                ORDER BY c.created_at DESC";
        return $this->select($sql, [$userId], 'i');
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
        $sql = "SELECT 
                SUM(c.quantity) as total_items,
                SUM(c.quantity * p.price) as total_price
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";
        $result = $this->select($sql, [$userId], 'i');
        
        return [
            'total_items' => (int)($result[0]['total_items'] ?? 0),
            'total_price' => (float)($result[0]['total_price'] ?? 0)
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
                WHERE c.user_id = ? AND c.quantity > p.stock";
        return $this->select($sql, [$userId], 'i');
    }
} 