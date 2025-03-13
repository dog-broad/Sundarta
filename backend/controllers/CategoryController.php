<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class CategoryController extends BaseController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        $this->ensureMethodAllowed('GET');
        
        $withCounts = $this->getQueryParam('with_counts', false);
        
        if ($withCounts) {
            $categories = $this->categoryModel->getWithCounts();
        } else {
            $categories = $this->categoryModel->getAll('name', 'ASC');
        }
        
        $this->sendSuccess($categories, 'Categories retrieved successfully');
    }

    /**
     * Get a category by ID
     */
    public function getCategory() {
        $this->ensureMethodAllowed('GET');
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Category ID is required', 400);
        }
        
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            $this->sendError('Category not found', 404);
        }
        
        $this->sendSuccess($category, 'Category retrieved successfully');
    }

    /**
     * Create a new category (admin only)
     */
    public function createCategory() {
        $this->ensureMethodAllowed('POST');
        
        requirePermission('manage_categories');
        
        $data = $this->getJsonData();
        
        // Validate required fields
        if (!isset($data['name']) || empty($data['name'])) {
            $this->sendError('Category name is required', 400);
        }
        
        // Check if category already exists
        if ($this->categoryModel->findByName($data['name'])) {
            $this->sendError('Category already exists', 409);
        }
        
        // Create category
        $categoryId = $this->categoryModel->create($data['name']);
        
        if (!$categoryId) {
            $this->sendError('Failed to create category', 500);
        }
        
        $category = $this->categoryModel->getById($categoryId);
        
        $this->sendSuccess($category, 'Category created successfully', 201);
    }

    /**
     * Update a category (admin only)
     */
    public function updateCategory() {
        $this->ensureMethodAllowed('PUT');
        
        requirePermission('manage_categories');
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Category ID is required', 400);
        }
        
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            $this->sendError('Category not found', 404);
        }
        
        $data = $this->getJsonData();
        
        // Validate required fields
        if (!isset($data['name']) || empty($data['name'])) {
            $this->sendError('Category name is required', 400);
        }
        
        // Update category
        $success = $this->categoryModel->update($id, $data['name']);
        
        if (!$success) {
            $this->sendError('Failed to update category', 500);
        }
        
        $updatedCategory = $this->categoryModel->getById($id);
        
        $this->sendSuccess($updatedCategory, 'Category updated successfully');
    }

    /**
     * Delete a category (admin only)
     */
    public function deleteCategory() {
        $this->ensureMethodAllowed('DELETE');
        
        requirePermission('manage_categories');
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Category ID is required', 400);
        }
        
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            $this->sendError('Category not found', 404);
        }
        
        // Check if category has associated products or services
        if ($this->categoryModel->hasAssociatedItems($id)) {
            $this->sendError('Cannot delete category with associated products or services', 400);
        }
        
        // Delete category
        $success = $this->categoryModel->deleteById($id);
        
        if (!$success) {
            $this->sendError('Failed to delete category', 500);
        }
        
        $this->sendSuccess([], 'Category deleted successfully');
    }
} 