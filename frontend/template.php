<?php
require_once __DIR__ . '/../backend/helpers/auth.php';
require 'partials/header.php';
?>

<div class="container mx-auto py-8">
    <h1 class="font-heading text-4xl mb-12 text-center">Sundarta Component Library</h1>
    
    <!-- Colors Section -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Color Palette</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div class="p-4 rounded-md bg-sand-light h-24 flex items-end">
                <span class="text-sm font-medium">Sand Light</span>
            </div>
            <div class="p-4 rounded-md bg-sand h-24 flex items-end">
                <span class="text-sm font-medium">Sand</span>
            </div>
            <div class="p-4 rounded-md bg-clay h-24 flex items-end text-white">
                <span class="text-sm font-medium">Clay</span>
            </div>
            <div class="p-4 rounded-md bg-clay-dark h-24 flex items-end text-white">
                <span class="text-sm font-medium">Clay Dark</span>
            </div>
            <div class="p-4 rounded-md bg-spice h-24 flex items-end text-white">
                <span class="text-sm font-medium">Spice (Primary)</span>
            </div>
            <div class="p-4 rounded-md bg-spice-light h-24 flex items-end text-white">
                <span class="text-sm font-medium">Spice Light</span>
            </div>
            <div class="p-4 rounded-md bg-leaf h-24 flex items-end text-white">
                <span class="text-sm font-medium">Leaf (Secondary)</span>
            </div>
            <div class="p-4 rounded-md bg-sky h-24 flex items-end">
                <span class="text-sm font-medium">Sky</span>
            </div>
            <div class="p-4 rounded-md bg-ocean h-24 flex items-end text-white">
                <span class="text-sm font-medium">Ocean (Accent)</span>
            </div>
            <div class="p-4 rounded-md bg-surface h-24 flex items-end border">
                <span class="text-sm font-medium">Surface</span>
            </div>
        </div>
    </section>
    
    <!-- Typography Section -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Typography</h2>
        
        <div class="grid gap-6">
            <div>
                <h3 class="text-sm text-text-light mb-2">Headings (Playfair Display)</h3>
                <h1 class="font-heading text-4xl mb-2">Heading 1</h1>
                <h2 class="font-heading text-3xl mb-2">Heading 2</h2>
                <h3 class="font-heading text-2xl mb-2">Heading 3</h3>
                <h4 class="font-heading text-xl mb-2">Heading 4</h4>
                <h5 class="font-heading text-lg mb-2">Heading 5</h5>
                <h6 class="font-heading text-base">Heading 6</h6>
            </div>
            
            <div>
                <h3 class="text-sm text-text-light mb-2">Body Text (Poppins)</h3>
                <p class="mb-2">Regular paragraph text. Sundarta offers a wide range of beauty and wellness products inspired by traditional Indian ingredients and modern research.</p>
                <p class="text-sm mb-2">Small text for secondary information.</p>
                <p class="text-xs">Extra small text for footnotes or captions.</p>
            </div>
            
            <div>
                <h3 class="text-sm text-text-light mb-2">Special Text Styles</h3>
                <p class="text-primary mb-2">Text in primary color</p>
                <p class="text-secondary mb-2">Text in secondary color</p>
                <p class="text-accent mb-2">Text in accent color</p>
                <p class="italic mb-2">Italic text for emphasis</p>
                <p class="font-semibold">Semibold text for importance</p>
            </div>
        </div>
    </section>
    
    <!-- Buttons Section -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Buttons</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <h3 class="text-sm text-text-light mb-4">Primary Buttons</h3>
                <div class="flex flex-wrap gap-4">
                    <button class="btn btn-primary">Primary Button</button>
                    <button class="btn btn-primary" disabled>Disabled</button>
                </div>
            </div>
            
            <div>
                <h3 class="text-sm text-text-light mb-4">Secondary Buttons</h3>
                <div class="flex flex-wrap gap-4">
                    <button class="btn btn-secondary">Secondary Button</button>
                    <button class="btn btn-secondary" disabled>Disabled</button>
                </div>
            </div>
            
            <div>
                <h3 class="text-sm text-text-light mb-4">Outline Buttons</h3>
                <div class="flex flex-wrap gap-4">
                    <button class="btn btn-outline">Outline Button</button>
                    <button class="btn btn-outline" disabled>Disabled</button>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Forms Section -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Form Elements</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm text-text-light mb-4">Text Inputs</h3>
                
                <div class="input-group">
                    <label for="text-input" class="input-label">Text Input</label>
                    <input type="text" id="text-input" class="input-text" placeholder="Enter text here">
                </div>
                
                <div class="input-group">
                    <label for="email-input" class="input-label">Email Input</label>
                    <input type="email" id="email-input" class="input-text" placeholder="Enter email here">
                </div>
                
                <div class="input-group">
                    <label for="password-input" class="input-label">Password Input</label>
                    <input type="password" id="password-input" class="input-text" placeholder="Enter password">
                </div>
            </div>
            
            <div>
                <h3 class="text-sm text-text-light mb-4">Other Inputs</h3>
                
                <div class="input-group">
                    <label for="select-input" class="input-label">Select Input</label>
                    <select id="select-input" class="input-text">
                        <option value="">Select an option</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                    </select>
                </div>
                
                <div class="input-group">
                    <label for="textarea-input" class="input-label">Textarea</label>
                    <textarea id="textarea-input" class="input-text" rows="4" placeholder="Enter your message"></textarea>
                </div>
            </div>
            
            <div>
                <h3 class="text-sm text-text-light mb-4">Search Box</h3>
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search products...">
                </div>
            </div>
            
            <div>
                <h3 class="text-sm text-text-light mb-4">Newsletter</h3>
                <form class="newsletter-form">
                    <input type="email" placeholder="Your email address" class="newsletter-input" required>
                    <button type="submit" class="newsletter-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>
    
    <!-- Cards Section -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Cards</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Standard Card -->
            <div class="card">
                <h3 class="font-heading text-xl mb-2">Standard Card</h3>
                <p class="text-text-light">This is a basic card component that can be used for various content.</p>
            </div>
            
            <!-- Product Card (Single Image) -->
            <div class="card card-product">
                <img src="https://hips.hearstapps.com/hmg-prod/images/ghk-digital-index-haircolor-449-640a4807297b5.jpg?crop=0.668xw:1.00xh;0.167xw,0&resize=480:*" alt="Product" class="card-product-image">
                <div class="card-product-body">
                    <span class="badge badge-primary mb-2">New</span>
                    <h3 class="font-heading text-lg mb-1">Product Name</h3>
                    <p class="text-text-light text-sm mb-2">Short description of the product goes here.</p>
                    <div class="flex items-center mb-2">
                        <div class="flex text-primary">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="text-xs ml-1">(4.5)</span>
                    </div>
                </div>
                <div class="card-product-footer flex justify-between items-center">
                    <span class="font-semibold">₹1,299.00</span>
                    <button class="btn btn-primary py-1 px-3 text-sm">Add to Cart</button>
                </div>
            </div>
            
            <!-- Product Card (Multiple Images) -->
            <div class="card card-product">
                <div class="product-gallery">
                    <div class="product-gallery-container">
                        <img src="https://img.freepik.com/free-vector/makeup-packaging-background_1268-1384.jpg" alt="Product" class="product-gallery-image">
                        <img src="https://www.fbscosmetics.com/wp-content/uploads/2024/05/super-set-sqaure-optimized.webp" alt="Product" class="product-gallery-image">
                        <img src="https://img.freepik.com/premium-photo/collection-beauty-products-with-copy-space_521733-10072.jpg" alt="Product" class="product-gallery-image">
                    </div>
                    <div class="product-gallery-prev">
                        <i class="fas fa-chevron-left"></i>
                    </div>
                    <div class="product-gallery-next">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="product-gallery-nav"></div>
                </div>
                <div class="card-product-body">
                    <span class="badge badge-primary mb-2">Featured</span>
                    <h3 class="font-heading text-lg mb-1">Multi-Image Product</h3>
                    <p class="text-text-light text-sm mb-2">Product with multiple images. Click arrows or dots to navigate.</p>
                    <div class="flex items-center mb-2">
                        <div class="flex text-primary">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="text-xs ml-1">(5.0)</span>
                    </div>
                </div>
                <div class="card-product-footer flex justify-between items-center">
                    <span class="font-semibold">₹1,899.00</span>
                    <button class="btn btn-primary py-1 px-3 text-sm">Add to Cart</button>
                </div>
            </div>
        </div>
        
        <!-- Additional Multi-Image Product Card Example -->
        <div class="mt-8">
            <h3 class="font-heading text-xl mb-4">Multi-Image Product Card (Full Width)</h3>
            <div class="card card-product max-w-2xl mx-auto">
                <div class="product-gallery">
                    <div class="product-gallery-container">
                        <img src="https://img.freepik.com/free-vector/makeup-packaging-background_1268-1384.jpg" alt="Product" class="product-gallery-image">
                        <img src="https://www.fbscosmetics.com/wp-content/uploads/2024/05/super-set-sqaure-optimized.webp" alt="Product" class="product-gallery-image">
                        <img src="https://img.freepik.com/premium-photo/collection-beauty-products-with-copy-space_521733-10072.jpg" alt="Product" class="product-gallery-image">
                    </div>
                    <div class="product-gallery-prev">
                        <i class="fas fa-chevron-left"></i>
                    </div>
                    <div class="product-gallery-next">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="product-gallery-nav"></div>
                </div>
                <div class="card-product-body">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="badge badge-secondary mb-2">Limited Edition</span>
                            <h3 class="font-heading text-xl mb-1">Ayurvedic Beauty Set</h3>
                        </div>
                        <span class="text-xl font-semibold text-primary">₹2,499.00</span>
                    </div>
                    <p class="text-text-light mb-4">Complete Ayurvedic skincare routine with four essential products. Made with traditional herbs and natural ingredients.</p>
                    <div class="flex items-center mb-4">
                        <div class="flex text-primary">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="text-sm ml-2">(4.5) 128 reviews</span>
                    </div>
                    <button class="btn btn-primary w-full">Add to Cart</button>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Alert Section -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Alerts</h2>
        
        <div class="grid gap-4">
            <div class="alert alert-success">
                <strong>Success!</strong> Your order has been placed successfully.
            </div>
            
            <div class="alert alert-warning">
                <strong>Warning!</strong> Some items in your cart are out of stock.
            </div>
            
            <div class="alert alert-error">
                <strong>Error!</strong> There was an error processing your payment.
            </div>
        </div>
    </section>
    
    <!-- Testimonial Section -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Testimonials</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="testimonial">
                <img src="https://img.freepik.com/free-photo/lifestyle-beauty-fashion-people-emotions-concept-young-asian-female-office-manager-ceo-with-pleased-expression-standing-white-background-smiling-with-arms-crossed-chest_1258-59329.jpg?semt=ais_hybrid" alt="Customer" class="testimonial-avatar">
                <div class="testimonial-quote">
                    <p class="mb-4">I've been using Sundarta's skincare products for three months now, and the results are amazing! My skin feels rejuvenated and healthier than ever.</p>
                    <p class="font-semibold">- Priya Sharma</p>
                    <p class="text-sm text-text-light">Delhi</p>
                </div>
            </div>
            
            <div class="testimonial">
                <img src="https://img.freepik.com/free-photo/smiling-beautiful-woman-shows-heart-gesture-near-chest-express-like-sympathy-passionate-about-smth-standing-against-white-wall-t-shirt_176420-40420.jpg" alt="Customer" class="testimonial-avatar">
                <div class="testimonial-quote">
                    <p class="mb-4">The wellness services at Sundarta have transformed my self-care routine. The staff is knowledgeable and professional. Highly recommended!</p>
                    <p class="font-semibold">- Rahul Mehta</p>
                    <p class="text-sm text-text-light">Mumbai</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Service Boxes -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Service Boxes</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="service-box">
                <div class="text-primary text-3xl mb-4">
                    <i class="fas fa-spa"></i>
                </div>
                <h3 class="font-heading text-xl mb-2">Ayurvedic Consultations</h3>
                <p class="text-text-light mb-4">Get personalized Ayurvedic wellness plans based on your unique body constitution.</p>
                <a href="#" class="hover-link text-primary text-sm">Learn More <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            
            <div class="service-box">
                <div class="text-primary text-3xl mb-4">
                    <i class="fas fa-hands"></i>
                </div>
                <h3 class="font-heading text-xl mb-2">Traditional Massages</h3>
                <p class="text-text-light mb-4">Experience the healing power of traditional Indian massage techniques.</p>
                <a href="#" class="hover-link text-primary text-sm">Learn More <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            
            <div class="service-box">
                <div class="text-primary text-3xl mb-4">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3 class="font-heading text-xl mb-2">Natural Skincare</h3>
                <p class="text-text-light mb-4">Discover skin treatments using natural ingredients for a radiant complexion.</p>
                <a href="#" class="hover-link text-primary text-sm">Learn More <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
    </section>
    
    <!-- Accordion Section -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Accordion</h2>
        
        <div class="accordion">
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
    
    <!-- Hero Section Example -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Hero Section Example</h2>
        
        <div class="hero relative rounded-lg overflow-hidden">
            <div class="container mx-auto px-6 py-12 relative z-10">
                <div class="max-w-lg">
                    <span class="inline-block bg-primary bg-opacity-20 text-white px-3 py-1 rounded-full text-sm font-medium mb-4">New Collection</span>
                    <h2 class="font-heading text-4xl md:text-5xl mb-4">Discover Natural Beauty</h2>
                    <p class="text-text-light mb-6">Experience the power of traditional Indian beauty rituals with our handcrafted products.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#" class="btn btn-primary">Shop Now</a>
                        <a href="#" class="btn btn-outline">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-full md:w-1/2 h-full opacity-20 md:opacity-100">
                <div class="dot-pattern top-10 right-10"></div>
            </div>
        </div>
    </section>
    
    <!-- Animation Examples -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Animation Examples</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card text-center p-6">
                <div class="fade-in">
                    <i class="fas fa-feather-alt text-4xl text-primary mb-4"></i>
                    <h3 class="font-heading text-lg mb-2">Fade In</h3>
                    <p class="text-sm text-text-light">Elements that smoothly fade in when viewed.</p>
                </div>
            </div>
            
            <div class="card text-center p-6">
                <div class="slide-in">
                    <i class="fas fa-wind text-4xl text-primary mb-4"></i>
                    <h3 class="font-heading text-lg mb-2">Slide In</h3>
                    <p class="text-sm text-text-light">Elements that slide in from the side.</p>
                </div>
            </div>
            
            <div class="card text-center p-6">
                <div class="pulse">
                    <i class="fas fa-heart text-4xl text-primary mb-4"></i>
                    <h3 class="font-heading text-lg mb-2">Pulse</h3>
                    <p class="text-sm text-text-light">Elements that pulse to draw attention.</p>
                </div>
            </div>
            
            <div class="card text-center p-6">
                <div class="border-glow rounded-md p-2">
                    <i class="fas fa-magic text-4xl text-primary mb-4"></i>
                    <h3 class="font-heading text-lg mb-2">Border Glow</h3>
                    <p class="text-sm text-text-light">Elements with glowing borders.</p>
                </div>
            </div>
        </div>
        
        <div class="mt-8">
            <h3 class="font-heading text-lg mb-4">Staggered List Animation</h3>
            <ul class="stagger-list grid grid-cols-1 md:grid-cols-3 gap-4">
                <li class="bg-surface p-4 rounded shadow">Item 1 - Appears first</li>
                <li class="bg-surface p-4 rounded shadow">Item 2 - Appears second</li>
                <li class="bg-surface p-4 rounded shadow">Item 3 - Appears third</li>
                <li class="bg-surface p-4 rounded shadow">Item 4 - Appears fourth</li>
                <li class="bg-surface p-4 rounded shadow">Item 5 - Appears fifth</li>
            </ul>
        </div>
    </section>
    
    <!-- Hover Effects -->
    <section class="mb-16">
        <h2 class="font-heading text-2xl mb-6 pb-2 border-b">Hover Effects</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="#" class="card hover:shadow-xl transition-shadow">
                <h3 class="font-heading text-lg mb-2">Card Shadow</h3>
                <p class="text-text-light">Hover to see enhanced shadow effect.</p>
            </a>
            
            <a href="#" class="hover-link text-xl flex items-center justify-center h-full">
                Hover Link Effect
            </a>
            
            <div class="card p-6 text-center">
                <img src="https://img.freepik.com/free-photo/smiling-beautiful-woman-shows-heart-gesture-near-chest-express-like-sympathy-passionate-about-smth-standing-against-white-wall-t-shirt_176420-40420.jpg" alt="Hover Image" class="product-image w-32 h-32 mx-auto rounded-full">
                <p class="text-text-light mt-4">Hover over the image to see zoom effect.</p>
            </div>
        </div>
    </section>
</div>

<?php
require 'partials/footer.php';
?>
