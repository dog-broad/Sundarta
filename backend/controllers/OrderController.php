<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class OrderController extends BaseController {
    private $orderModel;
    private $productModel;
    private $serviceModel;
    private $cartModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->serviceModel = new ServiceModel();
        $this->cartModel = new CartModel();
    }

    /**
     * Get all orders (admin only)
     */
    public function getAllOrders() {
        $this->ensureMethodAllowed('GET');
        
        requirePermission('view_orders');
        
        $pagination = $this->getPaginationParams();
        $page = $pagination['page'];
        $perPage = $pagination['per_page'];
        
        $userId = $this->getQueryParam('user_id');
        $status = $this->getQueryParam('status');
        
        $result = $this->orderModel->getWithPagination($page, $perPage, $userId, $status);
        
        $this->sendSuccess($result, 'Orders retrieved successfully');
    }

    /**
     * Get order details
     */
    public function getOrderDetails() {
        $this->ensureMethodAllowed('GET');
        
        requireLogin();
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Order ID is required', 400);
        }
        
        $order = $this->orderModel->getById($id);
        
        if (!$order) {
            $this->sendError('Order not found', 404);
        }
        
        // Check if user has permission to view this order
        $userId = getCurrentUserId();
        $isAdmin = hasPermission('view_orders');
        $isOwner = $order['user_id'] == $userId && hasPermission('view_own_orders');
        
        if (!$isAdmin && !$isOwner) {
            $this->sendError('You do not have permission to view this order', 403);
        }
        
        $orderDetails = $this->orderModel->getDetails($id);
        
        $this->sendSuccess($orderDetails, 'Order details retrieved successfully');
    }

    /**
     * Create a new order
     */
    public function createOrder() {
        $this->ensureMethodAllowed('POST');
        
        requireLogin();
        requirePermission('place_orders');
        
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
        $items = $data['items']; // Array of items, each containing product_id or service_id, quantity
        
        // Validate each item in the order
        $validatedItems = [];
        foreach ($items as $item) {
            if (isset($item['product_id'])) {
                // Product item
                if (!isset($item['quantity'])) {
                    $this->sendError('Quantity is required for each item', 400);
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
            } elseif (isset($item['service_id'])) {
                // Service item
                if (!isset($item['quantity'])) {
                    $this->sendError('Quantity is required for each item', 400);
                }

                $service = $this->serviceModel->getById($item['service_id']);
                
                if (!$service) {
                    $this->sendError('Service not found', 404);
                }

                // Calculate total price for the item
                $item['total_price'] = $service['price'] * $item['quantity'];
                $validatedItems[] = $item;
            } else {
                $this->sendError('Each item must have either product_id or service_id', 400);
            }
        }

        // Create the order
        $orderId = $this->orderModel->create($userId, $validatedItems);
        
        if (!$orderId) {
            $this->sendError('Failed to create order', 500);
        }

        // Update product stock for product items
        foreach ($validatedItems as $item) {
            if (isset($item['product_id'])) {
                $this->productModel->updateStock($item['product_id'], -$item['quantity']);
            }
        }

        // Clear cart if the order is created from the cart
        if (isset($data['from_cart']) && $data['from_cart']) {
            $this->cartModel->clearCart($userId);
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
        
        requirePermission('manage_orders');
        
        $id = $this->getQueryParam('id');
        
        if (!$id) {
            $this->sendError('Order ID is required', 400);
        }
        
        $order = $this->orderModel->getById($id);
        
        if (!$order) {
            $this->sendError('Order not found', 404);
        }
        
        $data = $this->getJsonData();
        
        if (!isset($data['status'])) {
            $this->sendError('Status is required', 400);
        }
        
        $allowedStatuses = ['pending', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($data['status'], $allowedStatuses)) {
            $this->sendError('Invalid status. Allowed values: ' . implode(', ', $allowedStatuses), 400);
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
        requirePermission('view_own_orders');
        
        $userId = getCurrentUserId();
        $orders = $this->orderModel->getByUser($userId);
        
        $this->sendSuccess($orders, 'Orders retrieved successfully');
    }

    /**
     * Get order statistics (admin only)
     */
    public function getOrderStatistics() {
        $this->ensureMethodAllowed('GET');
        
        requirePermission('view_orders');
        
        $statistics = $this->orderModel->getStatistics();
        
        $this->sendSuccess($statistics, 'Order statistics retrieved successfully');
    }

    /**
     * Create orders from cart (checkout)
     */
    public function createOrdersFromCart() {
        $this->ensureMethodAllowed('POST');
        
        requireLogin();
        requirePermission('place_orders');
        
        $userId = getCurrentUserId();
        
        // Get cart items
        $cartItems = $this->cartModel->getByUser($userId);
        
        if (empty($cartItems)) {
            $this->sendError('Your cart is empty', 400);
        }
        
        // Prepare items for order creation
        $orderItems = [];
        
        foreach ($cartItems as $item) {
            if (isset($item['product_id']) && $item['product_id']) {
                // Product item
                $product = $this->productModel->getById($item['product_id']);
                
                if (!$product) {
                    $this->sendError("Product with ID {$item['product_id']} not found", 404);
                }
                
                if ($product['stock'] < $item['quantity']) {
                    $this->sendError("Not enough stock for product: {$product['name']}", 400);
                }
                
                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['subtotal']
                ];
            } elseif (isset($item['service_id']) && $item['service_id']) {
                // Service item
                $service = $this->serviceModel->getById($item['service_id']);
                
                if (!$service) {
                    $this->sendError("Service with ID {$item['service_id']} not found", 404);
                }
                
                $orderItems[] = [
                    'service_id' => $item['service_id'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['subtotal']
                ];
            }
        }
        
        // Create order
        $orderId = $this->orderModel->createFromCart($userId, $orderItems);
        
        if (!$orderId) {
            $this->sendError('Failed to create order', 500);
        }
        
        // Update product stock for product items
        foreach ($orderItems as $item) {
            if (isset($item['product_id'])) {
                $this->productModel->updateStock($item['product_id'], -$item['quantity']);
            }
        }
        
        // Clear cart
        $this->cartModel->clearCart($userId);
        
        $order = $this->orderModel->getDetails($orderId);
        
        $this->sendSuccess($order, 'Order created successfully', 201);
    }
}
