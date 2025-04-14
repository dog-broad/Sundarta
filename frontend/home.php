<?php
require_once __DIR__ . '/../backend/helpers/auth.php';
require 'partials/header.php';
?>

<!-- Hero Section -->
<section class="hero relative rounded-lg overflow-hidden mb-16">
    <div class="container mx-auto px-6 py-16 relative">
        <div class="max-w-lg">
            <span class="inline-block bg-primary bg-opacity-20 text-white px-3 py-1 rounded-full text-sm font-medium mb-4">Ayurvedic Beauty</span>
            <h1 class="font-heading text-4xl md:text-5xl mb-4">Discover Natural Beauty & Wellness</h1>
            <p class="text-text-light mb-6">Experience the power of traditional Indian beauty rituals with our handcrafted products and authentic wellness services.</p>
            <div class="flex flex-wrap gap-4">
                <a href="/products" class="btn btn-primary">Shop Products</a>
                <a href="/services" class="btn btn-outline">Explore Services</a>
            </div>
        </div>
    </div>
    <div class="absolute top-0 right-0 w-full md:w-1/2 h-full opacity-20 md:opacity-100">
        <div class="dot-pattern top-10 right-10"></div>
    </div>
</section>

<!-- Featured Categories -->
<section class="container mx-auto px-6 mb-16">
    <div class="text-center mb-10">
        <h2 class="font-heading text-3xl mb-2">Our Categories</h2>
        <p class="text-text-light">Explore our range of beauty and wellness offerings</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="/products?category=skincare" class="card text-center p-6 hover:shadow-xl transition-shadow">
            <div class="text-primary text-4xl mb-4">
                <i class="fas fa-leaf"></i>
            </div>
            <h3 class="font-heading text-xl mb-2">Skincare</h3>
            <p class="text-text-light">Natural products for radiant skin</p>
        </a>
        
        <a href="/products?category=haircare" class="card text-center p-6 hover:shadow-xl transition-shadow">
            <div class="text-primary text-4xl mb-4">
                <i class="fas fa-spa"></i>
            </div>
            <h3 class="font-heading text-xl mb-2">Haircare</h3>
            <p class="text-text-light">Traditional remedies for healthy hair</p>
        </a>
        
        <a href="/services" class="card text-center p-6 hover:shadow-xl transition-shadow">
            <div class="text-primary text-4xl mb-4">
                <i class="fas fa-hands"></i>
            </div>
            <h3 class="font-heading text-xl mb-2">Wellness</h3>
            <p class="text-text-light">Holistic services for mind and body</p>
        </a>
    </div>
</section>

<!-- Featured Products -->
<section class="container mx-auto px-6 mb-16">
    <div class="flex justify-between items-center mb-10">
        <div>
            <h2 class="font-heading text-3xl mb-2">Featured Products</h2>
            <p class="text-text-light">Discover our most popular beauty products</p>
        </div>
        <a href="/products" class="hover-link text-primary">
            View All <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <div id="featured-products" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Products will be populated by JS -->
    </div>
</section>

<!-- About Section -->
<section class="container mx-auto px-6 mb-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
        <div>
            <span class="inline-block bg-sand-light px-3 py-1 rounded-full text-primary text-sm font-medium mb-4">Our Story</span>
            <h2 class="font-heading text-3xl mb-4">Traditional Beauty, Modern Science</h2>
            <p class="text-text-light mb-4">
                At Sundarta, we blend ancient Ayurvedic wisdom with modern scientific research to create products that nurture your natural beauty.
            </p>
            <p class="text-text-light mb-6">
                Our ingredients are ethically sourced from across India, supporting local communities and sustainable farming practices.
            </p>
            <a href="/about" class="btn btn-primary">Learn More About Us</a>
        </div>
        <div class="relative">
            <img src="https://img.freepik.com/free-photo/woman-with-face-mask-sitting-chair_23-2148758655.jpg" alt="Ayurvedic Beauty" class="rounded-lg shadow-lg">
            <div class="absolute -bottom-5 -left-5 w-24 h-24 bg-primary rounded-lg hidden md:block"></div>
            <div class="absolute -top-5 -right-5 w-16 h-16 bg-secondary rounded-lg hidden md:block"></div>
        </div>
    </div>
</section>

<!-- Featured Services -->
<section class="container mx-auto px-6 mb-16 bg-sand-light py-12 rounded-lg">
    <div class="flex justify-between items-center mb-10">
        <div>
            <h2 class="font-heading text-3xl mb-2">Our Services</h2>
            <p class="text-text-light">Experience authentic wellness treatments</p>
        </div>
        <a href="/services" class="hover-link text-primary">
            View All <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <div id="featured-services" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Services will be populated by JS -->
    </div>
</section>

<!-- Testimonials -->
<section class="container mx-auto px-6 mb-16">
    <div class="text-center mb-10">
        <h2 class="font-heading text-3xl mb-2">What Our Customers Say</h2>
        <p class="text-text-light">Hear from people who have experienced Sundarta</p>
    </div>
    
    <div id="testimonials" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Testimonials will be populated by JS -->
    </div>
</section>

<!-- Newsletter -->
<section class="container mx-auto px-6 mb-16">
    <div class="text-center bg-sand-light bg-opacity-10 rounded-lg p-12 text-center">
        <h2 class="font-heading text-3xl mb-2">Join Our Community</h2>
        <p class="text-text-light mb-6 max-w-xl mx-auto">Subscribe to our newsletter for exclusive offers, beauty tips, and updates on new products and services.</p>
        
        <form class="newsletter-form max-w-md mx-auto">
            <input type="email" placeholder="Your email address" class="newsletter-input" required>
            <button type="submit" class="newsletter-btn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</section>

<!-- FAQ Section -->
<section class="container mx-auto px-6 mb-16">
    <div class="text-center mb-10">
        <h2 class="font-heading text-3xl mb-2">Frequently Asked Questions</h2>
        <p class="text-text-light">Find answers to common questions about our products and services</p>
    </div>
    
    <div class="accordion max-w-3xl mx-auto">
        <div class="accordion-header">
            What makes Sundarta products unique?
        </div>
        <div class="accordion-content">
            <p>Sundarta products are formulated based on ancient Ayurvedic principles combined with modern scientific research. We use ethically sourced, natural ingredients that are free from harmful chemicals. Each product is created with a holistic approach to beauty and wellness.</p>
        </div>
        
        <div class="accordion-header">
            How are your ingredients sourced?
        </div>
        <div class="accordion-content">
            <p>We source our ingredients from trusted farmers and suppliers who follow sustainable farming practices. Many of our herbs and botanicals come from organic farms across India. We prioritize fair trade and environmental responsibility in our sourcing process.</p>
        </div>
        
        <div class="accordion-header">
            Do you offer international shipping?
        </div>
        <div class="accordion-content">
            <p>Yes, we ship to most countries worldwide. International shipping rates and delivery times vary depending on your location. Please check our shipping policy for more details or contact our customer service team for specific information.</p>
        </div>
    </div>
</section>

<script type="module">
    import HomeModule from '/assets/js/modules/home.js';
    
    document.addEventListener('DOMContentLoaded', () => {
        HomeModule.init();
    });
</script>

<?php
require 'partials/footer.php';
?>