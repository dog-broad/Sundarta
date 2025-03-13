-- ===========================================
-- Sundarta Database Setup for Beauty & Wellness Site
-- ===========================================
-- Date: March 2025
-- Description: This script sets up the database
-- for the Sundarta Beauty and Wellness site.
-- It includes tables for Users, Products, Services,
-- Categories, Reviews, Orders, and Cart.
-- ===========================================

-- Create the database
CREATE DATABASE IF NOT EXISTS sundarta_db;
USE sundarta_db;

-- ===========================================
-- Table: roles
-- This table stores information about user roles.
-- Each role will have a unique ID, name, and description.
-- ===========================================

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique role ID
    name VARCHAR(50) NOT NULL UNIQUE,                -- Role name (unique)
    description TEXT,                                -- Role description
    is_system BOOLEAN DEFAULT FALSE,                 -- Whether this is a system role (cannot be deleted)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when role is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Timestamp when role is updated
);

-- ===========================================
-- Table: permissions
-- This table stores information about permissions.
-- Each permission will have a unique ID, name, and description.
-- ===========================================

CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique permission ID
    name VARCHAR(100) NOT NULL UNIQUE,               -- Permission name (unique)
    description TEXT,                                -- Permission description
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when permission is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Timestamp when permission is updated
);

-- ===========================================
-- Table: role_permissions
-- This table maps roles to permissions (many-to-many).
-- ===========================================

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT NOT NULL,                            -- Role ID (foreign key)
    permission_id INT NOT NULL,                      -- Permission ID (foreign key)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when mapping is created
    PRIMARY KEY (role_id, permission_id),            -- Composite primary key
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE, -- Foreign key linking to the roles table
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE -- Foreign key linking to the permissions table
);

-- ===========================================
-- Table: users
-- This table stores information about the users.
-- Each user will have a unique ID, username, email.
-- Passwords will be stored in a hashed form for security.
-- ===========================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique user ID
    username VARCHAR(255) NOT NULL,                   -- User's username
    email VARCHAR(255) UNIQUE NOT NULL,               -- User's email, unique
    phone VARCHAR(15),                                -- User's phone number
    password VARCHAR(255) NOT NULL,                   -- User's password (hashed)
    is_active BOOLEAN DEFAULT TRUE,                   -- Whether the user is active
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when user is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Timestamp when user is updated
);

-- ===========================================
-- Table: user_roles
-- This table maps users to roles (many-to-many).
-- ===========================================

CREATE TABLE IF NOT EXISTS user_roles (
    user_id INT NOT NULL,                            -- User ID (foreign key)
    role_id INT NOT NULL,                            -- Role ID (foreign key)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when mapping is created
    PRIMARY KEY (user_id, role_id),                  -- Composite primary key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, -- Foreign key linking to the users table
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE -- Foreign key linking to the roles table
);

-- ===========================================
-- Table: category
-- This table stores information about product/service categories.
-- ===========================================

CREATE TABLE IF NOT EXISTS category (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique category ID
    name VARCHAR(255) NOT NULL,                       -- Category name
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when category is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Timestamp when category is updated
);

-- ===========================================
-- Table: products
-- This table stores information about products listed on the site.
-- It includes product name, description, price, stock, and category.
-- ===========================================

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique product ID
    name VARCHAR(255) NOT NULL,                       -- Product name
    description TEXT NOT NULL,                        -- Product description
    price DECIMAL(10, 2) NOT NULL,                    -- Product price
    stock INT NOT NULL,                               -- Available stock quantity
    category INT NOT NULL,                            -- Category ID (foreign key)
    images JSON,                                      -- Product image URLs in JSON format
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when product is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Timestamp when product is updated
    FOREIGN KEY (category) REFERENCES category(id)    -- Foreign key linking to the category table
);

-- ===========================================
-- Table: services
-- This table stores information about beauty and wellness services.
-- It includes service name, description, price, and category.
-- ===========================================

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique service ID
    name VARCHAR(255) NOT NULL,                       -- Service name
    user_id INT NOT NULL,                             -- User ID (foreign key)
    description TEXT NOT NULL,                        -- Service description
    price DECIMAL(10, 2) NOT NULL,                    -- Service price
    images JSON,                                      -- Service image URLs in JSON format
    category INT NOT NULL,                            -- Category ID (foreign key)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when service is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Timestamp when service is updated
    FOREIGN KEY (user_id) REFERENCES users(id),       -- Foreign key linking to the users table
    FOREIGN KEY (category) REFERENCES category(id)    -- Foreign key linking to the category table
);

-- ===========================================
-- Table: reviews
-- This table stores product and service reviews from users.
-- Reviews include a rating (1-5), review text, and can be linked to either a product or service.
-- ===========================================

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique review ID
    user_id INT NOT NULL,                             -- User ID (foreign key)
    product_id INT,                                   -- Product ID (nullable, foreign key)
    service_id INT,                                   -- Service ID (nullable, foreign key)
    rating INT NOT NULL,                              -- Rating from 1 to 5
    review TEXT,                                      -- Review text
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when review is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Timestamp when review is updated
    FOREIGN KEY (user_id) REFERENCES users(id),       -- Foreign key linking to the users table
    FOREIGN KEY (product_id) REFERENCES products(id), -- Foreign key linking to the products table
    FOREIGN KEY (service_id) REFERENCES services(id)  -- Foreign key linking to the services table
);

-- ===========================================
-- Table: orders
-- This table stores information about customer orders, including the user and order status.
-- It includes timestamps.
-- ===========================================

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique order ID
    user_id INT NOT NULL,                             -- User ID (foreign key)
    status ENUM('pending', 'shipped', 'delivered', 'cancelled') NOT NULL, -- Order status
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when order is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Timestamp when order is updated
    FOREIGN KEY (user_id) REFERENCES users(id)       -- Foreign key linking to the users table
);

-- ===========================================
-- Table: order_items
-- This table stores information about the items in each order, including product details and quantity.
-- It includes timestamps.
-- ===========================================

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique order item ID
    order_id INT NOT NULL,                            -- Order ID (foreign key)
    product_id INT,                          -- Product ID (nullable, foreign key)
    service_id INT,                                   -- Service ID (nullable, foreign key)
    quantity INT NOT NULL,                            -- Quantity of product ordered
    total_price DECIMAL(10, 2) NOT NULL,              -- Total price of the order item
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when order item is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Timestamp when order item is updated
    FOREIGN KEY (order_id) REFERENCES orders(id),     -- Foreign key linking to the orders table
    FOREIGN KEY (product_id) REFERENCES products(id),  -- Foreign key linking to the products table
    FOREIGN KEY (service_id) REFERENCES services(id)  -- Foreign key linking to the services table
);

-- ===========================================
-- Table: cart
-- This table stores information about the items added to the cart by users.
-- It links users and products, and stores the quantity of each product.
-- ===========================================

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique cart item ID
    user_id INT NOT NULL,                             -- User ID (foreign key)
    product_id INT,                                   -- Product ID (nullable, foreign key)
    service_id INT,                                   -- Service ID (nullable, foreign key)
    quantity INT NOT NULL,                            -- Quantity of the item in the cart
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when the item was added to the cart
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Timestamp when the cart is updated
    FOREIGN KEY (user_id) REFERENCES users(id),       -- Foreign key linking to the users table
    FOREIGN KEY (product_id) REFERENCES products(id), -- Foreign key linking to the products table
    FOREIGN KEY (service_id) REFERENCES services(id), -- Foreign key linking to the services table
    CHECK (product_id IS NOT NULL OR service_id IS NOT NULL) -- Ensure at least one of product_id or service_id is not null
);

-- ===========================================
-- Sample Data Inserts
-- You can use the following queries to add sample data for testing purposes.
-- ===========================================

-- Insert default roles
INSERT INTO roles (name, description, is_system) VALUES 
    ('admin', 'Administrator with full access to all features', TRUE),
    ('customer', 'Regular customer with basic access', TRUE),
    ('seller', 'Service provider with ability to manage their services', TRUE),
    ('manager', 'Manager with access to manage products and orders', FALSE),
    ('support', 'Customer support with limited access to help customers', FALSE);

-- Insert default permissions
INSERT INTO permissions (name, description) VALUES
    ('view_dashboard', 'Can view the admin dashboard'),
    ('manage_users', 'Can create, update, and delete users'),
    ('view_users', 'Can view user details'),
    ('manage_products', 'Can create, update, and delete products'),
    ('view_products', 'Can view product details'),
    ('manage_services', 'Can create, update, and delete services'),
    ('view_services', 'Can view service details'),
    ('manage_categories', 'Can create, update, and delete categories'),
    ('view_categories', 'Can view category details'),
    ('manage_orders', 'Can update and delete orders'),
    ('view_orders', 'Can view order details'),
    ('manage_reviews', 'Can update and delete reviews'),
    ('view_reviews', 'Can view review details'),
    ('manage_own_services', 'Can manage their own services'),
    ('view_own_orders', 'Can view their own orders'),
    ('place_orders', 'Can place orders'),
    ('manage_roles', 'Can create, update, and delete roles'),
    ('assign_roles', 'Can assign roles to users'),
    ('view_roles', 'Can view role details'),
    ('manage_permissions', 'Can create, update, and delete permissions'),
    ('assign_permissions', 'Can assign permissions to roles'),
    ('view_permissions', 'Can view permission details');

-- Assign permissions to roles
-- Admin role permissions (all permissions)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

-- Customer role permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions WHERE name IN (
    'view_products', 
    'view_services', 
    'view_categories', 
    'view_own_orders', 
    'place_orders'
);

-- Seller role permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions WHERE name IN (
    'view_products', 
    'view_services', 
    'view_categories', 
    'view_own_orders', 
    'place_orders', 
    'manage_own_services'
);

-- Manager role permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, id FROM permissions WHERE name IN (
    'view_dashboard',
    'view_users',
    'manage_products',
    'view_products',
    'view_services',
    'manage_categories',
    'view_categories',
    'manage_orders',
    'view_orders',
    'view_reviews'
);

-- Support role permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, id FROM permissions WHERE name IN (
    'view_dashboard',
    'view_users',
    'view_products',
    'view_services',
    'view_categories',
    'view_orders',
    'view_reviews'
);

-- Insert sample users (hashed passwords should be used in real scenarios)
INSERT INTO users (username, email, phone, password) 
VALUES 
    ('admin', 'admin@sundarta.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: password
    ('customer1', 'customer1@sundarta.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
    ('seller1', 'seller1@sundarta.com', '5551234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
    ('manager1', 'manager1@sundarta.com', '5559876543', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
    ('support1', 'support1@sundarta.com', '5554567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
    ('customer2', 'customer2@sundarta.com', '5552345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
    ('seller2', 'seller2@sundarta.com', '5553456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Assign roles to users
INSERT INTO user_roles (user_id, role_id) VALUES
    (1, 1), -- admin has admin role
    (2, 2), -- customer1 has customer role
    (3, 3), -- seller1 has seller role
    (4, 4), -- manager1 has manager role
    (5, 5), -- support1 has support role
    (6, 2), -- customer2 has customer role
    (7, 3); -- seller2 has seller role

-- Insert sample categories
INSERT INTO category (name) VALUES 
    ('Skincare'), 
    ('Haircare'), 
    ('Makeup'),
    ('Wellness'),
    ('Fragrance');

-- Insert sample products
INSERT INTO products (name, description, price, stock, category, images) 
VALUES 
    ('Anti-Aging Cream', 'A cream that helps reduce wrinkles', 25.99, 100, 1, '["https://dummyimage.com/600x400/000/fff&text=Anti+Aging+Cream+1", "https://dummyimage.com/600x400/111/fff&text=Anti+Aging+Cream+2"]'),
    ('Shampoo', 'A hair nourishing shampoo', 10.99, 50, 2, '["https://dummyimage.com/600x400/222/fff&text=Shampoo+1", "https://dummyimage.com/600x400/333/fff&text=Shampoo+2"]'),
    ('Lipstick', 'A long-lasting lipstick', 15.99, 200, 3, '["https://dummyimage.com/600x400/444/fff&text=Lipstick+1", "https://dummyimage.com/600x400/555/fff&text=Lipstick+2"]'),
    ('Essential Oil', 'A soothing oil for relaxation', 8.99, 150, 4, '["https://dummyimage.com/600x400/666/fff&text=Essential+Oil+1", "https://dummyimage.com/600x400/777/fff&text=Essential+Oil+2"]'),
    ('Perfume', 'A fresh floral fragrance', 20.99, 100, 5, '["https://dummyimage.com/600x400/888/fff&text=Perfume+1", "https://dummyimage.com/600x400/999/fff&text=Perfume+2"]'),
    ('Facial Cleanser', 'A gentle facial cleanser for all skin types', 12.99, 80, 1, '["https://dummyimage.com/600x400/aaa/fff&text=Facial+Cleanser+1", "https://dummyimage.com/600x400/bbb/fff&text=Facial+Cleanser+2"]'),
    ('Hair Conditioner', 'A deep conditioning treatment for damaged hair', 14.99, 60, 2, '["https://dummyimage.com/600x400/ccc/fff&text=Hair+Conditioner+1", "https://dummyimage.com/600x400/ddd/fff&text=Hair+Conditioner+2"]'),
    ('Foundation', 'A full-coverage foundation for all skin tones', 18.99, 120, 3, '["https://dummyimage.com/600x400/eee/fff&text=Foundation+1", "https://dummyimage.com/600x400/fff/000&text=Foundation+2"]'),
    ('Massage Oil', 'A relaxing massage oil with lavender scent', 16.99, 90, 4, '["https://dummyimage.com/600x400/111/fff&text=Massage+Oil+1", "https://dummyimage.com/600x400/222/fff&text=Massage+Oil+2"]'),
    ('Cologne', 'A masculine cologne with woody notes', 24.99, 70, 5, '["https://dummyimage.com/600x400/333/fff&text=Cologne+1", "https://dummyimage.com/600x400/444/fff&text=Cologne+2"]');

-- Insert sample services
INSERT INTO services (name, user_id, description, price, category, images) 
VALUES 
    ('Facial Treatment', 3, 'A rejuvenating facial treatment', 50.00, 1, '["https://dummyimage.com/600x400/666/fff&text=Facial+Treatment+1", "https://dummyimage.com/600x400/777/fff&text=Facial+Treatment+2"]'),
    ('Haircut', 3, 'A classic haircut', 15.00, 2, '["https://dummyimage.com/600x400/888/fff&text=Haircut+1", "https://dummyimage.com/600x400/999/fff&text=Haircut+2"]'),
    ('Makeup', 3, 'A professional makeup application', 40.00, 3, '["https://dummyimage.com/600x400/111/fff&text=Makeup+1", "https://dummyimage.com/600x400/222/fff&text=Makeup+2"]'),
    ('Massage', 3, 'A relaxing massage', 30.00, 4, '["https://dummyimage.com/600x400/333/fff&text=Massage+1", "https://dummyimage.com/600x400/444/fff&text=Massage+2"]'),
    ('Fragrance', 3, 'A personalized fragrance consultation', 25.00, 5, '["https://dummyimage.com/600x400/555/fff&text=Fragrance+1", "https://dummyimage.com/600x400/666/fff&text=Fragrance+2"]'),
    ('Deep Cleansing Facial', 7, 'A deep cleansing facial for problematic skin', 60.00, 1, '["https://dummyimage.com/600x400/777/fff&text=Deep+Cleansing+Facial+1", "https://dummyimage.com/600x400/888/fff&text=Deep+Cleansing+Facial+2"]'),
    ('Hair Coloring', 7, 'Professional hair coloring service', 45.00, 2, '["https://dummyimage.com/600x400/999/fff&text=Hair+Coloring+1", "https://dummyimage.com/600x400/aaa/fff&text=Hair+Coloring+2"]'),
    ('Bridal Makeup', 7, 'Complete bridal makeup package', 80.00, 3, '["https://dummyimage.com/600x400/bbb/fff&text=Bridal+Makeup+1", "https://dummyimage.com/600x400/ccc/fff&text=Bridal+Makeup+2"]'),
    ('Hot Stone Massage', 7, 'Relaxing hot stone massage therapy', 50.00, 4, '["https://dummyimage.com/600x400/ddd/fff&text=Hot+Stone+Massage+1", "https://dummyimage.com/600x400/eee/fff&text=Hot+Stone+Massage+2"]'),
    ('Custom Perfume Creation', 7, 'Create your own custom perfume blend', 70.00, 5, '["https://dummyimage.com/600x400/fff/000&text=Custom+Perfume+1", "https://dummyimage.com/600x400/000/fff&text=Custom+Perfume+2"]');

-- Insert sample reviews
INSERT INTO reviews (product_id, user_id, rating, review) VALUES 
    (1, 2, 5, 'Outstanding product, exceeded expectations.'),
    (2, 2, 4, 'Good product, would recommend.'),
    (3, 2, 3, 'Product is okay.'),
    (4, 2, 5, 'Excellent product, exceeded expectations.'),
    (5, 2, 2, 'Not satisfied with the product.'),
    (6, 6, 5, 'Amazing cleanser, my skin feels so fresh!'),
    (7, 6, 4, 'Great conditioner, made my hair so soft.'),
    (8, 6, 3, 'Decent foundation, but not perfect for my skin tone.'),
    (9, 6, 5, 'Love this massage oil, smells wonderful!'),
    (10, 6, 4, 'Nice cologne, my husband loves it.');

INSERT INTO reviews (service_id, user_id, rating, review) VALUES 
    (1, 2, 5, 'Outstanding service, exceeded expectations.'),
    (2, 2, 4, 'Good service, would recommend.'),
    (3, 2, 3, 'Service is okay.'),
    (4, 2, 5, 'Excellent service, exceeded expectations.'),
    (5, 2, 2, 'Not satisfied with the service.'),
    (6, 6, 5, 'Best facial I''ve ever had!'),
    (7, 6, 4, 'Great hair coloring service, love my new look.'),
    (8, 6, 5, 'Perfect bridal makeup, everyone complimented me!'),
    (9, 6, 5, 'The hot stone massage was so relaxing.'),
    (10, 6, 4, 'Loved creating my own perfume, very unique experience.');

-- Insert sample orders
INSERT INTO orders (user_id, status) VALUES
    (2, 'delivered'),
    (2, 'shipped'),
    (2, 'pending'),
    (6, 'delivered'),
    (6, 'cancelled');

-- Insert sample order items
INSERT INTO order_items (order_id, product_id, quantity, total_price) VALUES
    (1, 1, 2, 51.98),
    (1, 3, 1, 15.99),
    (2, 2, 1, 10.99),
    (2, 5, 1, 20.99),
    (3, 4, 3, 26.97),
    (4, 6, 1, 12.99),
    (4, 7, 1, 14.99),
    (5, 8, 1, 18.99);

-- Insert sample cart items
INSERT INTO cart (user_id, product_id, quantity) VALUES
    (2, 4, 1),
    (2, 5, 2),
    (6, 9, 1),
    (6, 10, 1);

-- ===========================================
-- END OF SCRIPT
-- =========================================== 