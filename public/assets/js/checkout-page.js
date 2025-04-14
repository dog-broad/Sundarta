/**
 * Checkout Page
 * 
 * This script initializes the checkout page:
 * - Loads cart contents
 * - Handles order placement
 * - Manages shipping & payment forms
 * - Validates order data
 */

import Cart from './modules/cart.js';
import UI from './utils/ui.js';
import URL from './utils/url.js';

document.addEventListener('DOMContentLoaded', async function() {
    // Initialize cart
    await Cart.init();
    
    // Check if cart is empty, redirect to cart page if it is
    if (Cart.items.length === 0) {
        URL.redirect('cart');
        return;
    }
    
    // Render order summary
    const orderSummaryContainer = document.getElementById('order-summary');
    if (orderSummaryContainer) {
        renderOrderSummary(orderSummaryContainer);
    }
    
    // Set up checkout form submission
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', handleCheckoutSubmission);
    }
    
    // Update order history link
    const orderHistoryLink = document.querySelector('a[href="/orders"]');
    if (orderHistoryLink) {
        orderHistoryLink.href = URL.path('orders');
    }
});

/**
 * Render order summary
 * @param {HTMLElement} container - Container element
 */
function renderOrderSummary(container) {
    let itemsHtml = '';
    
    // Add cart items to summary
    Cart.items.forEach(item => {
        itemsHtml += `
            <div class="flex justify-between py-2 border-b">
                <div>
                    <div class="font-medium">${item.name}</div>
                    <div class="text-sm text-text-light">
                        ${item.item_type === 'service' ? 'Service' : 'Product'} x ${item.quantity}
                    </div>
                </div>
                <div class="font-semibold">${Cart.formatPrice(item.subtotal)}</div>
            </div>
        `;
    });
    
    // Calculate price totals
    const subtotal = Cart.summary.total_price;
    const tax = subtotal * 0.18; // Assuming 18% GST
    const shipping = 0; // Free shipping
    const total = subtotal + tax + shipping;
    
    // Build summary HTML
    const summaryHtml = `
        <h2 class="font-heading text-xl mb-4">Order Summary</h2>
        
        <div class="mb-4">
            ${itemsHtml}
        </div>
        
        <div class="space-y-2 mb-4">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>${Cart.formatPrice(subtotal)}</span>
            </div>
            <div class="flex justify-between">
                <span>Tax (18% GST)</span>
                <span>${Cart.formatPrice(tax)}</span>
            </div>
            <div class="flex justify-between">
                <span>Shipping</span>
                <span>${shipping === 0 ? 'Free' : Cart.formatPrice(shipping)}</span>
            </div>
            <div class="flex justify-between font-semibold text-lg pt-2 border-t">
                <span>Total</span>
                <span>${Cart.formatPrice(total)}</span>
            </div>
        </div>
    `;
    
    container.innerHTML = summaryHtml;
}

/**
 * Handle checkout form submission
 * @param {Event} e - Submit event
 */
async function handleCheckoutSubmission(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(e.target);
    
    // Convert to object
    const orderData = {
        shipping: {
            name: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            address: formData.get('address'),
            city: formData.get('city'),
            state: formData.get('state'),
            pincode: formData.get('pincode')
        },
        payment: {
            method: formData.get('payment_method')
        }
    };
    
    // Show loading state in submit button
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
    
    // Check stock before proceeding
    const stockCheck = await Cart.checkStock();
    
    if (stockCheck.hasStockIssues) {
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
        
        // Show error for out of stock items
        let outOfStockMessage = 'The following items are out of stock or have insufficient stock:';
        stockCheck.outOfStockItems.forEach(item => {
            outOfStockMessage += `<br>- ${item.name} (Available: ${item.stock}, In cart: ${item.quantity})`;
        });
        
        UI.showError(outOfStockMessage);
        return;
    }
    
    // Process checkout
    const result = await Cart.checkout(orderData);
    
    // Reset button state
    submitBtn.disabled = false;
    submitBtn.textContent = originalBtnText;
    
    if (result.success) {
        // Show success message and redirect to order confirmation
        UI.showSuccess(result.message || 'Order placed successfully!');
        
        // Redirect to order confirmation page
        setTimeout(() => {
            URL.redirect(`order-confirmation?id=${result.orderId}`);
        }, 2000);
    } else {
        // Show error message
        UI.showError(result.message || 'Failed to place order. Please try again.');
    }
} 