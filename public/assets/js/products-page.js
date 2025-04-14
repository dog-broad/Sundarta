/**
 * Products Page Initialization
 * 
 * This file initializes the products page functionality:
 * - Product listing with filters and pagination
 */

import ProductsListModule from './modules/products-list.js';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize products list
    ProductsListModule.init({
        filtersContainerId: 'filters-container',
        productsContainerId: 'products-grid',
        paginationContainerId: 'pagination',
        productsPerPage: 12
    });
}); 