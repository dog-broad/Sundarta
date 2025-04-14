<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class CartController extends BaseController {
    private $cartModel;
    private $productModel;
    private $serviceModel;

    public function __construct() {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
        $this->serviceModel = new ServiceModel();
    }

    /**
     * Get cart items for the current user
     */
    public function getCart() {
        $this->ensureMethodAllowed('GET');
        
        requireLogin();
        requirePermission('place_orders');
        
        $userId = getCurrentUserId();
        $cartItems = $this->cartModel->getByUser($userId);
        $cartSummary = $this->cartModel->getCartSummary($userId);
        
        $this->sendSuccess([
            'items' => $cartItems,
            'summary' => $cartSummary
        ], 'Cart retrieved successfully');
    }

    /**
     * Add an item to the cart
     */
    public function addToCart() {
        $this->ensureMethodAllowed('POST');
        
        requireLogin();
        requirePermission('place_orders');
        
        $data = $this->getJsonData();
        
        // Validate required fields
        if (isset($data['product_id'])) {
            // Adding a product
            $requiredFields = ['product_id', 'quantity'];
            $missingFields = $this->validateRequired($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->sendError('Missing required fields', 400, [
                    'missing_fields' => $missingFields
                ]);
            }
            
            $userId = getCurrentUserId();
            $productId = $data['product_id'];
            $quantity = $data['quantity'];
            
            // Check if product exists
            $product = $this->productModel->getById($productId);
            
            if (!$product) {
                $this->sendError('Product not found', 404);
            }
            
            // Check if product has enough stock
            if ($product['stock'] < $quantity) {
                $this->sendError('Not enough stock available', 400);
            }
            
            // Add to cart
            $cartItemId = $this->cartModel->addItem($userId, $productId, $quantity);
            
            if (!$cartItemId) {
                $this->sendError('Failed to add item to cart', 500);
            }
        } elseif (isset($data['service_id'])) {
            // Adding a service
            $requiredFields = ['service_id', 'quantity'];
            $missingFields = $this->validateRequired($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->sendError('Missing required fields', 400, [
                    'missing_fields' => $missingFields
                ]);
            }
            
            $userId = getCurrentUserId();
            $serviceId = $data['service_id'];
            $quantity = $data['quantity'];
            
            // Check if service exists
            $service = $this->serviceModel->getById($serviceId);
            
            if (!$service) {
                $this->sendError('Service not found', 404);
            }
            
            // Add to cart
            $cartItemId = $this->cartModel->addServiceItem($userId, $serviceId, $quantity);
            
            if (!$cartItemId) {
                $this->sendError('Failed to add service to cart', 500);
            }
        } else {
            $this->sendError('Either product_id or service_id is required', 400);
        }
        
        $cartItems = $this->cartModel->getByUser($userId);
        $cartSummary = $this->cartModel->getCartSummary($userId);
        
        $this->sendSuccess([
            'items' => $cartItems,
            'summary' => $cartSummary
        ], 'Item added to cart successfully');
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItem() {
        $this->ensureMethodAllowed('PUT');
        
        requireLogin();
        requirePermission('place_orders');
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Cart item ID is required', 400);
        }
        
        $data = $this->getJsonData();
        
        if (!isset($data['quantity']) || $data['quantity'] < 1) {
            $this->sendError('Valid quantity is required', 400);
        }
        
        $userId = getCurrentUserId();
        
        // Check if cart item exists and belongs to the user
        $cartItem = $this->cartModel->getById($id);
        
        if (!$cartItem || $cartItem['user_id'] != $userId) {
            $this->sendError('Cart item not found', 404);
        }
        
        // Check if it's a product and has enough stock
        if (isset($cartItem['product_id']) && $cartItem['product_id']) {
            $product = $this->productModel->getById($cartItem['product_id']);
            
            if (!$product) {
                $this->sendError('Product not found', 404);
            }
            
            if ($product['stock'] < $data['quantity']) {
                $this->sendError('Not enough stock available', 400);
            }
        }
        
        // Update cart item
        $success = $this->cartModel->updateQuantity($id, $data['quantity']);
        
        if (!$success) {
            $this->sendError('Failed to update cart item', 500);
        }
        
        $cartItems = $this->cartModel->getByUser($userId);
        $cartSummary = $this->cartModel->getCartSummary($userId);
        
        $this->sendSuccess([
            'items' => $cartItems,
            'summary' => $cartSummary
        ], 'Cart item updated successfully');
    }

    /**
     * Remove an item from the cart
     */
    public function removeFromCart() {
        $this->ensureMethodAllowed('DELETE');
        
        requireLogin();
        requirePermission('place_orders');
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Cart item ID is required', 400);
        }
        
        $userId = getCurrentUserId();
        
        // Check if cart item exists and belongs to the user
        $cartItem = $this->cartModel->getById($id);
        
        if (!$cartItem || $cartItem['user_id'] != $userId) {
            $this->sendError('Cart item not found', 404);
        }
        
        // Remove from cart
        $success = $this->cartModel->deleteById($id);
        
        if (!$success) {
            $this->sendError('Failed to remove item from cart', 500);
        }
        
        $cartItems = $this->cartModel->getByUser($userId);
        $cartSummary = $this->cartModel->getCartSummary($userId);
        
        $this->sendSuccess([
            'items' => $cartItems,
            'summary' => $cartSummary
        ], 'Item removed from cart successfully');
    }

    /**
     * Clear the cart
     */
    public function clearCart() {
        $this->ensureMethodAllowed('DELETE');
        
        requireLogin();
        requirePermission('place_orders');
        
        $userId = getCurrentUserId();
        
        // Clear cart
        $success = $this->cartModel->clearCart($userId);
        
        if (!$success) {
            $this->sendError('Failed to clear cart', 500);
        }
        
        $this->sendSuccess([], 'Cart cleared successfully');
    }

    /**
     * Check stock availability for cart items
     */
    public function checkStock() {
        $this->ensureMethodAllowed('GET');
        
        requireLogin();
        requirePermission('place_orders');
        
        $userId = getCurrentUserId();
        
        // Check stock
        $outOfStockItems = $this->cartModel->checkStock($userId);
        
        $this->sendSuccess([
            'out_of_stock_items' => $outOfStockItems,
            'has_stock_issues' => !empty($outOfStockItems)
        ], 'Stock checked successfully');
    }
} 