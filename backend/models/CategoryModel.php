<?php
require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'category';
    }

    /**
     * Create a new category
     * 
     * @param string $name Category name
     * @return int|false Category ID or false on failure
     */
    public function create($name) {
        // Check if category already exists
        if ($this->findByName($name)) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (name) VALUES (?)";
        return $this->insert($sql, [$name], 's');
    }

    /**
     * Update a category
     * 
     * @param int $id Category ID
     * @param string $name New category name
     * @return bool True on success, false on failure
     */
    public function update($id, $name) {
        // Check if category with this name already exists (except this one)
        $existingCategory = $this->findByName($name);
        if ($existingCategory && $existingCategory['id'] != $id) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET name = ? WHERE id = ?";
        return $this->updateOrDelete($sql, [$name, $id], 'si') !== false;
    }

    /**
     * Find a category by name
     * 
     * @param string $name Category name
     * @return array|null Category data or null if not found
     */
    public function findByName($name) {
        $sql = "SELECT * FROM {$this->table} WHERE name = ?";
        $result = $this->select($sql, [$name], 's');
        return $result[0] ?? null;
    }

    /**
     * Get categories with product/service counts
     * 
     * @return array Array of categories with counts
     */
    public function getWithCounts() {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM products WHERE category = c.id) as product_count,
                (SELECT COUNT(*) FROM services WHERE category = c.id) as service_count
                FROM {$this->table} c
                ORDER BY c.name ASC";
        return $this->select($sql);
    }

    /**
     * Check if a category has associated products or services
     * 
     * @param int $id Category ID
     * @return bool True if category has products or services, false otherwise
     */
    public function hasAssociatedItems($id) {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM products WHERE category = ?) +
                (SELECT COUNT(*) FROM services WHERE category = ?) as total_count";
        $result = $this->select($sql, [$id, $id], 'ii');
        return ($result[0]['total_count'] ?? 0) > 0;
    }
} 