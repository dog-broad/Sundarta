/**
 * Services Page Initialization
 * 
 * This file initializes the services page functionality:
 * - Service listing with filters and pagination
 */

import ServicesListModule from './modules/services-list.js';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize services list
    ServicesListModule.init({
        filtersContainerId: 'filters-container',
        servicesContainerId: 'services-grid',
        paginationContainerId: 'pagination',
        servicesPerPage: 12
    });
}); 