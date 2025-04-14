/**
 * Order Confirmation Page
 * 
 * This script initializes the order confirmation page:
 * - Fetches order details from API
 * - Displays order items, shipping information, and total
 */

import API from './utils/api.js';
import UI from './utils/ui.js';
import URL from './utils/url.js';

document.addEventListener('DOMContentLoaded', async function() {
    const orderId = document.getElementById('order-id')?.textContent;
    
    if (!orderId) {
        URL.redirect('orders');
        return;
    }
    
    // Fetch order details
    try {
        const response = await API.get('/orders/detail', { id: orderId });
        
        if (response.success) {
            const order = response.data;
            
            // Render order details
            renderOrderDetails(order);
            
            // Render shipping information
            renderShippingInfo(order);
            
            // Update buttons with correct URLs
            updateActionButtons();
        } else {
            UI.showError('Failed to load order details');
        }
    } catch (error) {
        console.error('Error fetching order details:', error);
        UI.showError('An error occurred while loading order details');
    }
});

/**
 * Render order details
 * @param {Object} order - Order data
 */
function renderOrderDetails(order) {
    const container = document.getElementById('order-details');
    
    if (!container) return;
    
    let itemsHtml = '';
    
    // Add items to details
    order.items.forEach(item => {
        const itemPrice = parseFloat(item.price || 0);
        const itemQuantity = parseInt(item.quantity || 1);
        const itemTotal = itemPrice * itemQuantity;
        
        itemsHtml += `
            <div class="flex justify-between py-2 border-b">
                <div>
                    <div class="font-medium">${item.name || 'Unknown Item'}</div>
                    <div class="text-sm text-text-light">
                        ${item.type === 'service' ? 'Service' : 'Product'} x ${itemQuantity}
                    </div>
                </div>
                <div class="font-semibold">₹${itemTotal.toFixed(2)}</div>
            </div>
        `;
    });
    
    // Calculate totals from items if not directly available
    const subtotal = order.subtotal ? parseFloat(order.subtotal) : 
                     order.items.reduce((sum, item) => sum + (parseFloat(item.price || 0) * parseInt(item.quantity || 1)), 0);
    
    const tax = order.tax ? parseFloat(order.tax) : (subtotal * 0.18); // Default 18% tax if not specified
    const shippingFee = order.shipping_fee ? parseFloat(order.shipping_fee) : 0;
    const total = order.total ? parseFloat(order.total) : (subtotal + tax + shippingFee);
    
    // Build details HTML
    const detailsHtml = `
        <div class="mb-4">
            <div class="flex justify-between text-text-light mb-2">
                <span>Order Date</span>
                <span>${new Date(order.created_at || Date.now()).toLocaleString()}</span>
            </div>
            <div class="flex justify-between text-text-light mb-2">
                <span>Payment Method</span>
                <span>${order.payment_method === 'cod' ? 'Cash on Delivery' : 'Online Payment'}</span>
            </div>
            <div class="flex justify-between text-text-light mb-2">
                <span>Status</span>
                <span class="badge badge-${getStatusBadgeClass(order.status || 'pending')}">${order.status || 'Pending'}</span>
            </div>
        </div>
        
        <h3 class="font-semibold mb-2">Items</h3>
        <div class="mb-4">
            ${itemsHtml}
        </div>
        
        <div class="space-y-2 mb-4">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>₹${subtotal.toFixed(2)}</span>
            </div>
            <div class="flex justify-between">
                <span>Tax</span>
                <span>₹${tax.toFixed(2)}</span>
            </div>
            <div class="flex justify-between">
                <span>Shipping</span>
                <span>${shippingFee === 0 ? 'Free' : `₹${shippingFee.toFixed(2)}`}</span>
            </div>
            <div class="flex justify-between font-semibold text-lg pt-2 border-t">
                <span>Total</span>
                <span>₹${total.toFixed(2)}</span>
            </div>
        </div>
    `;
    
    container.innerHTML = detailsHtml;
}

/**
 * Render shipping information
 * @param {Object} order - Order data
 */
function renderShippingInfo(order) {
    const container = document.getElementById('shipping-info');
    
    if (!container || !order.shipping) return;
    
    const shipping = order.shipping;
    
    const shippingHtml = `
        <div class="space-y-2">
            <div>
                <span class="font-semibold">${shipping.name || 'Name Not Available'}</span>
            </div>
            <div>${shipping.email || 'Email Not Available'}</div>
            <div>${shipping.phone || 'Phone Not Available'}</div>
            <div class="pt-2">${shipping.address || 'Address Not Available'}</div>
            <div>${shipping.city || ''}, ${shipping.state || ''} ${shipping.pincode || ''}</div>
        </div>
    `;
    
    container.innerHTML = shippingHtml;
}

/**
 * Get badge class based on order status
 * @param {string} status - Order status
 * @returns {string} - Badge class
 */
function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
        case 'pending':
            return 'badge-warning';
        case 'processing':
            return 'badge-info';
        case 'shipped':
            return 'badge-primary';
        case 'delivered':
            return 'badge-success';
        case 'cancelled':
            return 'badge-danger';
        default:
            return 'badge-secondary';
    }
}

/**
 * Update action buttons with correct URLs
 */
function updateActionButtons() {
    const continueShoppingBtn = document.querySelector('a[href="/products"]');
    if (continueShoppingBtn) {
        continueShoppingBtn.href = URL.path('products');
    }
    
    const viewOrdersBtn = document.querySelector('a[href="/orders"]');
    if (viewOrdersBtn) {
        viewOrdersBtn.href = URL.path('orders');
    }
} 