<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class CartController extends BaseController {
    private $cartModel;
    private $productModel;

    public function __construct() {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Get cart items for the current user
     */
    public function getCart() {
        $this->ensureMethodAllowed('GET');
        
        requireLogin();
        
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
        
        $data = $this->getJsonData();
        
        // Validate required fields
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
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Cart item ID is required', 400);
        }
        
        $data = $this->getJsonData();
        
        if (!isset($data['quantity'])) {
            $this->sendError('Quantity is required', 400);
        }
        
        $quantity = $data['quantity'];
        
        // If quantity is 0 or negative, remove the item
        if ($quantity <= 0) {
            $success = $this->cartModel->deleteById($id);
            
            if (!$success) {
                $this->sendError('Failed to remove item from cart', 500);
            }
            
            $userId = getCurrentUserId();
            $cartItems = $this->cartModel->getByUser($userId);
            $cartSummary = $this->cartModel->getCartSummary($userId);
            
            $this->sendSuccess([
                'items' => $cartItems,
                'summary' => $cartSummary
            ], 'Item removed from cart successfully');
        }
        
        // Update quantity
        $success = $this->cartModel->updateQuantity($id, $quantity);
        
        if (!$success) {
            $this->sendError('Failed to update cart item', 500);
        }
        
        $userId = getCurrentUserId();
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
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Cart item ID is required', 400);
        }
        
        // Remove from cart
        $success = $this->cartModel->deleteById($id);
        
        if (!$success) {
            $this->sendError('Failed to remove item from cart', 500);
        }
        
        $userId = getCurrentUserId();
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
        
        $userId = getCurrentUserId();
        
        // Clear cart
        $success = $this->cartModel->clearCart($userId);
        
        if (!$success) {
            $this->sendError('Failed to clear cart', 500);
        }
        
        $this->sendSuccess([], 'Cart cleared successfully');
    }

    /**
     * Check if cart items are in stock
     */
    public function checkStock() {
        $this->ensureMethodAllowed('GET');
        
        requireLogin();
        
        $userId = getCurrentUserId();
        
        // Check stock
        $outOfStockItems = $this->cartModel->checkStock($userId);
        
        $this->sendSuccess([
            'out_of_stock_items' => $outOfStockItems,
            'has_stock_issues' => !empty($outOfStockItems)
        ], 'Stock check completed');
    }
} 