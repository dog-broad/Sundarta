/**
 * Cart Page
 * 
 * This script initializes the cart page:
 * - Loads cart contents
 * - Enables quantity updates
 * - Handles item removal
 * - Calculates totals
 * - Manages checkout process
 */

import Cart from './modules/cart.js';
import UI from './utils/ui.js';
import URL from './utils/url.js';

document.addEventListener('DOMContentLoaded', async function() {
    // Initialize cart
    await Cart.init();
    
    // Render cart items
    const cartItemsContainer = document.getElementById('cart-items');
    if (cartItemsContainer) {
        Cart.renderCartItems(cartItemsContainer);
    }
    
    // Update cart summary
    Cart.updateCartSummary();
    
    // Setup checkout button
    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
        // Disable checkout button if cart is empty
        checkoutBtn.disabled = Cart.items.length === 0;
        checkoutBtn.classList.toggle('opacity-50', Cart.items.length === 0);
        
        checkoutBtn.addEventListener('click', async () => {
            // First check stock availability
            const stockCheck = await Cart.checkStock();
            
            if (stockCheck.hasStockIssues) {
                // Show error for out of stock items
                let outOfStockMessage = 'The following items are out of stock or have insufficient stock:';
                stockCheck.outOfStockItems.forEach(item => {
                    outOfStockMessage += `<br>- ${item.name} (Available: ${item.stock}, In cart: ${item.quantity})`;
                });
                
                UI.showError(outOfStockMessage);
                return;
            }
            
            // Proceed to checkout page
            URL.redirect('checkout');
        });
    }
    
    // Update order history link
    const orderHistoryLink = document.querySelector('a[href="/sundarta/orders"]');
    if (orderHistoryLink) {
        orderHistoryLink.href = URL.path('orders');
    }
}); 