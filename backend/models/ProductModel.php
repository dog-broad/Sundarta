<?php
require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'products';
    }

    /**
     * Create a new product
     * 
     * @param string $name Product name
     * @param string $description Product description
     * @param float $price Product price
     * @param int $stock Product stock
     * @param int $category Category ID
     * @param string $specifications JSON string of product specifications
     * @param string $instructions Product usage instructions
     * @param string $images JSON string of image URLs
     * @return int|false Product ID or false on failure
     */
    public function create($name, $description, $price, $stock, $category, $specifications = '{}', $instructions = '', $images = '[]') {
        $sql = "INSERT INTO {$this->table} (name, description, price, stock, category, specifications, instructions, images) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->insert($sql, [$name, $description, $price, $stock, $category, $specifications, $instructions, $images], 'ssdiisss');
    }

    /**
     * Get a product by ID, Override the parent method to add reviews
     * 
     * @param int $id Product ID
     * @return array|null Product data or null if not found
     */
    public function getById($id) {
        $product = parent::getById($id);
        if ($product) {
            $product['reviews'] = $this->getReviews($id);
            $product['rating'] = $this->getRating($id);
        }
        return $product;
    }

    /**
     * Update a product
     * 
     * @param int $id Product ID
     * @param array $data Data to update
     * @return bool True on success, false on failure
     */
    public function update($id, $data) {
        $allowedFields = ['name', 'description', 'price', 'stock', 'category', 'specifications', 'instructions', 'images'];
        $updates = [];
        $params = [];
        $types = '';

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = ?";
                $params[] = $data[$field];
                
                if ($field === 'price') {
                    $types .= 'd';
                } elseif (in_array($field, ['stock', 'category'])) {
                    $types .= 'i';
                } else {
                    $types .= 's';
                }
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
     * Get products by category
     * 
     * @param int $categoryId Category ID
     * @return array Array of products
     */
    public function getByCategory($categoryId) {
        $sql = "SELECT p.*, COALESCE(AVG(r.rating), 0) AS rating 
                FROM {$this->table} p
                LEFT JOIN reviews r ON p.id = r.product_id
                WHERE p.category = ?
                GROUP BY p.id";
        return $this->select($sql, [$categoryId], 'i');
    }

    /**
     * Search products by name or description
     * 
     * @param string $query Search query
     * @return array Array of products
     */
    public function search($query) {
        $sql = "
            SELECT p.*, COALESCE(AVG(r.rating), 0) AS rating 
            FROM {$this->table} p
            LEFT JOIN reviews r ON p.id = r.product_id
            WHERE p.name LIKE ? OR p.description LIKE ?
            GROUP BY p.id
        ";
        $searchTerm = "%{$query}%";
        return $this->select($sql, [$searchTerm, $searchTerm], 'ss');
    }

    /**
     * Get featured products with their average rating
     * 
     * @param int $limit Number of products to return
     * @return array Array of products with ratings
     */
    public function getFeatured($limit = 6) {
        $sql = "
            SELECT p.*, COALESCE(AVG(r.rating), 0) AS rating 
            FROM {$this->table} p
            LEFT JOIN reviews r ON p.id = r.product_id
            GROUP BY p.id
            ORDER BY p.id DESC
            LIMIT ?
        ";

        return $this->select($sql, [$limit], 'i');
    }

    /**
     * Update product stock
     * 
     * @param int $id Product ID
     * @param int $quantity Quantity to add (positive) or subtract (negative)
     * @return bool True on success, false on failure
     */
    public function updateStock($id, $quantity) {
        $sql = "UPDATE {$this->table} SET stock = stock + ? WHERE id = ?";
        return $this->updateOrDelete($sql, [$quantity, $id], 'ii') !== false;
    }

    /**
     * Check if product has enough stock
     * 
     * @param int $id Product ID
     * @param int $quantity Quantity to check
     * @return bool True if enough stock, false otherwise
     */
    public function hasStock($id, $quantity) {
        $sql = "SELECT stock FROM {$this->table} WHERE id = ?";
        $result = $this->select($sql, [$id], 'i');
        
        if (empty($result)) {
            return false;
        }
        
        return $result[0]['stock'] >= $quantity;
    }

    /**
     * Get products with pagination and optional filtering
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @param int|null $categoryId Optional category filter
     * @param string|null $search Optional search term
     * @return array Array with products and pagination info
     */
    public function getWithPagination($page = 1, $perPage = 10, $categoryId = null, $search = null) {
        $offset = ($page - 1) * $perPage;
        $whereClause = [];
        $params = [];
        $types = '';
        
        if ($categoryId !== null) {
            $whereClause[] = "category = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }
        
        if ($search !== null) {
            $whereClause[] = "(name LIKE ? OR description LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }
        
        $sql = "SELECT p.*, COALESCE(AVG(r.rating), 0) AS rating 
                FROM {$this->table} p
                LEFT JOIN reviews r ON p.id = r.product_id";
        $countSql = "SELECT COUNT(DISTINCT p.id) as count FROM {$this->table} p"; // Ensure distinct count
        
        if (!empty($whereClause)) {
            $whereString = implode(' AND ', $whereClause);
            $sql .= " WHERE {$whereString}";
            $countSql .= " WHERE {$whereString}";
        }
        
        $sql .= " GROUP BY p.id ORDER BY p.id DESC LIMIT ? OFFSET ?"; // Group by product ID
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
        
        $products = $this->select($sql, $params, $types);
        
        // Get total count for pagination
        $countResult = $this->select($countSql, array_slice($params, 0, -2), substr($types, 0, -2));
        $totalCount = $countResult[0]['count'] ?? 0;
        $totalPages = ceil($totalCount / $perPage);
        
        return [
            'products' => $products,
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
     * Get product reviews
     * 
     * @param int $productId Product ID
     * @return array Array of reviews
     */
    public function getReviews($productId) {
        $sql = "SELECT * FROM reviews WHERE product_id = ?";
        return $this->select($sql, [$productId], 'i');
    }

    /**
     * Get product rating
     * 
     * @param int $productId Product ID
     * @return float Product rating
     */
    public function getRating($productId) {
        $sql = "SELECT AVG(rating) FROM reviews WHERE product_id = ?";
        $result = $this->select($sql, [$productId], 'i');
        return $result[0]['AVG(rating)'] ?? 0;
    }
} 