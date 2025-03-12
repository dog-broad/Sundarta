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
-- Table: users
-- This table stores information about the users.
-- Each user will have a unique ID, username, email, and role.
-- Passwords will be stored in a hashed form for security.
-- ===========================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique user ID
    username VARCHAR(255) NOT NULL,                   -- User's username
    email VARCHAR(255) UNIQUE NOT NULL,               -- User's email, unique
    phone VARCHAR(15),                                -- User's phone number
    role ENUM('admin', 'customer', 'seller') NOT NULL, -- Role of the user (admin, customer, seller)
    password VARCHAR(255) NOT NULL,                   -- User's password (hashed)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when user is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Timestamp when user is updated
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
    product_id INT NOT NULL,                          -- Product ID (foreign key)
    quantity INT NOT NULL,                            -- Quantity of product ordered
    total_price DECIMAL(10, 2) NOT NULL,              -- Total price of the order item
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when order item is created
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Timestamp when order item is updated
    FOREIGN KEY (order_id) REFERENCES orders(id),     -- Foreign key linking to the orders table
    FOREIGN KEY (product_id) REFERENCES products(id)  -- Foreign key linking to the products table
);

-- ===========================================
-- Table: cart
-- This table stores information about the items added to the cart by users.
-- It links users and products, and stores the quantity of each product.
-- ===========================================

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique cart item ID
    user_id INT NOT NULL,                             -- User ID (foreign key)
    product_id INT NOT NULL,                          -- Product ID (foreign key)
    quantity INT NOT NULL,                            -- Quantity of the product in the cart
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Timestamp when the item was added to the cart
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Timestamp when the cart is updated
    FOREIGN KEY (user_id) REFERENCES users(id),       -- Foreign key linking to the users table
    FOREIGN KEY (product_id) REFERENCES products(id)  -- Foreign key linking to the products table
);

-- ===========================================
-- Sample Data Inserts
-- You can use the following queries to add sample data for testing purposes.
-- ===========================================
-- Insert sample users (hashed passwords should be used in real scenarios)
INSERT INTO users (username, email, phone, role, password) 
VALUES 
    ('admin', 'admin@sundarta.com', '1234567890', 'admin', 'hashed_password'),
    ('customer1', 'customer1@sundarta.com', '9876543210', 'customer', 'hashed_password');

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
    ('Perfume', 'A fresh floral fragrance', 20.99, 100, 5, '["https://dummyimage.com/600x400/888/fff&text=Perfume+1", "https://dummyimage.com/600x400/999/fff&text=Perfume+2"]');

-- Insert sample services
INSERT INTO services (name, user_id, description, price, category, images) 
VALUES 
    ('Facial Treatment', 2, 'A rejuvenating facial treatment', 50.00, 1, '["https://dummyimage.com/600x400/666/fff&text=Facial+Treatment+1", "https://dummyimage.com/600x400/777/fff&text=Facial+Treatment+2"]'),
    ('Haircut', 2, 'A classic haircut', 15.00, 2, '["https://dummyimage.com/600x400/888/fff&text=Haircut+1", "https://dummyimage.com/600x400/999/fff&text=Haircut+2"]'),
    ('Makeup', 2, 'A professional makeup application', 40.00, 3, '["https://dummyimage.com/600x400/111/fff&text=Makeup+1", "https://dummyimage.com/600x400/222/fff&text=Makeup+2"]'),
    ('Massage', 2, 'A relaxing massage', 30.00, 4, '["https://dummyimage.com/600x400/333/fff&text=Massage+1", "https://dummyimage.com/600x400/444/fff&text=Massage+2"]'),
    ('Fragrance', 2, 'A personalized fragrance consultation', 25.00, 5, '["https://dummyimage.com/600x400/555/fff&text=Fragrance+1", "https://dummyimage.com/600x400/666/fff&text=Fragrance+2"]');

-- Insert sample reviews
INSERT INTO reviews (product_id, user_id, rating, review) VALUES 
    (1, 2, 5, 'Outstanding product, exceeded expectations.'),
    (2, 2, 4, 'Good product, would recommend.'),
    (3, 2, 3, 'Product is okay.'),
    (4, 2, 5, 'Excellent product, exceeded expectations.'),
    (5, 2, 2, 'Not satisfied with the product.'),
    (1, 2, 5, 'Outstanding product, exceeded expectations.'),
    (2, 2, 4, 'Good product, would recommend.'),
    (3, 2, 3, 'Product is okay.'),
    (4, 2, 5, 'Excellent product, exceeded expectations.'),
    (5, 2, 2, 'Not satisfied with the product.'),
    (1, 2, 5, 'Outstanding product, exceeded expectations.'),
    (2, 2, 4, 'Good product, would recommend.'),
    (3, 2, 3, 'Product is okay.'),
    (4, 2, 5, 'Excellent product, exceeded expectations.'),
    (5, 2, 2, 'Not satisfied with the product.');

INSERT INTO reviews (service_id, user_id, rating, review) VALUES 
    (1, 2, 5, 'Outstanding service, exceeded expectations.'),
    (2, 2, 4, 'Good service, would recommend.'),
    (3, 2, 3, 'Service is okay.'),
    (4, 2, 5, 'Excellent service, exceeded expectations.'),
    (5, 2, 2, 'Not satisfied with the service.'),
    (1, 2, 5, 'Outstanding service, exceeded expectations.'),
    (2, 2, 4, 'Good service, would recommend.'),
    (3, 2, 3, 'Service is okay.'),
    (4, 2, 5, 'Excellent service, exceeded expectations.'),
    (5, 2, 2, 'Not satisfied with the service.'),
    (1, 2, 5, 'Outstanding service, exceeded expectations.'),
    (2, 2, 4, 'Good service, would recommend.'),
    (3, 2, 3, 'Service is okay.'),
    (4, 2, 5, 'Excellent service, exceeded expectations.'),
    (5, 2, 2, 'Not satisfied with the service.');


-- ===========================================
-- END OF SCRIPT
-- ===========================================


