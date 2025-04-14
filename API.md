# Sundarta API Documentation

This document provides comprehensive documentation for the Sundarta API, including endpoints, request/response formats, and example curl commands for testing.

## Base URL

All API endpoints are relative to the base URL:

```
http://localhost/sundarta/api/
```

## Authentication

Most endpoints require authentication. The API uses session-based authentication with role-based access control.

### Login

```bash
curl -X POST "http://localhost/sundarta/api/users/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

Upon successful login, the response will include the user's roles and permissions.

### Logout

```bash
curl -X POST "http://localhost/sundarta/api/users/logout" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

## Permissions System

Sundarta uses a role-based access control system. Each user can have multiple roles, and each role can have multiple permissions. Access to endpoints is controlled by checking if the user has the required permission.

Key permissions include:

- `view_dashboard`: Can view the admin dashboard
- `manage_users`: Can create, update, and delete users
- `view_users`: Can view user details
- `manage_products`: Can create, update, and delete products
- `view_products`: Can view product details
- `manage_services`: Can create, update, and delete all services
- `manage_own_services`: Can create, update, and delete own services
- `view_services`: Can view service details
- `manage_categories`: Can create, update, and delete categories
- `view_categories`: Can view category details
- `manage_orders`: Can update and delete orders
- `view_orders`: Can view all order details
- `view_own_orders`: Can view own order details
- `place_orders`: Can place orders
- `manage_reviews`: Can update and delete all reviews
- `view_reviews`: Can view review details
- `manage_roles`: Can create, update, and delete roles
- `assign_roles`: Can assign roles to users
- `view_roles`: Can view role details
- `manage_permissions`: Can create, update, and delete permissions
- `assign_permissions`: Can assign permissions to roles
- `view_permissions`: Can view permission details

## User Management

### Register a New User

```bash
curl -X POST "http://localhost/sundarta/api/users/register" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "newuser",
    "email": "newuser@example.com",
    "phone": "1234567890",
    "password": "password123",
    "avatar": "https://example.com/avatars/default.jpg",
    "roles": ["customer", "seller"]
  }'
```

Note: The `roles` and `avatar` fields are optional. If not provided, the user will be assigned the "customer" role by default. You can specify roles by name (recommended) or by ID (for backward compatibility).

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
    "phone": "9876543210",
    "avatar": "https://example.com/avatars/user123.jpg"
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

### Get User by ID (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/users/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Update User (Admin Only)

```bash
curl -X PUT "http://localhost/sundarta/api/users/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "username": "updatedusername",
    "email": "updated@example.com",
    "phone": "9876543210",
    "avatar": "https://example.com/avatars/user123.jpg",
    "is_active": true,
    "roles": ["customer", "seller"]
  }'
```

Note: The `roles` field is optional. You can specify roles by name (recommended) or by ID (for backward compatibility). Regular users cannot modify their own roles.

### Delete User (Admin Only)

```bash
curl -X DELETE "http://localhost/sundarta/api/users/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Assign Roles to User (Admin Only)

```bash
curl -X POST "http://localhost/sundarta/api/users/roles?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "roles": ["customer", "seller"]
  }'
```

Note: You can specify roles by name (recommended) or by ID (for backward compatibility).

## Role Management

### Get All Roles (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/roles" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Get Role by ID (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/roles/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Create a New Role (Admin Only)

```bash
curl -X POST "http://localhost/sundarta/api/roles" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "editor",
    "description": "Content editor role",
    "permissions": ["view_products", "edit_products", "view_categories"]
  }'
```

Note: The `permissions` field is optional. You can specify permissions by name (recommended) or by ID (for backward compatibility).

### Update a Role (Admin Only)

```bash
curl -X PUT "http://localhost/sundarta/api/roles/detail?id=4" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "content_editor",
    "description": "Content editor with limited access",
    "permissions": ["view_products", "edit_products", "view_categories", "edit_categories"]
  }'
```

Note: The `permissions` field is optional. You can specify permissions by name (recommended) or by ID (for backward compatibility).

### Delete a Role (Admin Only)

```bash
curl -X DELETE "http://localhost/sundarta/api/roles/detail?id=4" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Get Role Permissions (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/roles/permissions?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Assign Permissions to Role (Admin Only)

```bash
curl -X POST "http://localhost/sundarta/api/roles/permissions?id=3" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "permissions": ["view_products", "edit_products", "view_categories", "edit_categories", "view_orders"]
  }'
```

Note: You can specify permissions by name (recommended) or by ID (for backward compatibility).

### Get Users with Role (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/roles/users?id=2" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

## Permission Management

### Get All Permissions (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/permissions" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Get Permission by ID (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/permissions/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Create a New Permission (Admin Only)

```bash
curl -X POST "http://localhost/sundarta/api/permissions" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "export_data",
    "description": "Can export data from the system"
  }'
```

### Update a Permission (Admin Only)

```bash
curl -X PUT "http://localhost/sundarta/api/permissions/detail?id=23" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "export_system_data",
    "description": "Can export data from the system in various formats"
  }'
```

### Delete a Permission (Admin Only)

```bash
curl -X DELETE "http://localhost/sundarta/api/permissions/detail?id=23" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Get Roles with Permission (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/permissions/roles?id=5" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Check User Permission

```bash
curl -X GET "http://localhost/sundarta/api/permissions/check?permission=view_products" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

Check permission for another user (Admin Only):

```bash
curl -X GET "http://localhost/sundarta/api/permissions/check?permission=view_products&user_id=2" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

### Get User Permissions

```bash
curl -X GET "http://localhost/sundarta/api/permissions/user" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

Get permissions for another user (Admin Only):

```bash
curl -X GET "http://localhost/sundarta/api/permissions/user?user_id=2" \
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

Note: Requires the `manage_products` permission.

### Delete a Product (Admin Only)

```bash
curl -X DELETE "http://localhost/sundarta/api/products/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

Note: Requires the `manage_products` permission.

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

Note: Requires the `manage_categories` permission.

### Update a Category (Admin Only)

```bash
curl -X PUT "http://localhost/sundarta/api/categories/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "name": "Updated Category"
  }'
```

Note: Requires the `manage_categories` permission.

### Delete a Category (Admin Only)

```bash
curl -X DELETE "http://localhost/sundarta/api/categories/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

Note: Requires the `manage_categories` permission. Cannot delete categories with associated products or services.

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
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "name": "New Service",
    "description": "Service description",
    "price": 49.99,
    "category": 1,
    "images": ["image1.jpg", "image2.jpg"]
  }'
```

Note: Requires either the `manage_services` permission (for all services) or the `manage_own_services` permission (for creating your own services).

### Update a Service (Owner or Admin Only)

```bash
curl -X PUT "http://localhost/sundarta/api/services/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "name": "Updated Service",
    "description": "Updated description",
    "price": 59.99,
    "category": 2,
    "images": ["image1.jpg", "image3.jpg"]
  }'
```

Note: Requires either the `manage_services` permission (for all services) or the `manage_own_services` permission (for your own services).

### Delete a Service (Owner or Admin Only)

```bash
curl -X DELETE "http://localhost/sundarta/api/services/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

Note: Requires either the `manage_services` permission (for all services) or the `manage_own_services` permission (for your own services).

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
curl -X GET "http://localhost/sundarta/api/services/seller?user_id=3" \
  -H "Content-Type: application/json"
```

### Get My Services (Seller Only)

```bash
curl -X GET "http://localhost/sundarta/api/services/my-services" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_seller_session_id"
```

Note: Requires either the `manage_services` permission (for all services) or the `manage_own_services` permission (for your own services).

## Cart

### Get Cart

```bash
curl -X GET "http://localhost/sundarta/api/cart" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

Note: Requires the `place_orders` permission. The response will include both product and service items in the cart.

### Add Product to Cart

```bash
curl -X POST "http://localhost/sundarta/api/cart/item" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

Note: Requires the `place_orders` permission.

### Add Service to Cart

```bash
curl -X POST "http://localhost/sundarta/api/cart/item" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "service_id": 1,
    "quantity": 1
  }'
```

Note: Requires the `place_orders` permission.

### Update Cart Item

```bash
curl -X PUT "http://localhost/sundarta/api/cart/item?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "quantity": 3
  }'
```

Note: Requires the `place_orders` permission.

### Remove from Cart

```bash
curl -X DELETE "http://localhost/sundarta/api/cart/item?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

Note: Requires the `place_orders` permission.

### Clear Cart

```bash
curl -X DELETE "http://localhost/sundarta/api/cart/clear" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

Note: Requires the `place_orders` permission.

### Check Stock

```bash
curl -X GET "http://localhost/sundarta/api/cart/check-stock" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

Note: Requires the `place_orders` permission. This only checks stock for product items in the cart.

## Orders

### Get All Orders (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/orders" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```
Note: Requires the `view_orders` permission.

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

Note: Requires either the `view_orders` permission (for all orders) or the `view_own_orders` permission (for your own orders).

### Create Order with Products

```bash
curl -X POST "http://localhost/sundarta/api/orders" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "items": [
      {
        "product_id": 1,
        "quantity": 2
      },
      {
        "product_id": 3,
        "quantity": 1
      }
    ]
  }'
```

Note: Requires the `place_orders` permission.

### Create Order with Services

```bash
curl -X POST "http://localhost/sundarta/api/orders" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "items": [
      {
        "service_id": 1,
        "quantity": 1
      },
      {
        "service_id": 2,
        "quantity": 2
      }
    ]
  }'
```

Note: Requires the `place_orders` permission.

### Create Mixed Order (Products and Services)

```bash
curl -X POST "http://localhost/sundarta/api/orders" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "items": [
      {
        "product_id": 1,
        "quantity": 2
      },
      {
        "service_id": 3,
        "quantity": 1
      }
    ]
  }'
```

Note: Requires the `place_orders` permission.

### Update Order Status (Admin Only)

```bash
curl -X PUT "http://localhost/sundarta/api/orders/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id" \
  -d '{
    "status": "shipped"
  }'
```

Note: Requires the `manage_orders` permission.

### Get My Orders

```bash
curl -X GET "http://localhost/sundarta/api/orders/my-orders" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

Note: Requires the `view_own_orders` permission.

### Get Order Statistics (Admin Only)

```bash
curl -X GET "http://localhost/sundarta/api/orders/statistics" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_admin_session_id"
```

Note: Requires the `view_orders` permission.

### Checkout (Create Order from Cart)

```bash
curl -X POST "http://localhost/sundarta/api/orders/checkout" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

Note: Requires the `place_orders` permission. This will create an order with all items (products and services) in your cart.

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

### Create Product Review

```bash
curl -X POST "http://localhost/sundarta/api/reviews/product" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "product_id": 1,
    "rating": 5,
    "review": "Excellent product, highly recommended!"
  }'
```

### Create Service Review

```bash
curl -X POST "http://localhost/sundarta/api/reviews/service" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "service_id": 1,
    "rating": 5,
    "review": "Excellent service, highly recommended!"
  }'
```

### Update Review

```bash
curl -X PUT "http://localhost/sundarta/api/reviews/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id" \
  -d '{
    "rating": 4,
    "review": "Good product, but could be better."
  }'
```

Note: Requires either the `manage_reviews` permission (for all reviews) or being the owner of the review.

### Delete Review

```bash
curl -X DELETE "http://localhost/sundarta/api/reviews/detail?id=1" \
  -H "Content-Type: application/json" \
  --cookie "PHPSESSID=your_session_id"
```

Note: Requires either the `manage_reviews` permission (for all reviews) or being the owner of the review.

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