document.addEventListener('DOMContentLoaded', function() {
    // Accordion functionality
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            this.classList.toggle('active');
            
            const content = this.nextElementSibling;
            if (content.classList.contains('active')) {
                content.classList.remove('active');
            } else {
                content.classList.add('active');
            }
        });
    });
    
    // Mobile navigation toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Add fadeIn animation to elements with fade-in class
    const fadeElements = document.querySelectorAll('.fade-in');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    fadeElements.forEach(element => {
        element.style.animationPlayState = 'paused';
        observer.observe(element);
    });
    
    // Product image hover zoom effect
    const productImages = document.querySelectorAll('.product-image');
    
    productImages.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Newsletter form validation
    const newsletterForm = document.querySelector('.newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            if (!isValidEmail(email)) {
                showAlert('Please enter a valid email address', 'error');
                return;
            }
            
            // Here you would normally send the form data to your backend
            // For now we'll just show a success message
            showAlert('Thank you for subscribing to our newsletter!', 'success');
            emailInput.value = '';
        });
    }
    
    // Helper function for email validation
    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    // Helper function to show alerts
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        const container = document.querySelector('.container') || document.body;
        container.prepend(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    
    // Back to top button
    const backToTopBtn = document.getElementById('back-to-top');
    
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.remove('hidden');
            } else {
                backToTopBtn.classList.add('hidden');
            }
        });
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Product Gallery Functionality
    const productGalleries = document.querySelectorAll('.product-gallery');
    
    productGalleries.forEach(gallery => {
        const container = gallery.querySelector('.product-gallery-container');
        const images = gallery.querySelectorAll('.product-gallery-image');
        const dotsContainer = gallery.querySelector('.product-gallery-nav');
        const prevButton = gallery.querySelector('.product-gallery-prev');
        const nextButton = gallery.querySelector('.product-gallery-next');
        let currentIndex = 0;
        
        // Don't set up if there's only one image
        if (images.length <= 1) return;
        
        // Create dots for navigation
        if (dotsContainer) {
            images.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.className = `product-gallery-dot ${index === 0 ? 'active' : ''}`;
                dot.addEventListener('click', () => {
                    goToSlide(index);
                });
                dotsContainer.appendChild(dot);
            });
        }
        
        // Previous button functionality
        if (prevButton) {
            prevButton.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                updateGallery();
            });
        }
        
        // Next button functionality
        if (nextButton) {
            nextButton.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % images.length;
                updateGallery();
            });
        }
        
        function goToSlide(index) {
            currentIndex = index;
            updateGallery();
        }
        
        function updateGallery() {
            if (container) {
                container.style.transform = `translateX(-${currentIndex * 100}%)`;
            }
            
            // Update active dot
            if (dotsContainer) {
                const dots = dotsContainer.querySelectorAll('.product-gallery-dot');
                dots.forEach((dot, index) => {
                    if (index === currentIndex) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            }
        }
    });
});

// Add smooth scrolling to all links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});
