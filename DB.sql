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
    avatar VARCHAR(255) DEFAULT NULL,                 -- User's profile picture URL
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
    specifications JSON,                              -- Product specifications in JSON format
    instructions TEXT,                                -- How to use instructions
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
    process JSON,                                     -- Service process steps in JSON format
    faqs JSON,                                        -- Service FAQs in JSON format
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
INSERT INTO products (name, description, price, stock, category, specifications, instructions, images) 
VALUES 
    ('Anti-Aging Cream', 'A premium anti-aging cream that helps reduce wrinkles and fine lines while providing deep hydration. Formulated with natural ingredients to promote youthful skin.', 25.99, 100, 1, 
    '{"Skin Type": "All skin types", "Weight": "50g", "Key Ingredients": "Retinol, Hyaluronic Acid, Vitamin E", "Benefits": "Reduces fine lines, Improves elasticity, Hydrates deeply", "Shelf Life": "24 months", "Country of Origin": "India"}', 
    'Apply a pea-sized amount to clean, dry skin every evening. Gently massage in upward circular motions until fully absorbed. For best results, use consistently as part of your nighttime skincare routine. Avoid contact with eyes. If irritation occurs, discontinue use.', 
    '["https://dummyimage.com/600x400/000/fff&text=Anti+Aging+Cream+1", "https://dummyimage.com/600x400/111/fff&text=Anti+Aging+Cream+2"]'),
    
    ('Shampoo', 'A luxurious hair nourishing shampoo that cleanses while adding volume and shine. Made with natural botanicals to strengthen and protect hair.', 10.99, 50, 2, 
    '{"Hair Type": "All hair types", "Volume": "250ml", "Key Ingredients": "Keratin, Pro-Vitamin B5, Aloe Vera", "Benefits": "Adds volume, Strengthens hair, Prevents breakage", "pH Level": "5.5", "Fragrance": "Herbal"}', 
    'Apply to wet hair and massage gently into scalp and hair. Allow to sit for 1-2 minutes, then rinse thoroughly with warm water. For best results, follow with our matching conditioner. Use 2-3 times per week or as needed.', 
    '["https://dummyimage.com/600x400/222/fff&text=Shampoo+1", "https://dummyimage.com/600x400/333/fff&text=Shampoo+2"]'),
    
    ('Lipstick', 'A long-lasting, creamy lipstick with vibrant color that stays all day. Infused with moisturizing ingredients for comfortable wear.', 15.99, 200, 3, 
    '{"Shade": "Crimson Red", "Finish": "Matte", "Weight": "4g", "Key Ingredients": "Shea Butter, Vitamin E, Jojoba Oil", "Benefits": "Long-lasting, Non-drying, High pigmentation", "Fragrance": "Vanilla"}', 
    'Start by applying lip balm and allowing it to absorb. For precision, outline lips with a matching lip liner. Apply lipstick starting from the center of your lips and moving outward. For a more intense color, apply a second coat. Blot with a tissue for a longer-lasting finish.', 
    '["https://dummyimage.com/600x400/444/fff&text=Lipstick+1", "https://dummyimage.com/600x400/555/fff&text=Lipstick+2"]'),
    
    ('Essential Oil', 'A pure, therapeutic-grade essential oil for relaxation and aromatherapy. Sourced from organic farms and carefully distilled to preserve potency.', 8.99, 150, 4, 
    '{"Source": "Organic farms", "Volume": "10ml", "Key Ingredients": "100% Pure Lavender Oil", "Extraction Method": "Steam distillation", "Benefits": "Promotes relaxation, Helps with sleep, Soothes skin", "Certification": "USDA Organic"}', 
    'For aromatherapy, add 3-5 drops to a diffuser filled with water. For massage, dilute 5 drops in 10ml of carrier oil like jojoba or sweet almond. For bath, add 5-7 drops to warm bathwater. Do not apply undiluted oil directly to skin. Keep away from children and pets.', 
    '["https://dummyimage.com/600x400/666/fff&text=Essential+Oil+1", "https://dummyimage.com/600x400/777/fff&text=Essential+Oil+2"]'),
    
    ('Perfume', 'A sophisticated floral fragrance with notes of rose, jasmine, and subtle hints of vanilla. Long-lasting and perfect for daily wear or special occasions.', 20.99, 100, 5, 
    '{"Fragrance Family": "Floral", "Volume": "50ml", "Top Notes": "Bergamot, Rose", "Middle Notes": "Jasmine, Lily", "Base Notes": "Vanilla, Musk", "Concentration": "Eau de Parfum", "Longevity": "8-10 hours"}', 
    'Spray on pulse points such as wrists, neck, and behind ears. For longer-lasting fragrance, apply to moisturized skin. Avoid rubbing wrists together as this breaks down the fragrance molecules. Store in a cool, dry place away from direct sunlight to preserve the scent.', 
    '["https://dummyimage.com/600x400/888/fff&text=Perfume+1", "https://dummyimage.com/600x400/999/fff&text=Perfume+2"]'),
    
    ('Facial Cleanser', 'A gentle yet effective facial cleanser suitable for all skin types. Removes makeup, dirt, and impurities while maintaining skin\'s natural moisture balance.', 12.99, 80, 1, 
    '{"Skin Type": "All skin types", "Volume": "150ml", "Key Ingredients": "Aloe Vera, Cucumber Extract, Glycerin", "pH Level": "5.5", "Benefits": "Removes impurities, Maintains moisture, Soothes skin", "Free from": "Parabens, Sulfates, Artificial fragrances"}', 
    'Splash face with lukewarm water. Pump a small amount onto fingertips and gently massage onto face in circular motions, avoiding the eye area. Rinse thoroughly with water and pat dry with a clean towel. Use morning and evening as part of your skincare routine.', 
    '["https://dummyimage.com/600x400/aaa/fff&text=Facial+Cleanser+1", "https://dummyimage.com/600x400/bbb/fff&text=Facial+Cleanser+2"]'),
    
    ('Hair Conditioner', 'A deep conditioning treatment that repairs and nourishes damaged hair. Leaves hair soft, smooth, and easy to manage without weighing it down.', 14.99, 60, 2, 
    '{"Hair Type": "Dry and damaged hair", "Volume": "200ml", "Key Ingredients": "Argan Oil, Keratin, Shea Butter", "Benefits": "Repairs damage, Detangles, Adds shine", "pH Level": "4.5", "Fragrance": "Coconut and Vanilla"}', 
    'After shampooing, squeeze excess water from hair and apply conditioner from mid-lengths to ends, avoiding the scalp. Leave on for 2-3 minutes to allow ingredients to penetrate hair shaft. Rinse thoroughly with cool water to seal the cuticle and add shine. Use after every shampoo.', 
    '["https://dummyimage.com/600x400/ccc/fff&text=Hair+Conditioner+1", "https://dummyimage.com/600x400/ddd/fff&text=Hair+Conditioner+2"]'),
    
    ('Foundation', 'A full-coverage, long-wearing foundation suitable for all skin tones. Provides a natural, flawless finish while nurturing skin with beneficial ingredients.', 18.99, 120, 3, 
    '{"Skin Type": "All skin types", "Shade Range": "20 shades", "Coverage": "Medium to full", "Finish": "Satin", "Volume": "30ml", "SPF": "15", "Benefits": "Oil-free, Non-comedogenic, Long-wearing"}', 
    'Start with moisturized, primed skin. Dispense a small amount onto the back of your hand. Using a foundation brush, beauty blender, or fingertips, apply from the center of your face outward. Build coverage as needed with additional thin layers. Set with powder for longer wear.', 
    '["https://dummyimage.com/600x400/eee/fff&text=Foundation+1", "https://dummyimage.com/600x400/fff/000&text=Foundation+2"]'),
    
    ('Massage Oil', 'A relaxing massage oil with lavender scent that soothes muscles and nourishes skin. Perfect for professional use or home relaxation sessions.', 16.99, 90, 4, 
    '{"Skin Type": "All skin types", "Volume": "100ml", "Key Ingredients": "Sweet Almond Oil, Jojoba Oil, Lavender Essential Oil", "Benefits": "Relieves muscle tension, Moisturizes skin, Promotes relaxation", "Absorption Rate": "Medium", "Fragrance": "Lavender"}', 
    'Warm a small amount of oil between your palms before applying to skin. Use long, smooth strokes for relaxation or firmer pressure for deeper muscle work. For best results, perform massage in a warm, comfortable environment. Avoid contact with eyes. Store in a cool, dark place.', 
    '["https://dummyimage.com/600x400/111/fff&text=Massage+Oil+1", "https://dummyimage.com/600x400/222/fff&text=Massage+Oil+2"]'),
    
    ('Cologne', 'A sophisticated masculine cologne with woody notes and subtle citrus undertones. Long-lasting fragrance that evolves throughout the day.', 24.99, 70, 5, 
    '{"Fragrance Family": "Woody", "Volume": "75ml", "Top Notes": "Citrus, Bergamot", "Middle Notes": "Cardamom, Lavender", "Base Notes": "Sandalwood, Cedarwood, Amber", "Concentration": "Eau de Toilette", "Longevity": "6-8 hours"}', 
    'Apply to clean, dry skin on pulse points such as neck, wrists, and behind ears. Spray from a distance of about 6 inches. Do not rub the fragrance into the skin as this breaks down the molecules. For longer-lasting scent, apply after showering when skin is slightly damp.', 
    '["https://dummyimage.com/600x400/333/fff&text=Cologne+1", "https://dummyimage.com/600x400/444/fff&text=Cologne+2"]');

-- Insert sample services
INSERT INTO services (name, user_id, description, price, category, images, process, faqs) 
VALUES 
    ('Ayurvedic Facial Treatment', 3, 'A rejuvenating facial treatment that combines traditional Ayurvedic herbs and modern techniques to deeply cleanse, exfoliate and nourish your skin. This treatment helps to improve skin tone, texture, and radiance while promoting relaxation.', 50.00, 1, 
    '["https://dummyimage.com/600x400/666/fff&text=Ayurvedic+Facial+1", "https://dummyimage.com/600x400/777/fff&text=Ayurvedic+Facial+2"]', 
    '[
        {"step": 1, "title": "Cleansing", "description": "Deep cleansing with natural herbal cleanser to remove impurities"},
        {"step": 2, "title": "Exfoliation", "description": "Gentle exfoliation with Ayurvedic herbs to remove dead skin cells"},
        {"step": 3, "title": "Steam", "description": "Herbal steam to open pores and prepare for extraction"},
        {"step": 4, "title": "Extraction", "description": "Careful extraction of impurities and blackheads"},
        {"step": 5, "title": "Massage", "description": "Facial massage with dosha-specific oils to improve circulation"},
        {"step": 6, "title": "Mask", "description": "Application of herbal mask tailored to your skin type"},
        {"step": 7, "title": "Moisturize", "description": "Final application of natural moisturizer to hydrate skin"}
    ]', 
    '{
        "How long does the treatment take?": "The Ayurvedic facial treatment takes approximately 60-75 minutes for the complete session.",
        "Is it safe for sensitive skin?": "Yes, we customize the herbs and products based on your skin type and sensitivity. Please inform your therapist about any skin concerns before the treatment.",
        "How often should I get this facial?": "For optimal results, we recommend getting the Ayurvedic facial once every 4-6 weeks, depending on your skin condition.",
        "Are there any side effects?": "Most clients experience no side effects. Mild redness may occur immediately after the treatment but subsides quickly.",
        "Should I do anything to prepare before the treatment?": "Arrive with a clean face if possible. Avoid sun exposure and harsh treatments 24 hours before your appointment."
    }'),
    
    ('Precision Haircut & Styling', 3, 'A personalized haircut service that includes consultation, precision cutting techniques, and professional styling to create a look that complements your face shape, hair texture, and lifestyle. Includes a relaxing scalp massage and styling tips.', 35.00, 2, 
    '["https://dummyimage.com/600x400/888/fff&text=Precision+Haircut+1", "https://dummyimage.com/600x400/999/fff&text=Precision+Haircut+2"]', 
    '[
        {"step": 1, "title": "Consultation", "description": "Personalized consultation to discuss your hair goals and preferences"},
        {"step": 2, "title": "Shampoo", "description": "Relaxing shampoo with products selected for your hair type"},
        {"step": 3, "title": "Scalp Massage", "description": "Tension-relieving scalp massage to improve circulation"},
        {"step": 4, "title": "Precision Cut", "description": "Skillful cutting technique tailored to your hair texture and style"},
        {"step": 5, "title": "Blow Dry", "description": "Professional blow dry to set the foundation for styling"},
        {"step": 6, "title": "Styling", "description": "Final styling with premium products for a polished look"},
        {"step": 7, "title": "Hair Care Advice", "description": "Personalized recommendations for maintaining your new style"}
    ]', 
    '{
        "How often should I get a haircut?": "For short styles, every 4-6 weeks. For medium styles, every 6-8 weeks. For long hair, every 8-12 weeks to maintain shape and remove split ends.",
        "Do you cut all hair types and textures?": "Yes, our stylists are trained to work with all hair types, including straight, wavy, curly, coily, fine, thick, and textured hair.",
        "What should I bring to my appointment?": "Reference photos of styles you like can be helpful. Come with clean or day-old hair for best cutting results.",
        "How long does the service take?": "Typically 45-60 minutes, depending on hair length and thickness.",
        "Can I get styling tips during my appointment?": "Absolutely! Our stylists are happy to demonstrate techniques and recommend products for at-home styling."
    }'),
    
    ('Professional Bridal Makeup', 3, 'Comprehensive bridal makeup service designed to create a flawless, radiant look for your special day. Includes skin preparation, premium long-lasting makeup application, and optional false lashes. This service ensures you look stunning both in person and in photographs.', 80.00, 3, 
    '["https://dummyimage.com/600x400/111/fff&text=Bridal+Makeup+1", "https://dummyimage.com/600x400/222/fff&text=Bridal+Makeup+2"]', 
    '[
        {"step": 1, "title": "Skin Prep", "description": "Thorough cleansing and priming to create the perfect canvas"},
        {"step": 2, "title": "Foundation", "description": "Application of long-wearing, photography-friendly foundation matched to your skin tone"},
        {"step": 3, "title": "Eye Makeup", "description": "Detailed eye makeup application customized to complement your features and outfit"},
        {"step": 4, "title": "Lashes", "description": "Optional application of false lashes for added drama"},
        {"step": 5, "title": "Contouring & Highlighting", "description": "Strategic contouring and highlighting to enhance your natural bone structure"},
        {"step": 6, "title": "Lips", "description": "Long-lasting lip color application with liner for definition"},
        {"step": 7, "title": "Setting", "description": "Final setting with professional techniques to ensure makeup lasts throughout your event"}
    ]', 
    '{
        "How long does the makeup application take?": "The bridal makeup service takes approximately 60-90 minutes, depending on the complexity of the look.",
        "Should I do a trial session before my wedding day?": "Yes, we strongly recommend a trial session 3-4 weeks before your wedding to ensure we achieve your perfect look.",
        "How long will the makeup last?": "Our professional techniques and products ensure your makeup will last 12+ hours. We provide touch-up recommendations for your wedding day.",
        "Do you use hypoallergenic products?": "We have a range of products suitable for sensitive skin. Please inform us of any allergies or sensitivities during your consultation.",
        "Can I bring my own makeup products?": "Certainly! While we use professional-grade products, you are welcome to bring items you love or that work well with your skin."
    }'),
    
    ('Therapeutic Deep Tissue Massage', 3, 'A therapeutic massage that targets the deeper layers of muscle and connective tissue. This technique uses slow, firm pressure and deep strokes to release chronic patterns of tension, alleviate pain, and improve mobility. Ideal for addressing specific muscle problems.', 65.00, 4, 
    '["https://dummyimage.com/600x400/333/fff&text=Deep+Tissue+Massage+1", "https://dummyimage.com/600x400/444/fff&text=Deep+Tissue+Massage+2"]', 
    '[
        {"step": 1, "title": "Consultation", "description": "Discussion of your specific concerns, pain points, and pressure preferences"},
        {"step": 2, "title": "Warm-Up", "description": "Initial lighter techniques to warm and prepare the muscles"},
        {"step": 3, "title": "Deep Tissue Work", "description": "Targeted pressure applied to problematic areas to release tension"},
        {"step": 4, "title": "Stretching", "description": "Gentle stretching to improve mobility and flexibility"},
        {"step": 5, "title": "Circulation", "description": "Techniques to enhance blood flow to affected areas"},
        {"step": 6, "title": "Final Relaxation", "description": "Lighter techniques to promote overall relaxation and comfort"},
        {"step": 7, "title": "Aftercare Advice", "description": "Recommendations for post-massage care to maximize benefits"}
    ]', 
    '{
        "How long does the massage last?": "Our deep tissue massage sessions are 60, 90, or 120 minutes, depending on your needs and preference.",
        "Is it safe for pregnant women?": "Deep tissue massage is not recommended during pregnancy. We offer specialized prenatal massage services instead.",
        "Will the massage hurt?": "Deep tissue massage uses firm pressure that may cause some discomfort, but should never be painful. Always communicate with your therapist about pressure.",
        "How often should I get a deep tissue massage?": "For chronic conditions, every 2-4 weeks is often beneficial. For general maintenance, monthly sessions are recommended.",
        "What should I do after my massage?": "Drink plenty of water, avoid strenuous activity for 24 hours, and consider applying ice to any areas that feel tender."
    }'),
    
    ('Custom Perfume Creation Experience', 3, 'An immersive fragrance journey where you create your own signature scent under the guidance of our perfume expert. You\'ll learn about fragrance families, explore over 50 essential oils and aroma compounds, and blend a personalized 30ml perfume to take home.', 85.00, 5, 
    '["https://dummyimage.com/600x400/555/fff&text=Custom+Perfume+1", "https://dummyimage.com/600x400/666/fff&text=Custom+Perfume+2"]', 
    '[
        {"step": 1, "title": "Fragrance Education", "description": "Introduction to perfume basics, fragrance families, and composition"},
        {"step": 2, "title": "Scent Exploration", "description": "Guided exploration of base, middle, and top note ingredients"},
        {"step": 3, "title": "Preference Mapping", "description": "Identifying your personal preferences and creating a scent profile"},
        {"step": 4, "title": "Formula Creation", "description": "Developing your custom formula with expert guidance"},
        {"step": 5, "title": "Blending", "description": "Precise measuring and mixing of selected ingredients"},
        {"step": 6, "title": "Maturation", "description": "Information on the maturation process for your fragrance"},
        {"step": 7, "title": "Bottling", "description": "Bottling your signature scent in a beautiful container to take home"}
    ]', 
    '{
        "How long does the experience take?": "The custom perfume creation experience takes approximately 90-120 minutes.",
        "Do I need any prior knowledge about perfumes?": "No prior knowledge is required. Our expert will guide you through the entire process, explaining everything you need to know.",
        "How long will my custom perfume last?": "The 30ml bottle typically lasts 3-6 months with regular use. We keep your formula on file for easy reorders.",
        "Is the experience suitable for groups?": "Yes, we offer private group sessions for up to 6 people, perfect for bridal parties, birthdays, or corporate events.",
        "Are the ingredients natural?": "We use a combination of natural essential oils and safe synthetic compounds to create well-rounded, long-lasting fragrances."
    }'),
    
    ('Advanced Hydrating Facial', 7, 'A deeply hydrating facial treatment designed for dry, dehydrated skin. This luxurious facial combines multiple hydration technologies including hyaluronic acid serums, hydrating masks, and gentle exfoliation to restore moisture balance and create a plump, dewy complexion.', 75.00, 1, 
    '["https://dummyimage.com/600x400/777/fff&text=Hydrating+Facial+1", "https://dummyimage.com/600x400/888/fff&text=Hydrating+Facial+2"]', 
    '[
        {"step": 1, "title": "Cleansing", "description": "Double cleansing with gentle hydrating cleansers"},
        {"step": 2, "title": "Analysis", "description": "Detailed skin analysis to assess hydration needs"},
        {"step": 3, "title": "Exfoliation", "description": "Gentle enzymatic exfoliation to remove dead skin cells"},
        {"step": 4, "title": "Hydration Infusion", "description": "Application of multiple layers of hydrating serums"},
        {"step": 5, "title": "Facial Massage", "description": "Specialized massage techniques to improve product absorption"},
        {"step": 6, "title": "Mask", "description": "Intensive hydrating mask with hyaluronic acid and botanicals"},
        {"step": 7, "title": "Finishing", "description": "Application of moisture-sealing creams and SPF protection"}
    ]', 
    '{
        "How often should I get this facial?": "For severely dehydrated skin, initially every 2-3 weeks, then monthly for maintenance.",
        "Will this help with fine lines?": "Yes, proper hydration can significantly reduce the appearance of fine lines caused by dehydration.",
        "Is this suitable for sensitive skin?": "This facial is generally gentle and suitable for most sensitive skin types. We adjust products based on your sensitivity level.",
        "How can I maintain results at home?": "We provide a customized home care routine and product recommendations to maintain optimal hydration between treatments.",
        "Will I be red after the treatment?": "This facial is designed to be gentle, so redness is minimal to none for most clients."
    }'),
    
    ('Hair Color Transformation', 7, 'Complete hair color service that can transform your look through highlighting, balayage, ombre, or all-over color. Includes color consultation, professional application, processing, toner (if needed), and a nourishing treatment to maintain hair health and vibrancy.', 120.00, 2, 
    '["https://dummyimage.com/600x400/999/fff&text=Hair+Color+1", "https://dummyimage.com/600x400/aaa/fff&text=Hair+Color+2"]', 
    '[
        {"step": 1, "title": "Color Consultation", "description": "In-depth discussion about your color goals, maintenance expectations, and hair history"},
        {"step": 2, "title": "Hair Assessment", "description": "Analysis of your hair condition, porosity, and current color to determine the best approach"},
        {"step": 3, "title": "Color Formulation", "description": "Custom mixing of professional color products for your unique needs"},
        {"step": 4, "title": "Application", "description": "Precise application using techniques appropriate for your desired result"},
        {"step": 5, "title": "Processing", "description": "Monitored processing time to achieve optimal color development"},
        {"step": 6, "title": "Toner", "description": "Application of toner if needed to perfect your shade"},
        {"step": 7, "title": "Treatment", "description": "Deep conditioning treatment to restore moisture and shine"}
    ]', 
    '{
        "How long does the coloring process take?": "Depending on the service (highlights, balayage, all-over color), expect between 2-4 hours.",
        "How often will I need to touch up my color?": "Root touch-ups are typically needed every 4-6 weeks. Balayage or ombre styles can go 2-3 months between appointments.",
        "Is coloring damaging to my hair?": "We use premium color products and conditioning treatments to minimize damage. We\'ll also provide home care advice to maintain hair health.",
        "Can I go from dark to blonde in one session?": "Major color changes usually require multiple sessions to protect hair integrity. We\'ll create a custom plan for your color journey.",
        "How should I prepare for my color appointment?": "Come with unwashed hair (1-2 days dirty), wear clothing that won\'t be damaged by color, and bring reference photos of your desired result."
    }'),
    
    ('Deluxe Makeover Package', 7, 'A comprehensive beauty transformation that includes professional makeup application, hairstyling, skincare consultation, and personalized beauty advice. Perfect for special events, photoshoots, or when you want to learn new techniques for your everyday routine.', 95.00, 3, 
    '["https://dummyimage.com/600x400/bbb/fff&text=Deluxe+Makeover+1", "https://dummyimage.com/600x400/ccc/fff&text=Deluxe+Makeover+2"]', 
    '[
        {"step": 1, "title": "Consultation", "description": "Discussion of your preferences, event details, and desired look"},
        {"step": 2, "title": "Skincare Prep", "description": "Cleansing and priming to create the perfect canvas"},
        {"step": 3, "title": "Foundation", "description": "Flawless foundation application matched to your skin tone and type"},
        {"step": 4, "title": "Eyes & Brows", "description": "Detailed eye makeup and brow styling to frame your face"},
        {"step": 5, "title": "Contouring & Highlighting", "description": "Sculpting techniques to enhance your features"},
        {"step": 6, "title": "Hairstyling", "description": "Professional styling to complement your makeup look"},
        {"step": 7, "title": "Education", "description": "Tips and techniques for recreating elements of your look at home"}
    ]', 
    '{
        "How long does the makeover take?": "The deluxe makeover package takes approximately 2-2.5 hours to complete.",
        "Should I arrive with clean hair and face?": "Yes, please arrive with clean, dry hair and a cleansed face for best results.",
        "Can I bring my own products?": "You\'re welcome to bring favorite products, but we provide all professional-grade makeup and hair products needed.",
        "Do you accommodate all skin tones?": "Absolutely. Our makeup artists are trained to work with all skin tones and have inclusive shade ranges.",
        "Is this service good for learning techniques?": "Yes! We provide education throughout the service and can focus on teaching you specific techniques you\'d like to master."
    }'),
    
    ('Hot Stone & Aromatherapy Massage', 7, 'A luxurious therapeutic massage that combines the deep heat of smooth basalt stones with the benefits of essential oil aromatherapy. The stones help release muscle tension while essential oils enhance relaxation, reduce stress, and balance body systems for complete wellness.', 85.00, 4, 
    '["https://dummyimage.com/600x400/ddd/fff&text=Hot+Stone+Massage+1", "https://dummyimage.com/600x400/eee/fff&text=Hot+Stone+Massage+2"]', 
    '[
        {"step": 1, "title": "Aromatherapy Selection", "description": "Selection of essential oil blend based on your needs and preferences"},
        {"step": 2, "title": "Full Body Relaxation", "description": "Initial relaxation techniques with aromatherapy oils"},
        {"step": 3, "title": "Stone Placement", "description": "Strategic placement of warm stones on key energy points"},
        {"step": 4, "title": "Stone Massage", "description": "Deep tissue techniques using heated stones as massage tools"},
        {"step": 5, "title": "Tension Release", "description": "Focused work on areas of particular tension or discomfort"},
        {"step": 6, "title": "Energy Balancing", "description": "Final stone placement for chakra or energy balancing"},
        {"step": 7, "title": "Integration", "description": "Gentle techniques to help your body integrate the benefits of the massage"}
    ]', 
    '{
        "How hot are the stones?": "The stones are heated to approximately 120-140°F (49-60°C), comfortable enough to be placed directly on the skin while providing therapeutic heat.",
        "How long does the massage last?": "The hot stone and aromatherapy massage session is 75 or 90 minutes.",
        "Is it safe if I\'m pregnant?": "This massage is not recommended during pregnancy due to the heat and pressure. We offer specialized prenatal massage instead.",
        "Can I choose my aromatherapy scent?": "Yes, we offer several therapeutic blends targeting different concerns like relaxation, energy, clarity, or immune support.",
        "What should I do after the massage?": "Drink plenty of water, avoid alcohol, and try to relax for the remainder of the day to maximize benefits."
    }'),
    
    ('Luxury Aromatherapy Consultation', 7, 'An in-depth aromatherapy consultation with our certified aromatherapist to create custom essential oil blends for your specific needs. Includes education on essential oil properties, custom blending session, and three personalized products to take home (massage oil, room spray, and roll-on).', 110.00, 5, 
    '["https://dummyimage.com/600x400/fff/000&text=Aromatherapy+Consultation+1", "https://dummyimage.com/600x400/000/fff&text=Aromatherapy+Consultation+2"]', 
    '[
        {"step": 1, "title": "Wellness Assessment", "description": "Comprehensive discussion of your health concerns, stress levels, and wellness goals"},
        {"step": 2, "title": "Scent Preference Evaluation", "description": "Exploration of different oil families to determine your scent preferences"},
        {"step": 3, "title": "Education", "description": "Overview of essential oil properties, safety, and applications"},
        {"step": 4, "title": "Custom Formulation", "description": "Development of personalized blends to address your specific needs"},
        {"step": 5, "title": "Product Creation", "description": "Hands-on blending of three custom aromatherapy products"},
        {"step": 6, "title": "Usage Instruction", "description": "Detailed guidance on how and when to use your products"},
        {"step": 7, "title": "Follow-up Plan", "description": "Creation of a long-term aromatherapy wellness plan"}
    ]', 
    '{
        "Do I need prior knowledge about essential oils?": "No prior knowledge is required. The consultation is educational and tailored to your level of familiarity with aromatherapy.",
        "Are the essential oils organic?": "Yes, we use certified organic or wildcrafted essential oils of therapeutic grade.",
        "How long do the custom products last?": "The products typically last 6-12 months, depending on usage frequency. We keep your formulas on file for easy reordering.",
        "Can aromatherapy help with my specific health concerns?": "Aromatherapy can support many aspects of wellbeing, from stress and sleep to immunity and energy. We\'ll discuss your specific concerns during the consultation.",
        "Is this suitable for sensitive individuals?": "Yes, we take a conservative approach with dilution rates and can select oils suitable for sensitivity. Please inform us of any known allergies or sensitivities."
    }');

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