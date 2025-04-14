/**
 * Cart Module
 * 
 * This module handles all shopping cart operations including:
 * - Viewing cart contents
 * - Adding/removing items
 * - Updating quantities
 * - Checking stock
 * - Checkout process
 * 
 * API Endpoints Used:
 * - GET /api/cart
 * - POST /api/cart/item
 * - PUT /api/cart/item
 * - DELETE /api/cart/item
 * - DELETE /api/cart/clear
 * - GET /api/cart/check-stock
 * - POST /api/orders/checkout
 */

import API from '../utils/api.js';
import UI from '../utils/ui.js';
import URL from '../utils/url.js';

const Cart = {
    /**
     * Current cart state
     */
    items: [],
    summary: {
        total_items: 0,
        total_price: 0,
        product_items: 0,
        product_price: 0,
        service_items: 0,
        service_price: 0
    },

    /**
     * Initialize cart
     * Loads cart contents from server
     */
    init: async () => {
        try {
            await Cart.getCart();
            
            // Update cart count in header
            Cart.updateCartCount();
            
            return true;
        } catch (error) {
            console.error('Error initializing cart:', error);
            return false;
        }
    },

    /**
     * Get cart contents
     * @returns {Promise<Object>}
     */
    getCart: async () => {
        try {
            const response = await API.get('/cart');
            
            if (response.success) {
                Cart.items = response.data.items || [];
                Cart.summary = response.data.summary || {
                    total_items: 0,
                    total_price: 0,
                    product_items: 0,
                    product_price: 0,
                    service_items: 0,
                    service_price: 0
                };
                
                return {
                    items: Cart.items,
                    summary: Cart.summary
                };
            }
            
            return {
                items: [],
                summary: {
                    total_items: 0,
                    total_price: 0,
                    product_items: 0,
                    product_price: 0,
                    service_items: 0,
                    service_price: 0
                }
            };
        } catch (error) {
            console.error('Error getting cart:', error);
            return {
                items: [],
                summary: {
                    total_items: 0,
                    total_price: 0,
                    product_items: 0,
                    product_price: 0,
                    service_items: 0,
                    service_price: 0
                }
            };
        }
    },

    /**
     * Add item to cart
     * @param {string} type - Type of item (product or service)
     * @param {number} itemId - Product or service ID
     * @param {number} quantity - Quantity to add
     * @param {Object} options - Additional options
     * @returns {Promise<boolean>} - Success status
     */
    addItem: async (type, itemId, quantity = 1, options = {}) => {
        try {
            const payload = {};
            
            if (type === 'product') {
                payload.product_id = itemId;
            } else if (type === 'service') {
                payload.service_id = itemId;
                
                // Add appointment details if provided
                if (options.appointment) {
                    payload.appointment = options.appointment;
                }
            } else {
                throw new Error('Invalid item type');
            }
            
            payload.quantity = quantity;
            
            const response = await API.post('/cart', payload);
            
            if (response.success) {
                // Update local cart state
                Cart.items = response.data.items || [];
                Cart.summary = response.data.summary || Cart.summary;
                
                // Update cart count in header
                Cart.updateCartCount();
                
                return true;
            }
            
            return false;
        } catch (error) {
            console.error(`Error adding ${type} to cart:`, error);
            return false;
        }
    },

    /**
     * Update item quantity
     * @param {number} itemId 
     * @param {number} quantity 
     * @returns {Promise}
     */
    updateQuantity: async (itemId, quantity) => {
        try {
            if (quantity <= 0) {
                return Cart.removeItem(itemId);
            }
            
            const response = await API.put(`/cart/item?id=${itemId}`, { quantity });
            
            if (response.success) {
                // Update local cart state
                Cart.items = response.data.items || [];
                Cart.summary = response.data.summary || Cart.summary;
                
                // Update cart count in header
                Cart.updateCartCount();
                
                return true;
            }
            
            return false;
        } catch (error) {
            console.error('Error updating cart item quantity:', error);
            return false;
        }
    },

    /**
     * Remove item from cart
     * @param {number} itemId 
     * @returns {Promise}
     */
    removeItem: async (itemId) => {
        try {
            const response = await API.delete(`/cart/item?id=${itemId}`);
            
            if (response.success) {
                // Update local cart state
                Cart.items = response.data.items || [];
                Cart.summary = response.data.summary || Cart.summary;
                
                // Update cart count in header
                Cart.updateCartCount();
                
                return true;
            }
            
            return false;
        } catch (error) {
            console.error('Error removing item from cart:', error);
            return false;
        }
    },

    /**
     * Clear entire cart
     * @returns {Promise}
     */
    clearCart: async () => {
        try {
            const response = await API.delete('/cart/clear');
            
            if (response.success) {
                // Reset local cart state
                Cart.items = [];
                Cart.summary = {
                    total_items: 0,
                    total_price: 0,
                    product_items: 0,
                    product_price: 0,
                    service_items: 0,
                    service_price: 0
                };
                
                // Update cart count in header
                Cart.updateCartCount();
                
                return true;
            }
            
            return false;
        } catch (error) {
            console.error('Error clearing cart:', error);
            return false;
        }
    },

    /**
     * Check stock availability for products
     * @returns {Promise<Object>}
     */
    checkStock: async () => {
        try {
            const response = await API.get('/cart/check-stock');
            
            if (response.success) {
                return {
                    hasStockIssues: response.data.has_stock_issues,
                    outOfStockItems: response.data.out_of_stock_items || []
                };
            }
            
            return {
                hasStockIssues: false,
                outOfStockItems: []
            };
        } catch (error) {
            console.error('Error checking stock:', error);
            return {
                hasStockIssues: true,
                outOfStockItems: []
            };
        }
    },

    /**
     * Process checkout
     * @param {Object} orderData - Shipping and payment details
     * @returns {Promise<Object>}
     */
    checkout: async (orderData) => {
        try {
            // First check stock
            const stockCheck = await Cart.checkStock();
            
            if (stockCheck.hasStockIssues) {
                return {
                    success: false,
                    message: 'Some items are out of stock',
                    outOfStockItems: stockCheck.outOfStockItems
                };
            }
            
            // Proceed with checkout
            const response = await API.post('/orders/checkout', orderData);
            
            if (response.success) {
                // Clear the cart locally after successful order
                Cart.items = [];
                Cart.summary = {
                    total_items: 0,
                    total_price: 0,
                    product_items: 0,
                    product_price: 0,
                    service_items: 0,
                    service_price: 0
                };
                
                // Update cart count in header
                Cart.updateCartCount();
                
                return {
                    success: true,
                    orderId: response.data.order_id,
                    message: response.message || 'Order placed successfully'
                };
            }
            
            return {
                success: false,
                message: response.message || 'Failed to place order'
            };
        } catch (error) {
            console.error('Error processing checkout:', error);
            return {
                success: false,
                message: 'An error occurred during checkout'
            };
        }
    },
    
    /**
     * Update cart count in header
     */
    updateCartCount: () => {
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = Cart.summary.total_items || 0;
            
            // Show/hide count based on whether there are items
            if (Cart.summary.total_items > 0) {
                cartCountElement.classList.remove('hidden');
            } else {
                cartCountElement.classList.add('hidden');
            }
        }
    },
    
    /**
     * Format price with currency symbol
     * @param {number} price 
     * @returns {string}
     */
    formatPrice: (price) => {
        return `₹${parseFloat(price).toFixed(2)}`;
    },
    
    /**
     * Render cart items
     * @param {HTMLElement} container 
     */
    renderCartItems: (container) => {
        if (!container) return;
        
        if (Cart.items.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-shopping-cart text-5xl text-text-light mb-4"></i>
                    <p class="text-text-light mb-6">Your cart is empty</p>
                    <a href="${URL.path('products')}" class="btn btn-primary">Shop Now</a>
                </div>
            `;
            return;
        }
        
        let cartHtml = '';
        
        Cart.items.forEach(item => {
            // Parse images
            let imageUrl = 'https://via.placeholder.com/100';
            try {
                const images = JSON.parse(item.images);
                if (images && images.length > 0) {
                    imageUrl = images[0];
                }
            } catch (e) {
                console.error('Error parsing images', e);
            }
            
            cartHtml += `
                <div class="cart-item card p-4" data-item-id="${item.id}" data-item-type="${item.item_type}">
                    <div class="flex items-center gap-4">
                        <div class="cart-item-image w-24 h-24 rounded-md overflow-hidden">
                            <img src="${imageUrl}" alt="${item.name}" class="w-full h-full object-cover">
                        </div>
                        <div class="cart-item-details flex-grow">
                            <h3 class="font-heading text-lg">${item.name}</h3>
                            <div class="text-sm text-text-light mt-1">
                                ${item.item_type === 'service' ? '<i class="fas fa-spa mr-1"></i> Service' : '<i class="fas fa-box mr-1"></i> Product'}
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <div class="cart-item-price font-semibold">
                                    ${Cart.formatPrice(item.price)} 
                                    <span class="text-xs text-text-light">x ${item.quantity}</span>
                                </div>
                                <div class="cart-item-subtotal font-semibold">
                                    ${Cart.formatPrice(item.subtotal)}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4 pt-4 border-t">
                        <div class="quantity-controls flex items-center">
                            <button class="quantity-btn decrement-btn px-3 py-1 border border-sand rounded-l-md">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="quantity-input w-16 py-1 px-2 text-center border-y border-sand" 
                                value="${item.quantity}" min="1" max="${item.item_type === 'product' ? (item.stock || 10) : 10}">
                            <button class="quantity-btn increment-btn px-3 py-1 border border-sand rounded-r-md">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <button class="remove-item-btn text-text-light hover:text-red-500">
                            <i class="fas fa-trash-alt mr-1"></i> Remove
                        </button>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = cartHtml;
        
        // Add event listeners
        const cartItems = container.querySelectorAll('.cart-item');
        
        cartItems.forEach(cartItem => {
            const itemId = cartItem.getAttribute('data-item-id');
            const quantityInput = cartItem.querySelector('.quantity-input');
            const decrementBtn = cartItem.querySelector('.decrement-btn');
            const incrementBtn = cartItem.querySelector('.increment-btn');
            const removeBtn = cartItem.querySelector('.remove-item-btn');
            
            if (quantityInput && decrementBtn && incrementBtn) {
                decrementBtn.addEventListener('click', async () => {
                    const currentValue = parseInt(quantityInput.value);
                    if (currentValue > 1) {
                        const newValue = currentValue - 1;
                        quantityInput.value = newValue;
                        await Cart.updateQuantity(itemId, newValue);
                        
                        // Update subtotal and cart summary
                        Cart.updateCartUI();
                    }
                });
                
                incrementBtn.addEventListener('click', async () => {
                    const currentValue = parseInt(quantityInput.value);
                    const max = parseInt(quantityInput.getAttribute('max'));
                    if (currentValue < max) {
                        const newValue = currentValue + 1;
                        quantityInput.value = newValue;
                        await Cart.updateQuantity(itemId, newValue);
                        
                        // Update subtotal and cart summary
                        Cart.updateCartUI();
                    }
                });
                
                quantityInput.addEventListener('change', async () => {
                    let value = parseInt(quantityInput.value);
                    const max = parseInt(quantityInput.getAttribute('max'));
                    
                    if (isNaN(value) || value < 1) {
                        value = 1;
                    } else if (value > max) {
                        value = max;
                    }
                    
                    quantityInput.value = value;
                    await Cart.updateQuantity(itemId, value);
                    
                    // Update subtotal and cart summary
                    Cart.updateCartUI();
                });
            }
            
            if (removeBtn) {
                removeBtn.addEventListener('click', async () => {
                    // Show confirmation modal
                    UI.showModal({
                        title: 'Remove Item',
                        content: 'Are you sure you want to remove this item from your cart?',
                        buttons: [
                            {
                                text: 'Cancel',
                                class: 'btn-outline'
                            },
                            {
                                text: 'Remove',
                                class: 'btn-primary',
                                callback: async () => {
                                    await Cart.removeItem(itemId);
                                    
                                    // Update cart UI
                                    Cart.updateCartUI();
                                }
                            }
                        ]
                    });
                });
            }
        });
    },
    
    /**
     * Update cart summary display
     * @param {Object} selectors - DOM selectors for subtotal, tax, and total
     */
    updateCartSummary: (selectors = {}) => {
        const { 
            subtotalSelector = '#cart-subtotal', 
            taxSelector = '#cart-tax',
            totalSelector = '#cart-total'
        } = selectors;
        
        // Update subtotal
        const subtotalElement = document.querySelector(subtotalSelector);
        if (subtotalElement) {
            subtotalElement.textContent = Cart.formatPrice(Cart.summary.total_price);
        }
        
        // Calculate and update tax (assuming 18% GST)
        const taxElement = document.querySelector(taxSelector);
        if (taxElement) {
            const tax = Cart.summary.total_price * 0.18;
            taxElement.textContent = Cart.formatPrice(tax);
        }
        
        // Update total (subtotal + tax)
        const totalElement = document.querySelector(totalSelector);
        if (totalElement) {
            const total = Cart.summary.total_price * 1.18; // Adding 18% tax
            totalElement.textContent = Cart.formatPrice(total);
        }
    },
    
    /**
     * Update entire cart UI (items and summary)
     */
    updateCartUI: async () => {
        // Refresh cart data from server
        await Cart.getCart();
        
        // Update cart items
        const cartItemsContainer = document.getElementById('cart-items');
        if (cartItemsContainer) {
            Cart.renderCartItems(cartItemsContainer);
        }
        
        // Update cart summary
        Cart.updateCartSummary();
        
        // Update checkout button state
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.disabled = Cart.items.length === 0;
            checkoutBtn.classList.toggle('opacity-50', Cart.items.length === 0);
        }
    }
};

export default Cart; 