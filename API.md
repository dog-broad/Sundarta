# Sundarta API Documentation

This document provides comprehensive documentation for the Sundarta API, including endpoints, request/response formats, and example curl commands for testing.

## Base URL

All API endpoints are relative to the base URL:

```
http://localhost/sundarta/api/
```

## Authentication

Most endpoints require authentication. The API uses session-based authentication.

### Login

```bash
curl -X POST "http://localhost/sundarta/api/users/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

### Logout

```bash
curl -X POST "http://localhost/sundarta/api/users/logout" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

## User Management

### Register a New User

```bash
curl -X POST "http://localhost/sundarta/api/users/register" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "newuser",
    "email": "newuser@example.com",
    "phone": "1234567890",
    "password": "password123"
  }'
```

### Get User Profile

```bash
curl -X GET "http://localhost/sundarta/api/users/profile" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

### Update User Profile

```bash
curl -X PUT "http://localhost/sundarta/api/users/profile" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "username": "updatedusername",
    "email": "updated@example.com",
    "phone": "9876543210"
  }'
```

### Update Password

```bash
curl -X PUT "http://localhost/sundarta/api/users/password" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "current_password": "password123",
    "new_password": "newpassword123"
  }'
```

### Get All Users (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/users/list" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

Optional query parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10)
- `role`: Filter by role (admin, customer, seller)

Example with query parameters:

```bash
curl -X GET "http://localhost/sundarta/api/users/list?page=1&per_page=20&role=customer" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

## Products

### Get All Products

```bash
curl -X GET "http://localhost/sundarta/api/products" \
  -H "Content-Type: application/json"
```

Optional query parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10)
- `category`: Filter by category ID
- `search`: Search term

Example with query parameters:

```bash
curl -X GET "http://localhost/sundarta/api/products?page=1&per_page=20&category=1&search=shirt" \
  -H "Content-Type: application/json"
```

### Get Product Details

```bash
curl -X GET "http://localhost/sundarta/api/products/detail?id=1" \
  -H "Content-Type: application/json"
```

### Create a New Product (Admin Only)

```bash
curl -X POST "http://localhost/sundarta/api/products" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "New Product",
    "description": "Product description",
    "price": 29.99,
    "stock": 100,
    "category": 1,
    "images": ["image1.jpg", "image2.jpg"]
  }'
```

### Update a Product (Admin Only)

```bash
curl -X PUT "http://localhost/sundarta/api/products/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "Updated Product",
    "description": "Updated description",
    "price": 39.99,
    "stock": 150,
    "category": 2,
    "images": ["image1.jpg", "image3.jpg"]
  }'
```

### Delete a Product (Admin Only)

```bash
curl -X DELETE "http://localhost/sundarta/api/products/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Get Featured Products

```bash
curl -X GET "http://localhost/sundarta/api/products/featured?limit=6" \
  -H "Content-Type: application/json"
```

### Search Products

```bash
curl -X GET "http://localhost/sundarta/api/products/search?query=shirt" \
  -H "Content-Type: application/json"
```

### Get Products by Category

```bash
curl -X GET "http://localhost/sundarta/api/products/category?category_id=1" \
  -H "Content-Type: application/json"
```

### Update Product Stock (Admin Only)

```bash
curl -X PUT "http://localhost/sundarta/api/products/stock?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "quantity": 50
  }'
```

## Categories

### Get All Categories

```bash
curl -X GET "http://localhost/sundarta/api/categories" \
  -H "Content-Type: application/json"
```

### Get Category Details

```bash
curl -X GET "http://localhost/sundarta/api/categories/detail?id=1" \
  -H "Content-Type: application/json"
```

### Create a New Category (Admin Only)

```bash
curl -X POST "http://localhost/sundarta/api/categories" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "New Category",
    "description": "Category description"
  }'
```

### Update a Category (Admin Only)

```bash
curl -X PUT "http://localhost/sundarta/api/categories/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "Updated Category",
    "description": "Updated description"
  }'
```

### Delete a Category (Admin Only)

```bash
curl -X DELETE "http://localhost/sundarta/api/categories/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

## Services

### Get All Services

```bash
curl -X GET "http://localhost/sundarta/api/services" \
  -H "Content-Type: application/json"
```

Optional query parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10)
- `category`: Filter by category ID
- `search`: Search term

### Get Service Details

```bash
curl -X GET "http://localhost/sundarta/api/services/detail?id=1" \
  -H "Content-Type: application/json"
```

### Create a New Service (Admin or Seller Only)

```bash
curl -X POST "http://localhost/sundarta/api/services" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "New Service",
    "description": "Service description",
    "price": 99.99,
    "duration": 60,
    "category": 1,
    "images": ["image1.jpg", "image2.jpg"]
  }'
```

### Update a Service (Admin or Seller Only)

```bash
curl -X PUT "http://localhost/sundarta/api/services/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "Updated Service",
    "description": "Updated description",
    "price": 129.99,
    "duration": 90,
    "category": 2,
    "images": ["image1.jpg", "image3.jpg"]
  }'
```

### Delete a Service (Admin or Seller Only)

```bash
curl -X DELETE "http://localhost/sundarta/api/services/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Get Featured Services

```bash
curl -X GET "http://localhost/sundarta/api/services/featured?limit=6" \
  -H "Content-Type: application/json"
```

### Search Services

```bash
curl -X GET "http://localhost/sundarta/api/services/search?query=massage" \
  -H "Content-Type: application/json"
```

### Get Services by Category

```bash
curl -X GET "http://localhost/sundarta/api/services/category?category_id=1" \
  -H "Content-Type: application/json"
```

### Get Services by Seller

```bash
curl -X GET "http://localhost/sundarta/api/services/seller?seller_id=1" \
  -H "Content-Type: application/json"
```

### Get My Services (Seller Only)

```bash
curl -X GET "http://localhost/sundarta/api/services/my-services" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_seller_session_id"
```

## Cart

### Get Cart

```bash
curl -X GET "http://localhost/sundarta/api/cart" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

### Add to Cart

```bash
curl -X POST "http://localhost/sundarta/api/cart/item" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

### Update Cart Item

```bash
curl -X PUT "http://localhost/sundarta/api/cart/item?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "quantity": 3
  }'
```

### Remove from Cart

```bash
curl -X DELETE "http://localhost/sundarta/api/cart/item?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

### Clear Cart

```bash
curl -X DELETE "http://localhost/sundarta/api/cart/clear" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

### Check Stock

```bash
curl -X GET "http://localhost/sundarta/api/cart/check-stock" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

## Orders

### Get All Orders (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/orders" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

Optional query parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10)
- `user_id`: Filter by user ID
- `status`: Filter by status (pending, shipped, delivered, cancelled)

Example with query parameters:

```bash
curl -X GET "http://localhost/sundarta/api/orders?page=1&per_page=20&status=pending" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Get Order Details

```bash
curl -X GET "http://localhost/sundarta/api/orders/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

### Create a New Order

```bash
curl -X POST "http://localhost/sundarta/api/orders" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

### Update Order Status (Admin Only)

```bash
curl -X PUT "http://localhost/sundarta/api/orders/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "status": "shipped"
  }'
```

### Get My Orders

```bash
curl -X GET "http://localhost/sundarta/api/orders/my-orders" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

### Get Order Statistics (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/orders/statistics" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Checkout (Create Orders from Cart)

```bash
curl -X POST "http://localhost/sundarta/api/orders/checkout" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

## Reviews

### Get Product Reviews

```bash
curl -X GET "http://localhost/sundarta/api/reviews/product?product_id=1" \
  -H "Content-Type: application/json"
```

### Get Service Reviews

```bash
curl -X GET "http://localhost/sundarta/api/reviews/service?service_id=1" \
  -H "Content-Type: application/json"
```

### Get Review Details

```bash
curl -X GET "http://localhost/sundarta/api/reviews/detail?id=1" \
  -H "Content-Type: application/json"
```

### Create a New Review

```bash
curl -X POST "http://localhost/sundarta/api/reviews/product" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "product_id": 1,
    "rating": 5,
    "comment": "Great product, highly recommended!"
  }'
```

### Update a Review

```bash
curl -X PUT "http://localhost/sundarta/api/reviews/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "rating": 4,
    "comment": "Updated review comment"
  }'
```

### Delete a Review

```bash
curl -X DELETE "http://localhost/sundarta/api/reviews/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

### Get My Reviews

```bash
curl -X GET "http://localhost/sundarta/api/reviews/my-reviews" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

## Response Format

All API responses follow a standard format:

### Success Response

```json
{
  "success": true,
  "message": "Success message",
  "data": {
    // Response data
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Optional validation errors
  }
}
```

## HTTP Status Codes

- `200 OK`: Request succeeded
- `201 Created`: Resource created successfully
- `400 Bad Request`: Invalid request parameters
- `401 Unauthorized`: Authentication required
- `403 Forbidden`: Permission denied
- `404 Not Found`: Resource not found
- `405 Method Not Allowed`: HTTP method not allowed
- `409 Conflict`: Resource conflict (e.g., email already exists)
- `500 Internal Server Error`: Server error 