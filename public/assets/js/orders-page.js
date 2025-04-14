/**
 * Orders Page
 * 
 * This script initializes the orders page:
 * - Fetches user's order history
 * - Displays orders in a list format
 * - Provides links to view order details
 */

import API from './utils/api.js';
import UI from './utils/ui.js';
import URL from './utils/url.js';

document.addEventListener('DOMContentLoaded', async function() {
    // Fetch and display orders
    await loadOrders();
});

/**
 * Fetch orders from API and display them
 */
async function loadOrders() {
    const ordersContainer = document.getElementById('orders-container');
    const noOrdersContainer = document.getElementById('no-orders');
    
    if (!ordersContainer) return;
    
    try {
        const response = await API.get('/orders/my-orders');
        
        if (response.success) {
            const orders = response.data;
            
            // Show empty state if no orders
            if (!orders || orders.length === 0) {
                if (ordersContainer) ordersContainer.classList.add('hidden');
                if (noOrdersContainer) noOrdersContainer.classList.remove('hidden');
                return;
            }
            
            // Render orders
            renderOrders(ordersContainer, orders);
        } else {
            UI.showError(response.message || 'Failed to load orders');
            
            // Show empty container with error
            ordersContainer.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-error mb-4">Failed to load orders</p>
                    <button class="btn btn-outline" onclick="location.reload()">Try Again</button>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        UI.showError('An error occurred while loading orders');
        
        // Show empty container with error
        ordersContainer.innerHTML = `
            <div class="text-center py-8">
                <p class="text-error mb-4">Failed to load orders</p>
                <button class="btn btn-outline" onclick="location.reload()">Try Again</button>
            </div>
        `;
    }
}

/**
 * Render orders in the container
 * @param {HTMLElement} container - Container element
 * @param {Array} orders - Array of order objects
 */
function renderOrders(container, orders) {
    // Sort orders by date (most recent first)
    const sortedOrders = [...orders].sort((a, b) => 
        new Date(b.created_at || Date.now()) - new Date(a.created_at || Date.now())
    );
    
    // Generate HTML for orders
    let ordersHtml = `
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border">
                        <th class="text-left py-3 px-4">Order ID</th>
                        <th class="text-left py-3 px-4">Date</th>
                        <th class="text-left py-3 px-4">Total</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-right py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    sortedOrders.forEach(order => {
        // Calculate total from items if not directly available
        let orderTotal;
        if (order.total) {
            orderTotal = parseFloat(order.total);
        } else if (order.items && Array.isArray(order.items)) {
            orderTotal = order.items.reduce((sum, item) => {
                const itemPrice = parseFloat(item.price || 0);
                const itemQuantity = parseInt(item.quantity || 1);
                return sum + (itemPrice * itemQuantity);
            }, 0);
        } else {
            orderTotal = 0;
        }
        
        ordersHtml += `
            <tr class="border-b border-border hover:bg-surface-hover transition">
                <td class="py-4 px-4">#${order.id || 'N/A'}</td>
                <td class="py-4 px-4">${formatDate(order.created_at || Date.now())}</td>
                <td class="py-4 px-4">₹${orderTotal.toFixed(2)}</td>
                <td class="py-4 px-4">
                    <span class="badge badge-${getStatusBadgeClass(order.status || 'pending')}">${order.status || 'Pending'}</span>
                </td>
                <td class="py-4 px-4 text-right">
                    <a href="${URL.path('order-confirmation')}?id=${order.id}" class="btn btn-sm btn-outline">
                        View Details
                    </a>
                </td>
            </tr>
        `;
    });
    
    ordersHtml += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = ordersHtml;
}

/**
 * Format date in a readable format
 * @param {string} dateString - ISO date string
 * @returns {string} - Formatted date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Get badge class based on order status
 * @param {string} status - Order status
 * @returns {string} - Badge class
 */
function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
        case 'completed':
            return 'success';
        case 'processing':
            return 'primary';
        case 'shipped':
            return 'primary';  // Treating shipped as primary
        case 'delivered':
            return 'success';  // Treating delivered as completed/success
        case 'pending':
            return 'warning';
        case 'cancelled':
            return 'error';
        default:
            return 'secondary';
    }
} 