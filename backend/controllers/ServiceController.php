<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class ServiceController extends BaseController {
    private $serviceModel;
    private $roleModel;

    public function __construct() {
        $this->serviceModel = new ServiceModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * Get all services with pagination and optional filtering
     */
    public function getAllServices() {
        $this->ensureMethodAllowed('GET');
        
        $pagination = $this->getPaginationParams();
        $page = $pagination['page'];
        $perPage = $pagination['per_page'];
        
        $categoryId = $this->getQueryParam('category');
        $userId = $this->getQueryParam('user_id');
        $search = $this->getQueryParam('search');
        
        $result = $this->serviceModel->getWithPagination($page, $perPage, $categoryId, $userId, $search);
        
        $this->sendSuccess($result, 'Services retrieved successfully');
    }

    /**
     * Get a service by ID
     */
    public function getService() {
        $this->ensureMethodAllowed('GET');
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Service ID is required', 400);
        }
        
        $service = $this->serviceModel->getById($id);
        
        if (!$service) {
            $this->sendError('Service not found', 404);
        }
        
        $this->sendSuccess($service, 'Service retrieved successfully');
    }

    /**
     * Create a new service (seller or admin only)
     */
    public function createService() {
        $this->ensureMethodAllowed('POST');
        
        requireLogin();
        
        // Check if user has permission to create services
        if (!hasPermission('manage_services') && !hasPermission('manage_own_services')) {
            $this->sendError('You do not have permission to create services', 403);
        }
        
        $data = $this->getJsonData();
        
        // Validate required fields
        $requiredFields = ['name', 'description', 'price', 'category'];
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
        
        // Create service
        $userId = getCurrentUserId();
        $serviceId = $this->serviceModel->create(
            $userId,
            $data['name'],
            $data['description'],
            $data['price'],
            $data['category'],
            $data['images']
        );
        
        if (!$serviceId) {
            $this->sendError('Failed to create service', 500);
        }
        
        $service = $this->serviceModel->getById($serviceId);
        
        $this->sendSuccess($service, 'Service created successfully', 201);
    }

    /**
     * Update a service (owner or admin only)
     */
    public function updateService() {
        $this->ensureMethodAllowed('PUT');
        
        requireLogin();
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Service ID is required', 400);
        }
        
        $service = $this->serviceModel->getById($id);
        
        if (!$service) {
            $this->sendError('Service not found', 404);
        }
        
        $userId = getCurrentUserId();
        
        // Check if user has permission to update this service
        $canManageAllServices = hasPermission('manage_services');
        $canManageOwnServices = hasPermission('manage_own_services') && $service['user_id'] == $userId;
        
        if (!$canManageAllServices && !$canManageOwnServices) {
            $this->sendError('You do not have permission to update this service', 403);
        }
        
        $data = $this->getJsonData();
        
        // Handle images (convert array to JSON if needed)
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = json_encode($data['images']);
        }
        
        // Update service
        $success = $this->serviceModel->update($id, $data);
        
        if (!$success) {
            $this->sendError('Failed to update service', 500);
        }
        
        $updatedService = $this->serviceModel->getById($id);
        
        $this->sendSuccess($updatedService, 'Service updated successfully');
    }

    /**
     * Delete a service (owner or admin only)
     */
    public function deleteService() {
        $this->ensureMethodAllowed('DELETE');
        
        requireLogin();
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Service ID is required', 400);
        }
        
        $service = $this->serviceModel->getById($id);
        
        if (!$service) {
            $this->sendError('Service not found', 404);
        }
        
        $userId = getCurrentUserId();
        
        // Check if user has permission to delete this service
        $canManageAllServices = hasPermission('manage_services');
        $canManageOwnServices = hasPermission('manage_own_services') && $service['user_id'] == $userId;
        
        if (!$canManageAllServices && !$canManageOwnServices) {
            $this->sendError('You do not have permission to delete this service', 403);
        }
        
        // Delete service
        $success = $this->serviceModel->deleteById($id);
        
        if (!$success) {
            $this->sendError('Failed to delete service', 500);
        }
        
        $this->sendSuccess([], 'Service deleted successfully');
    }

    /**
     * Get featured services
     */
    public function getFeaturedServices() {
        $this->ensureMethodAllowed('GET');
        
        $limit = (int)$this->getQueryParam('limit', 6);
        $services = $this->serviceModel->getFeatured($limit);
        
        $this->sendSuccess($services, 'Featured services retrieved successfully');
    }

    /**
     * Search services
     */
    public function searchServices() {
        $this->ensureMethodAllowed('GET');
        
        $query = $this->getQueryParam('query');
        
        if (!$query) {
            $this->sendError('Search query is required', 400);
        }
        
        $services = $this->serviceModel->search($query);
        
        $this->sendSuccess($services, 'Services search results');
    }

    /**
     * Get services by category
     */
    public function getServicesByCategory() {
        $this->ensureMethodAllowed('GET');
        
        $categoryId = $this->getQueryParam('category_id');
        
        if (!$categoryId) {
            $this->sendError('Category ID is required', 400);
        }
        
        $services = $this->serviceModel->getByCategory($categoryId);
        
        $this->sendSuccess($services, 'Services by category retrieved successfully');
    }

    /**
     * Get services by seller
     */
    public function getServicesBySeller() {
        $this->ensureMethodAllowed('GET');
        
        $sellerId = $this->getQueryParam('seller_id');
        
        if (!$sellerId) {
            $this->sendError('Seller ID is required', 400);
        }

        if (!is_numeric($sellerId)) {
            $this->sendError('Seller ID must be a number', 400);
        }

        if (!$this->roleModel->userHasRole($sellerId, 'seller')) {
            $this->sendError('User is not a seller', 400);
        }
        
        $services = $this->serviceModel->getBySeller($sellerId);
        
        $this->sendSuccess($services, 'Services by seller retrieved successfully');
    }

    /**
     * Get my services (for logged in seller)
     */
    public function getMyServices() {
        $this->ensureMethodAllowed('GET');
        
        requireLogin();
        
        // Check if user has permission to manage services
        if (!hasPermission('manage_own_services') && !hasPermission('manage_services')) {
            $this->sendError('You do not have permission to view your services', 403);
        }
        
        $userId = getCurrentUserId();
        
        $pagination = $this->getPaginationParams();
        $page = $pagination['page'];
        $perPage = $pagination['per_page'];
        
        $result = $this->serviceModel->getByUserId($userId, $page, $perPage);
        
        $this->sendSuccess($result, 'Services retrieved successfully');
    }
} 