# Sundarta - Beauty and Wellness E-Commerce Site

Sundarta is a beauty and wellness e-commerce platform that allows users to browse and purchase beauty products and book wellness services.

## Tech Stack

- HTML
- CSS (Tailwind)
- JavaScript
- PHP
- MySQL

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/sundarta.git
   ```

2. Navigate to the project directory:
   ```
   cd sundarta
   ```

3. Install dependencies:
   ```
   composer install
   ```

4. Create a `.env` file in the root directory with the following content:
   ```
   DB_USER=your_db_username
   DB_PASS=your_db_password
   APP_ENV=development
   APP_TIMEZONE=Asia/Kolkata
   ```

5. Import the database schema:
   ```
   mysql -u your_db_username -p sundarta_db < DB.sql
   ```

6. Start the server:
   ```
   php -S localhost:8000
   ```

7. Open your browser and navigate to `http://localhost:8000`

## API Endpoints

### Users

- **POST /api/users/register** - Register a new user
  - Request: `{ "username": "string", "email": "string", "phone": "string", "password": "string", "role": "string" }`
  - Response: `{ "success": true, "message": "User registered successfully", "data": { "id": "number", "username": "string", "email": "string", "phone": "string", "role": "string" } }`

- **POST /api/users/login** - Login a user
  - Request: `{ "email": "string", "password": "string" }`
  - Response: `{ "success": true, "message": "Login successful", "data": { "user_id": "number", "username": "string", "role": "string" } }`

- **POST /api/users/logout** - Logout a user
  - Response: `{ "success": true, "message": "Logout successful" }`

- **GET /api/users/profile** - Get user profile
  - Response: `{ "success": true, "message": "Profile retrieved successfully", "data": { "id": "number", "username": "string", "email": "string", "phone": "string", "role": "string" } }`

- **PUT /api/users/profile** - Update user profile
  - Request: `{ "username": "string", "email": "string", "phone": "string" }`
  - Response: `{ "success": true, "message": "Profile updated successfully", "data": { "id": "number", "username": "string", "email": "string", "phone": "string", "role": "string" } }`

- **PUT /api/users/password** - Update user password
  - Request: `{ "current_password": "string", "new_password": "string" }`
  - Response: `{ "success": true, "message": "Password updated successfully" }`

- **GET /api/users/list** - Get all users (admin only)
  - Query Parameters: `page`, `per_page`, `role`
  - Response: `{ "success": true, "message": "Users retrieved successfully", "data": { "users": [...], "pagination": { ... } } }`

### Products

- **GET /api/products** - Get all products
  - Query Parameters: `page`, `per_page`, `category`, `search`
  - Response: `{ "success": true, "message": "Products retrieved successfully", "data": { "products": [...], "pagination": { ... } } }`

- **POST /api/products** - Create a new product (admin only)
  - Request: `{ "name": "string", "description": "string", "price": "number", "stock": "number", "category": "number", "images": "array" }`
  - Response: `{ "success": true, "message": "Product created successfully", "data": { ... } }`

- **GET /api/products/detail** - Get a product by ID
  - Query Parameters: `id`
  - Response: `{ "success": true, "message": "Product retrieved successfully", "data": { ... } }`

- **PUT /api/products/detail** - Update a product (admin only)
  - Query Parameters: `id`
  - Request: `{ "name": "string", "description": "string", "price": "number", "stock": "number", "category": "number", "images": "array" }`
  - Response: `{ "success": true, "message": "Product updated successfully", "data": { ... } }`

- **DELETE /api/products/detail** - Delete a product (admin only)
  - Query Parameters: `id`
  - Response: `{ "success": true, "message": "Product deleted successfully" }`

- **GET /api/products/featured** - Get featured products
  - Query Parameters: `limit`
  - Response: `{ "success": true, "message": "Featured products retrieved successfully", "data": [...] }`

- **GET /api/products/search** - Search products
  - Query Parameters: `query`
  - Response: `{ "success": true, "message": "Products search results", "data": [...] }`

- **GET /api/products/category** - Get products by category
  - Query Parameters: `category_id`
  - Response: `{ "success": true, "message": "Products by category retrieved successfully", "data": [...] }`

- **PUT /api/products/stock** - Update product stock (admin only)
  - Query Parameters: `id`
  - Request: `{ "quantity": "number" }`
  - Response: `{ "success": true, "message": "Stock updated successfully", "data": { ... } }`

### Services

- **GET /api/services** - Get all services
  - Query Parameters: `page`, `per_page`, `category`, `user_id`, `search`
  - Response: `{ "success": true, "message": "Services retrieved successfully", "data": { "services": [...], "pagination": { ... } } }`

- **POST /api/services** - Create a new service (seller or admin only)
  - Request: `{ "name": "string", "description": "string", "price": "number", "category": "number", "images": "array" }`
  - Response: `{ "success": true, "message": "Service created successfully", "data": { ... } }`

- **GET /api/services/detail** - Get a service by ID
  - Query Parameters: `id`
  - Response: `{ "success": true, "message": "Service retrieved successfully", "data": { ... } }`

- **PUT /api/services/detail** - Update a service (owner or admin only)
  - Query Parameters: `id`
  - Request: `{ "name": "string", "description": "string", "price": "number", "category": "number", "images": "array" }`
  - Response: `{ "success": true, "message": "Service updated successfully", "data": { ... } }`

- **DELETE /api/services/detail** - Delete a service (owner or admin only)
  - Query Parameters: `id`
  - Response: `{ "success": true, "message": "Service deleted successfully" }`

- **GET /api/services/featured** - Get featured services
  - Query Parameters: `limit`
  - Response: `{ "success": true, "message": "Featured services retrieved successfully", "data": [...] }`

- **GET /api/services/search** - Search services
  - Query Parameters: `query`
  - Response: `{ "success": true, "message": "Services search results", "data": [...] }`

- **GET /api/services/category** - Get services by category
  - Query Parameters: `category_id`
  - Response: `{ "success": true, "message": "Services by category retrieved successfully", "data": [...] }`

- **GET /api/services/seller** - Get services by seller
  - Query Parameters: `seller_id`
  - Response: `{ "success": true, "message": "Services by seller retrieved successfully", "data": [...] }`

- **GET /api/services/my-services** - Get services for the current seller
  - Response: `{ "success": true, "message": "Your services retrieved successfully", "data": [...] }`

### Categories

- **GET /api/categories** - Get all categories
  - Query Parameters: `with_counts`
  - Response: `{ "success": true, "message": "Categories retrieved successfully", "data": [...] }`

- **POST /api/categories** - Create a new category (admin only)
  - Request: `{ "name": "string" }`
  - Response: `{ "success": true, "message": "Category created successfully", "data": { ... } }`

- **GET /api/categories/detail** - Get a category by ID
  - Query Parameters: `id`
  - Response: `{ "success": true, "message": "Category retrieved successfully", "data": { ... } }`

- **PUT /api/categories/detail** - Update a category (admin only)
  - Query Parameters: `id`
  - Request: `{ "name": "string" }`
  - Response: `{ "success": true, "message": "Category updated successfully", "data": { ... } }`

- **DELETE /api/categories/detail** - Delete a category (admin only)
  - Query Parameters: `id`
  - Response: `{ "success": true, "message": "Category deleted successfully" }`

### Reviews

- **GET /api/reviews/product** - Get reviews for a product
  - Query Parameters: `product_id`
  - Response: `{ "success": true, "message": "Product reviews retrieved successfully", "data": { "reviews": [...], "average_rating": "number", "total_reviews": "number" } }`

- **POST /api/reviews/product** - Create a review for a product
  - Request: `{ "product_id": "number", "rating": "number", "review": "string" }`
  - Response: `{ "success": true, "message": "Review created successfully", "data": { "id": "number" } }`

- **GET /api/reviews/service** - Get reviews for a service
  - Query Parameters: `service_id`
  - Response: `{ "success": true, "message": "Service reviews retrieved successfully", "data": { "reviews": [...], "average_rating": "number", "total_reviews": "number" } }`

- **POST /api/reviews/service** - Create a review for a service
  - Request: `{ "service_id": "number", "rating": "number", "review": "string" }`
  - Response: `{ "success": true, "message": "Review created successfully", "data": { "id": "number" } }`

- **PUT /api/reviews/detail** - Update a review
  - Query Parameters: `id`
  - Request: `{ "rating": "number", "review": "string" }`
  - Response: `{ "success": true, "message": "Review updated successfully" }`

- **DELETE /api/reviews/detail** - Delete a review
  - Query Parameters: `id`
  - Response: `{ "success": true, "message": "Review deleted successfully" }`

- **GET /api/reviews/my-reviews** - Get reviews by the current user
  - Response: `{ "success": true, "message": "Your reviews retrieved successfully", "data": [...] }`

### Orders

- **GET /api/orders** - Get all orders (admin only)
  - Query Parameters: `page`, `per_page`, `user_id`, `status`
  - Response: `{ "success": true, "message": "Orders retrieved successfully", "data": { "orders": [...], "pagination": { ... } } }`

- **POST /api/orders** - Create a new order
  - Request: `{ "product_id": "number", "quantity": "number", "from_cart": "boolean" }`
  - Response: `{ "success": true, "message": "Order created successfully", "data": { ... } }`

- **GET /api/orders/detail** - Get an order by ID
  - Query Parameters: `id`
  - Response: `{ "success": true, "message": "Order retrieved successfully", "data": { ... } }`

- **PUT /api/orders/detail** - Update order status (admin only)
  - Query Parameters: `id`
  - Request: `{ "status": "string" }`
  - Response: `{ "success": true, "message": "Order status updated successfully", "data": { ... } }`

- **GET /api/orders/my-orders** - Get orders for the current user
  - Response: `{ "success": true, "message": "Your orders retrieved successfully", "data": [...] }`

- **GET /api/orders/statistics** - Get order statistics (admin only)
  - Response: `{ "success": true, "message": "Order statistics retrieved successfully", "data": { ... } }`

- **POST /api/orders/checkout** - Create orders from cart
  - Response: `{ "success": true, "message": "Orders created successfully", "data": [...] }`

### Cart

- **GET /api/cart** - Get cart items for the current user
  - Response: `{ "success": true, "message": "Cart retrieved successfully", "data": { "items": [...], "summary": { ... } } }`

- **POST /api/cart** - Add an item to the cart
  - Request: `{ "product_id": "number", "quantity": "number" }`
  - Response: `{ "success": true, "message": "Item added to cart successfully", "data": { "items": [...], "summary": { ... } } }`

- **PUT /api/cart/item** - Update cart item quantity
  - Query Parameters: `id`
  - Request: `{ "quantity": "number" }`
  - Response: `{ "success": true, "message": "Cart item updated successfully", "data": { "items": [...], "summary": { ... } } }`

- **DELETE /api/cart/item** - Remove an item from the cart
  - Query Parameters: `id`
  - Response: `{ "success": true, "message": "Item removed from cart successfully", "data": { "items": [...], "summary": { ... } } }`

- **DELETE /api/cart/clear** - Clear the cart
  - Response: `{ "success": true, "message": "Cart cleared successfully" }`

- **GET /api/cart/check-stock** - Check if cart items are in stock
  - Response: `{ "success": true, "message": "Stock check completed", "data": { "out_of_stock_items": [...], "has_stock_issues": "boolean" } }`

## License

This project is licensed under the MIT License. 