/**
 * UI Utility Module
 * 
 * This module provides common UI functionality and components:
 * - Loading states
 * - Alerts and notifications
 * - Modal dialogs
 * - Form handling
 * - Image galleries
 */

const UI = {
    /**
     * Show loading spinner
     * @param {HTMLElement} container 
     */
    showLoading: (container) => {
        // Create loading spinner
        const spinner = document.createElement('div');
        spinner.className = 'loading-spinner';
        spinner.innerHTML = `
            <div class="spinner-container">
                <div class="spinner"></div>
            </div>
        `;
        
        // Add loading class to container
        container.classList.add('is-loading');
        
        // Append spinner to container
        container.appendChild(spinner);
    },

    /**
     * Hide loading spinner
     * @param {HTMLElement} container 
     */
    hideLoading: (container) => {
        // Remove loading class from container
        container.classList.remove('is-loading');
        
        // Remove spinner
        const spinner = container.querySelector('.loading-spinner');
        if (spinner) {
            spinner.remove();
        }
    },

    /**
     * Show alert message, This method takes the class alerts-container and appends the alert to it
     * @param {string} message 
     * @param {string} type - success, warning, error
     * @param {number} duration - milliseconds
     */
    showAlert: (message, type = 'success', duration = 3000) => {
        // Create alert element
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            <div class="alert-content">
                <i class="alert-icon fas ${UI.getAlertIcon(type)}"></i>
                <span class="alert-message">${message}</span>
            </div>
        `;
        
        // Add to alerts container or create one
        let alertsContainer = document.querySelector('.alerts-container');
        if (alertsContainer.classList.contains('hidden')) {
            alertsContainer.classList.remove('hidden');
        }
        if (!alertsContainer) {
            alertsContainer = document.createElement('div');
            alertsContainer.className = 'alerts-container';
            document.body.appendChild(alertsContainer);
        }
        
        alertsContainer.appendChild(alert);
        
        // Add animation class for showing alert
        setTimeout(() => {
            alert.classList.add('show');
        }, 10);
        
        // Auto close after duration
        if (duration > 0) {
            setTimeout(() => {
                UI.closeAlert(alert);
            }, duration);
        }
        
        // reset the alert container
        alert.classList.remove('show');
        alert.classList.add('hide');
        
        return alert;
    },

    /**
     * Close alert
     * @param {HTMLElement} alert 
     */
    closeAlert: (alert) => {
        alert.classList.remove('show');
        alert.classList.add('hide');
        
        // Remove after animation completes
        setTimeout(() => {
            alert.remove();
            
            // Remove container if empty
            const alertsContainer = document.querySelector('.alerts-container');
            if (alertsContainer && !alertsContainer.hasChildNodes()) {
                alertsContainer.remove();
            }
        }, 300);
    },

    /**
     * Get icon for alert type
     * @param {string} type 
     * @returns {string} Icon class
     */
    getAlertIcon: (type) => {
        switch (type) {
            case 'success':
                return 'fa-check-circle';
            case 'warning':
                return 'fa-exclamation-triangle';
            case 'error':
                return 'fa-times-circle';
            default:
                return 'fa-info-circle';
        }
    },

    /**
     * Show success message
     * @param {string} message 
     */
    showSuccess: (message) => {
        return UI.showAlert(message, 'success');
    },

    /**
     * Show error message
     * @param {string} message 
     */
    showError: (message) => {
        return UI.showAlert(message, 'error', 5000);
    },

    /**
     * Show modal dialog
     * @param {Object} options 
     */
    showModal: (options) => {
        const {
            title = '',
            content = '',
            buttons = [],
            size = 'medium',
            closable = true,
            onClose = null
        } = options;
        
        // Create modal element
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        
        // Modal HTML structure
        modal.innerHTML = `
            <div class="modal-container modal-${size}">
                <div class="modal-header">
                    <h3 class="modal-title">${title}</h3>
                    ${closable ? '<button class="modal-close"><i class="fas fa-times"></i></button>' : ''}
                </div>
                <div class="modal-content">
                    ${content}
                </div>
                <div class="modal-footer">
                    ${buttons.map(btn => `
                        <button class="btn ${btn.class || 'btn-secondary'}" data-action="${btn.action || ''}">
                            ${btn.text}
                        </button>
                    `).join('')}
                </div>
            </div>
        `;
        
        // Add to document
        document.body.appendChild(modal);
        
        // Prevent body scrolling
        document.body.classList.add('modal-open');
        
        // Add animation class
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        
        // Close button functionality
        if (closable) {
            const closeBtn = modal.querySelector('.modal-close');
            closeBtn.addEventListener('click', () => {
                UI.hideModal(modal);
                if (onClose) onClose();
            });
            
            // Close on overlay click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    UI.hideModal(modal);
                    if (onClose) onClose();
                }
            });
        }
        
        // Button actions
        buttons.forEach((btn, index) => {
            const btnElement = modal.querySelectorAll('.modal-footer .btn')[index];
            btnElement.addEventListener('click', () => {
                if (btn.callback) {
                    btn.callback(modal);
                }
                
                if (btn.closeModal !== false) {
                    UI.hideModal(modal);
                }
            });
        });
        
        return modal;
    },

    /**
     * Hide modal dialog
     * @param {HTMLElement} modal 
     */
    hideModal: (modal) => {
        modal.classList.remove('show');
        modal.classList.add('hide');
        
        // Remove after animation completes
        setTimeout(() => {
            modal.remove();
            
            // Restore body scrolling if no other modals
            if (!document.querySelector('.modal-overlay')) {
                document.body.classList.remove('modal-open');
            }
        }, 300);
    },

    /**
     * Initialize image gallery
     * @param {HTMLElement} container 
     * @param {Object} options 
     */
    initGallery: (container, options = {}) => {
        // Implementation details will go here
    },

    /**
     * Handle form submission with loading state
     * @param {HTMLFormElement} form 
     * @param {Function} submitHandler 
     */
    handleFormSubmit: (form, submitHandler) => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = form.querySelector('[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            try {
                // Call submit handler
                await submitHandler(form);
            } catch (error) {
                console.error('Form submission error:', error);
            } finally {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
};

export default UI;