<?php
require_once __DIR__ . '/../backend/helpers/auth.php';
require 'partials/header.php';
?>

<?php
// Sample data for categories, products, and services as JSON strings
$categories_json = '[
    {"id": 1, "name": "Skincare"},
    {"id": 2, "name": "Haircare"},
    {"id": 3, "name": "Makeup"},
    {"id": 4, "name": "Wellness"},
    {"id": 5, "name": "Fragrance"}
]';

$products_json = '[
    {"id": 1, "name": "Anti-Aging Cream", "description": "A cream that helps reduce wrinkles and fine lines", "price": 25.99, "stock": 100, "category": 1, "images": ["https://via.placeholder.com/300x300.png?text=Anti-Aging+Cream+1", "https://via.placeholder.com/300x300.png?text=Anti-Aging+Cream+2"]},
    {"id": 2, "name": "Hair Growth Shampoo", "description": "A shampoo that promotes healthy hair growth", "price": 15.99, "stock": 50, "category": 2, "images": ["https://via.placeholder.com/300x300.png?text=Hair+Growth+Shampoo+1", "https://via.placeholder.com/300x300.png?text=Hair+Growth+Shampoo+2"]},
    {"id": 3, "name": "Foundation Cream", "description": "A foundation cream that blends smoothly for perfect coverage", "price": 30.99, "stock": 75, "category": 3, "images": ["https://via.placeholder.com/300x300.png?text=Foundation+Cream+1", "https://via.placeholder.com/300x300.png?text=Foundation+Cream+2"]},
    {"id": 4, "name": "Lavender Essential Oil", "description": "A soothing oil used for relaxation and stress relief", "price": 10.49, "stock": 150, "category": 4, "images": ["https://via.placeholder.com/300x300.png?text=Lavender+Oil+1", "https://via.placeholder.com/300x300.png?text=Lavender+Oil+2"]},
    {"id": 5, "name": "Perfume Spray", "description": "A fresh floral fragrance that lasts all day", "price": 40.00, "stock": 200, "category": 5, "images": ["https://via.placeholder.com/300x300.png?text=Perfume+Spray+1", "https://via.placeholder.com/300x300.png?text=Perfume+Spray+2"]}
]';

$services_json = '[
    {"id": 1, "name": "Facial Treatment", "description": "A rejuvenating facial treatment that enhances skin glow and texture", "price": 50.00, "category": 1, "images": ["https://via.placeholder.com/300x300.png?text=Facial+Treatment+1", "https://via.placeholder.com/300x300.png?text=Facial+Treatment+2"]},
    {"id": 2, "name": "Hair Styling", "description": "A stylish hair cut and treatment that fits your personality", "price": 40.00, "category": 2, "images": ["https://via.placeholder.com/300x300.png?text=Hair+Styling+1", "https://via.placeholder.com/300x300.png?text=Hair+Styling+2"]},
    {"id": 3, "name": "Makeup Application", "description": "A professional makeup application for special events", "price": 60.00, "category": 3, "images": ["https://via.placeholder.com/300x300.png?text=Makeup+Application+1", "https://via.placeholder.com/300x300.png?text=Makeup+Application+2"]},
    {"id": 4, "name": "Yoga Session", "description": "A relaxing yoga session for stress relief and flexibility", "price": 25.00, "category": 4, "images": ["https://via.placeholder.com/300x300.png?text=Yoga+Session+1", "https://via.placeholder.com/300x300.png?text=Yoga+Session+2"]},
    {"id": 5, "name": "Fragrance Consultation", "description": "A personalized fragrance consultation to find your perfect scent", "price": 45.00, "category": 5, "images": ["https://via.placeholder.com/300x300.png?text=Fragrance+Consultation+1", "https://via.placeholder.com/300x300.png?text=Fragrance+Consultation+2"]}
]';

// Decode JSON data into PHP arrays
$categories = json_decode($categories_json, true);
$products = json_decode($products_json, true);
$services = json_decode($services_json, true);

// Now, you can use these PHP variables ($categories, $products, $services) to populate your HTML
?>


<!-- Sample Section for Categories -->
<!-- 
    Use the $categories array to display the available categories
    Loop through each category and display the name.
    Example:
        <ul>
            <li>Skincare</li>
            <li>Haircare</li>
            <li>Makeup</li>
            <li>Wellness</li>
            <li>Fragrance</li>
        </ul>
-->

<!-- Sample Section for Products -->
<!-- 
    Use the $products array to display the products.
    Loop through each product and display the name, description, price, and images.
    Example for each product:
        <div class="product">
            <img src="[Image URL]" alt="[Product Name]">
            <h2>[Product Name]</h2>
            <p>[Description]</p>
            <span>$[Price]</span>
        </div>
-->

<!-- Sample Section for Services -->
<!-- 
    Use the $services array to display the services.
    Loop through each service and display the name, description, price, and images.
    Example for each service:
        <div class="service">
            <img src="[Image URL]" alt="[Service Name]">
            <h2>[Service Name]</h2>
            <p>[Description]</p>
            <span>$[Price]</span>
        </div>
-->


<?php
require 'partials/footer.php';
?>