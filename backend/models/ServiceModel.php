<?php
require_once __DIR__ . '/BaseModel.php';

class ServiceModel extends BaseModel {
    public function __construct() {
        parent::__construct();
        $this->table = 'services';
    }

    /**
     * Create a new service
     * 
     * @param int $userId User ID (seller)
     * @param string $name Service name
     * @param string $description Service description
     * @param float $price Service price
     * @param int $category Category ID
     * @param string $images JSON string of image URLs
     * @param string $process JSON string of process steps
     * @param string $faqs JSON string of FAQs
     * @return int|false Service ID or false on failure
     */
    public function create($userId, $name, $description, $price, $category, $images = '[]', $process = '[]', $faqs = '{}') {
        $sql = "INSERT INTO {$this->table} (user_id, name, description, price, category, images, process, faqs) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->insert($sql, [$userId, $name, $description, $price, $category, $images, $process, $faqs], 'issdiiss');
    }


    /**
     * Get service by ID, override the parent method to add reviews
     * 
     * @param int $id Service ID
     * @return array|false Service details with reviews or false on failure
     */
    public function getById($id) {
        $service = parent::getById($id);
        if ($service) {
            $service['reviews'] = $this->getReviews($id);
            $service['rating'] = $this->getRating($id);
        }
        return $service;
    }

    /**
     * Update a service
     * 
     * @param int $id Service ID
     * @param array $data Data to update
     * @return bool True on success, false on failure
     */
    public function update($id, $data) {
        $allowedFields = ['name', 'description', 'price', 'category', 'images', 'process', 'faqs'];
        $updates = [];
        $params = [];
        $types = '';

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = ?";
                $params[] = $data[$field];
                
                if ($field === 'price') {
                    $types .= 'd';
                } elseif ($field === 'category') {
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
     * Get services by category
     * 
     * @param int $categoryId Category ID
     * @return array Array of services
     */
    public function getByCategory($categoryId) {
        $sql = "SELECT s.*, COALESCE(AVG(r.rating), 0) AS rating 
                FROM {$this->table} s
                LEFT JOIN reviews r ON s.id = r.service_id
                WHERE s.category = ?
                GROUP BY s.id";
        return $this->select($sql, [$categoryId], 'i');
    }

    /**
     * Get services by seller (user)
     * 
     * @param int $userId User ID
     * @return array Array of services
     */
    public function getBySeller($userId) {
        $sql = "SELECT s.*, COALESCE(AVG(r.rating), 0) AS rating 
                FROM {$this->table} s
                LEFT JOIN reviews r ON s.id = r.service_id
                WHERE s.user_id = ?
                GROUP BY s.id";
        return $this->select($sql, [$userId], 'i');
    }

    /**
     * Search services by name or description
     * 
     * @param string $query Search query
     * @return array Array of services
     */
    public function search($query) {
        $sql = "SELECT s.*, COALESCE(AVG(r.rating), 0) AS rating 
                FROM {$this->table} s
                LEFT JOIN reviews r ON s.id = r.service_id
                WHERE s.name LIKE ? OR s.description LIKE ?
                GROUP BY s.id";
        $searchTerm = "%{$query}%";
        return $this->select($sql, [$searchTerm, $searchTerm], 'ss');
    }

    /**
     * Get featured services (limited number of services)
     * 
     * @param int $limit Number of services to return
     * @return array Array of services
     */
    public function getFeatured($limit = 6) {
        $sql = "SELECT s.*, COALESCE(AVG(r.rating), 0) AS rating 
                FROM {$this->table} s
                LEFT JOIN reviews r ON s.id = r.service_id
                GROUP BY s.id
                ORDER BY s.id DESC
                LIMIT ?";
        return $this->select($sql, [$limit], 'i');
    }

    /**
     * Check if a user owns a service
     * 
     * @param int $serviceId Service ID
     * @param int $userId User ID
     * @return bool True if user owns the service, false otherwise
     */
    public function isOwner($serviceId, $userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE id = ? AND user_id = ?";
        $result = $this->select($sql, [$serviceId, $userId], 'ii');
        return ($result[0]['count'] ?? 0) > 0;
    }

    /**
     * Get services with pagination and optional filtering
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @param int|null $categoryId Optional category filter
     * @param int|null $userId Optional seller filter
     * @param string|null $search Optional search term
     * @return array Array with services and pagination info
     */
    public function getWithPagination($page = 1, $perPage = 10, $categoryId = null, $userId = null, $search = null) {
        $offset = ($page - 1) * $perPage;
        $whereClause = [];
        $params = [];
        $types = '';
        
        if ($categoryId !== null) {
            $whereClause[] = "s.category = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }
        
        if ($userId !== null) {
            $whereClause[] = "s.user_id = ?";
            $params[] = $userId;
            $types .= 'i';
        }
        
        if ($search !== null) {
            $whereClause[] = "(s.name LIKE ? OR s.description LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }
        
        $sql = "SELECT s.*, COALESCE(AVG(r.rating), 0) AS rating, u.username as seller_name 
                FROM {$this->table} s
                LEFT JOIN reviews r ON s.id = r.service_id
                JOIN users u ON s.user_id = u.id";
        $countSql = "SELECT COUNT(DISTINCT s.id) as count FROM {$this->table} s"; // Ensure distinct count
        
        if (!empty($whereClause)) {
            $whereString = implode(' AND ', $whereClause);
            $sql .= " WHERE {$whereString}";
            $countSql .= " WHERE {$whereString}";
        }
        
        $sql .= " GROUP BY s.id ORDER BY s.id DESC LIMIT ? OFFSET ?"; // Group by service ID
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
        
        $services = $this->select($sql, $params, $types);
        
        // Get total count for pagination
        $countResult = $this->select($countSql, array_slice($params, 0, -2), substr($types, 0, -2));
        $totalCount = $countResult[0]['count'] ?? 0;
        $totalPages = ceil($totalCount / $perPage);
        
        return [
            'services' => $services,
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
     * Get reviews for a service
     * 
     * @param int $serviceId Service ID
     * @return array Array of reviews
     */
    public function getReviews($serviceId) {
        $sql = "SELECT * from reviews where service_id = ?";
        return $this->select($sql, [$serviceId], 'i');
    }

    /**
     * Get average rating for a service
     * 
     * @param int $serviceId Service ID
     * @return float Average rating
     */
    public function getRating($serviceId) {
        $sql = "SELECT AVG(rating) as rating FROM reviews WHERE service_id = ?";
        $result = $this->select($sql, [$serviceId], 'i');
        return $result[0]['rating'] ?? 0;
    }
} 