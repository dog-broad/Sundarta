<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class ProductController extends BaseController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    /**
     * Get all products with pagination and optional filtering
     */
    public function getAllProducts() {
        $this->ensureMethodAllowed('GET');
        
        $pagination = $this->getPaginationParams();
        $page = $pagination['page'];
        $perPage = $pagination['per_page'];
        
        $categoryId = $this->getQueryParam('category');
        $search = $this->getQueryParam('search');
        
        $result = $this->productModel->getWithPagination($page, $perPage, $categoryId, $search);
        
        $this->sendSuccess($result, 'Products retrieved successfully');
    }

    /**
     * Get a product by ID
     */
    public function getProduct() {
        $this->ensureMethodAllowed('GET');
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Product ID is required', 400);
        }
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->sendError('Product not found', 404);
        }
        
        $this->sendSuccess($product, 'Product retrieved successfully');
    }

    /**
     * Create a new product (admin only)
     */
    public function createProduct() {
        $this->ensureMethodAllowed('POST');
        
        requireAdmin();
        
        $data = $this->getJsonData();
        
        // Validate required fields
        $requiredFields = ['name', 'description', 'price', 'stock', 'category'];
        $missingFields = $this->validateRequired($data, $requiredFields);
        
        if (!empty($missingFields)) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $missingFields
            ]);
        }
        
        // Handle images (convert array to JSON if needed)
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = json_encode($data['images']);
        } else if (!isset($data['images'])) {
            $data['images'] = '[]';
        }
        
        // Create product
        $productId = $this->productModel->create(
            $data['name'],
            $data['description'],
            $data['price'],
            $data['stock'],
            $data['category'],
            $data['images']
        );
        
        if (!$productId) {
            $this->sendError('Failed to create product', 500);
        }
        
        $product = $this->productModel->getById($productId);
        
        $this->sendSuccess($product, 'Product created successfully', 201);
    }

    /**
     * Update a product (admin only)
     */
    public function updateProduct() {
        $this->ensureMethodAllowed('PUT');
        
        requireAdmin();
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Product ID is required', 400);
        }
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->sendError('Product not found', 404);
        }
        
        $data = $this->getJsonData();
        
        // Handle images (convert array to JSON if needed)
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = json_encode($data['images']);
        }
        
        // Update product
        $success = $this->productModel->update($id, $data);
        
        if (!$success) {
            $this->sendError('Failed to update product', 500);
        }
        
        $updatedProduct = $this->productModel->getById($id);
        
        $this->sendSuccess($updatedProduct, 'Product updated successfully');
    }

    /**
     * Delete a product (admin only)
     */
    public function deleteProduct() {
        $this->ensureMethodAllowed('DELETE');
        
        requireAdmin();
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Product ID is required', 400);
        }
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->sendError('Product not found', 404);
        }
        
        // Delete product
        $success = $this->productModel->deleteById($id);
        
        if (!$success) {
            $this->sendError('Failed to delete product', 500);
        }
        
        $this->sendSuccess([], 'Product deleted successfully');
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts() {
        $this->ensureMethodAllowed('GET');
        
        $limit = (int)$this->getQueryParam('limit', 6);
        $products = $this->productModel->getFeatured($limit);
        
        $this->sendSuccess($products, 'Featured products retrieved successfully');
    }

    /**
     * Search products
     */
    public function searchProducts() {
        $this->ensureMethodAllowed('GET');
        
        $query = $this->getQueryParam('query');
        
        if (!$query) {
            $this->sendError('Search query is required', 400);
        }
        
        $products = $this->productModel->search($query);
        
        $this->sendSuccess($products, 'Products search results');
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory() {
        $this->ensureMethodAllowed('GET');
        
        $categoryId = $this->getQueryParam('category_id');
        
        if (!$categoryId) {
            $this->sendError('Category ID is required', 400);
        }
        
        $products = $this->productModel->getByCategory($categoryId);
        
        $this->sendSuccess($products, 'Products by category retrieved successfully');
    }

    /**
     * Update product stock (admin only)
     */
    public function updateStock() {
        $this->ensureMethodAllowed('PUT');
        
        requireAdmin();
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Product ID is required', 400);
        }
        
        $data = $this->getJsonData();
        
        if (!isset($data['quantity'])) {
            $this->sendError('Quantity is required', 400);
        }
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->sendError('Product not found', 404);
        }
        
        // Update stock
        $success = $this->productModel->updateStock($id, $data['quantity']);
        
        if (!$success) {
            $this->sendError('Failed to update stock', 500);
        }
        
        $updatedProduct = $this->productModel->getById($id);
        
        $this->sendSuccess($updatedProduct, 'Stock updated successfully');
    }
} 