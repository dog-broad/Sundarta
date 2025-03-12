<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class OrderController extends BaseController {
    private $orderModel;
    private $productModel;
    private $cartModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->cartModel = new CartModel();
    }

    /**
     * Get all orders (admin only)
     */
    public function getAllOrders() {
        $this->ensureMethodAllowed('GET');
        
        requireAdmin();
        
        $pagination = $this->getPaginationParams();
        $page = $pagination['page'];
        $perPage = $pagination['per_page'];
        
        $userId = $this->getQueryParam('user_id');
        $status = $this->getQueryParam('status');
        
        $result = $this->orderModel->getWithPagination($page, $perPage, $userId, $status);
        
        $this->sendSuccess($result, 'Orders retrieved successfully');
    }

    /**
     * Get an order by ID
     */
    public function getOrder() {
        $this->ensureMethodAllowed('GET');
        
        requireLogin();
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Order ID is required', 400);
        }
        
        $order = $this->orderModel->getDetails($id);
        
        if (!$order) {
            $this->sendError('Order not found', 404);
        }
        
        $userId = getCurrentUserId();
        $userRole = getCurrentUserRole();
        
        // Check if user is the owner or an admin
        if ($order['user_id'] != $userId && $userRole !== 'admin') {
            $this->sendError('You do not have permission to view this order', 403);
        }
        
        $this->sendSuccess($order, 'Order retrieved successfully');
    }

    /**
     * Create a new order
     */
    public function createOrder() {
        $this->ensureMethodAllowed('POST');
        
        requireLogin();
        
        $data = $this->getJsonData();
        
        // Validate required fields
        $requiredFields = ['items'];
        $missingFields = $this->validateRequired($data, $requiredFields);
        
        if (!empty($missingFields)) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $missingFields
            ]);
        }
        
        $userId = getCurrentUserId();
        $items = $data['items']; // Array of items, each containing product_id, quantity
        
        // Validate each item in the order
        $validatedItems = [];
        foreach ($items as $item) {
            if (!isset($item['product_id'], $item['quantity'])) {
                $this->sendError('Product ID and quantity are required for each item', 400);
            }

            $product = $this->productModel->getById($item['product_id']);
            
            if (!$product) {
                $this->sendError('Product not found', 404);
            }
            
            if ($product['stock'] < $item['quantity']) {
                $this->sendError('Not enough stock for product ' . $item['product_id'], 400);
            }

            // Calculate total price for the item
            $item['total_price'] = $product['price'] * $item['quantity'];
            $validatedItems[] = $item;
        }

        // Create the order
        $orderId = $this->orderModel->create($userId, $validatedItems);
        
        if (!$orderId) {
            $this->sendError('Failed to create order', 500);
        }

        // Update product stock for each item
        foreach ($validatedItems as $item) {
            $this->productModel->updateStock($item['product_id'], -$item['quantity']);
        }

        // Clear cart if the order is created from the cart
        if (isset($data['from_cart']) && $data['from_cart']) {
            foreach ($validatedItems as $item) {
                $cartItem = $this->cartModel->getCartItem($userId, $item['product_id']);
                if ($cartItem) {
                    $this->cartModel->deleteById($cartItem['id']);
                }
            }
        }

        // Fetch the created order details
        $order = $this->orderModel->getDetails($orderId);
        
        $this->sendSuccess($order, 'Order created successfully', 201);
    }

    /**
     * Update order status (admin only)
     */
    public function updateOrderStatus() {
        $this->ensureMethodAllowed('PUT');
        
        requireAdmin();
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Order ID is required', 400);
        }
        
        $data = $this->getJsonData();
        
        if (!isset($data['status']) || empty($data['status'])) {
            $this->sendError('Status is required', 400);
        }
        
        $allowedStatuses = ['pending', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($data['status'], $allowedStatuses)) {
            $this->sendError('Invalid status', 400);
        }
        
        $order = $this->orderModel->getById($id);
        
        if (!$order) {
            $this->sendError('Order not found', 404);
        }
        
        // If cancelling an order, restore stock
        if ($data['status'] === 'cancelled' && $order['status'] !== 'cancelled') {
            foreach ($order['items'] as $item) {
                $this->productModel->updateStock($item['product_id'], $item['quantity']);
            }
        }
        
        // If un-cancelling an order, reduce stock again
        if ($order['status'] === 'cancelled' && $data['status'] !== 'cancelled') {
            foreach ($order['items'] as $item) {
                $product = $this->productModel->getById($item['product_id']);
                
                if ($product['stock'] < $item['quantity']) {
                    $this->sendError('Not enough stock to un-cancel this order', 400);
                }
                
                $this->productModel->updateStock($item['product_id'], -$item['quantity']);
            }
        }
        
        // Update order status
        $success = $this->orderModel->updateStatus($id, $data['status']);
        
        if (!$success) {
            $this->sendError('Failed to update order status', 500);
        }
        
        $updatedOrder = $this->orderModel->getDetails($id);
        
        $this->sendSuccess($updatedOrder, 'Order status updated successfully');
    }

    /**
     * Get orders for the current user
     */
    public function getMyOrders() {
        $this->ensureMethodAllowed('GET');
        
        requireLogin();
        
        $userId = getCurrentUserId();
        $orders = $this->orderModel->getByUser($userId);
        
        $this->sendSuccess($orders, 'Your orders retrieved successfully');
    }

    /**
     * Get order statistics (admin only)
     */
    public function getOrderStatistics() {
        $this->ensureMethodAllowed('GET');
        
        requireAdmin();
        
        $statistics = $this->orderModel->getStatistics();
        
        $this->sendSuccess($statistics, 'Order statistics retrieved successfully');
    }

    /**
     * Create orders from cart
     */
    public function createOrdersFromCart() {
        $this->ensureMethodAllowed('POST');
        
        requireLogin();
        
        $userId = getCurrentUserId();
        
        // Get cart items
        $cartItems = $this->cartModel->getByUser($userId);
        
        if (empty($cartItems)) {
            $this->sendError('Cart is empty', 400);
        }
        
        // Validate stock for all items
        $validatedItems = [];
        foreach ($cartItems as $item) {
            $product = $this->productModel->getById($item['product_id']);
            
            if (!$product || $product['stock'] < $item['quantity']) {
                $this->sendError('Not enough stock for some items', 400);
            }
            
            // Calculate total price for the item
            $item['total_price'] = $product['price'] * $item['quantity'];
            $validatedItems[] = $item;
        }

        // Create the orders
        $orderId = $this->orderModel->create($userId, $validatedItems);
        
        if (!$orderId) {
            $this->sendError('Failed to create orders', 500);
        }

        // Update product stock for each item
        foreach ($validatedItems as $item) {
            $this->productModel->updateStock($item['product_id'], -$item['quantity']);
        }

        // Clear cart after creating orders
        $this->cartModel->clearCart($userId);
        
        // Fetch the created order details
        $orders = [];
        foreach ($validatedItems as $item) {
            $orders[] = $this->orderModel->getDetails($orderId);
        }
        
        $this->sendSuccess($orders, 'Orders created successfully', 201);
    }
}
