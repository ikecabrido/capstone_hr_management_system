/**
 * Policy Documentation Module JavaScript
 * HR Legal & Compliance System
 * Bestlink College of the Philippines
 */

(function($) {
    'use strict';
    
    // ============================================
    // INITIALIZATION
    // ============================================
    
    /**
     * Initialize module on DOM ready
     */
    function init() {
        initFormHandling();
        initPersistentTabs();
        initAutoDismissAlerts();
        initReminderModal();
        initAutoOpenModal();
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // ============================================
    // FORM HANDLING
    // ============================================
    
    /**
     * Initialize form handling
     */
    function initFormHandling() {
        const forms = document.querySelectorAll('.policy-form');
        
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                }
                
                // Let the form submit normally - this is most reliable
            });
        });
    }
    
    // ============================================
    // PERSISTENT TABS
    // ============================================
    
    /**
     * Initialize persistent tab selection using sessionStorage
     */
    function initPersistentTabs() {
        const tabContainer = document.getElementById('policyTabs');
        if (!tabContainer) return;
        
        const STORAGE_KEY = 'policyActiveTab';
        const tabs = tabContainer.querySelectorAll('.nav-link');
        
        // Check if there's a stored tab preference
        const urlParams = new URLSearchParams(window.location.search);
        const currentView = urlParams.get('view');
        
        // Get stored tab FROM lc_sessionStorage
        let storedTab = sessionStorage.getItem(STORAGE_KEY);
        
        // If no view parameter in URL and we have a stored tab, restore it
        if (!currentView && storedTab) {
            // Remove active class FROM lc_all tabs
            tabs.forEach(function(tab) {
                tab.classList.remove('active');
            });
            
            // Add active class to the stored tab
            const activeTab = tabContainer.querySelector('[data-tab="' + storedTab + '"]');
            if (activeTab) {
                activeTab.classList.add('active');
            }
        }
        
        // Save active tab to sessionStorage before page unloads
        window.addEventListener('beforeunload', function() {
            const activeTab = tabContainer.querySelector('.nav-link.active');
            if (activeTab) {
                const tabId = activeTab.getAttribute('data-tab');
                sessionStorage.setItem(STORAGE_KEY, tabId);
            }
        });
        
        // Also save when clicking on a tab
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                sessionStorage.setItem(STORAGE_KEY, tabId);
            });
        });
    }
    
    // ============================================
    // AUTO DISMISS ALERTS
    // ============================================
    
    /**
     * Initialize auto-dismiss alerts
     */
    function initAutoDismissAlerts() {
        const autoDismissAlerts = document.querySelectorAll('.alert.auto-dismiss[data-auto-dismiss]');
        
        autoDismissAlerts.forEach(function(alert) {
            const dismissTime = parseInt(alert.getAttribute('data-auto-dismiss'), 10) || 3000;
            
            setTimeout(function() {
                alert.classList.add('fade-out');
                
                // Remove FROM lc_DOM after animation completes
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            }, dismissTime);
        });
    }
    
    // ============================================
    // REMINDER MODAL HANDLING
    // ============================================
    
    /**
     * Initialize reminder modal functionality
     */
    function initReminderModal() {
        const reminderModal = document.getElementById('reminderModal');
        if (!reminderModal) return;
        
        // Handle remind button clicks
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.remind-btn');
            if (btn) {
                const ackId = btn.dataset.ackId;
                const employeeName = btn.dataset.employeeName;
                const policyTitle = btn.dataset.policyTitle;
                const employeeId = btn.dataset.employeeId;
                const policyId = btn.dataset.policyId;
                
                // Populate modal fields
                document.getElementById('reminderAckId').value = ackId;
                document.getElementById('reminderEmployeeName').value = employeeName;
                document.getElementById('reminderPolicyTitle').value = policyTitle;
                
                // Generate default message
                const defaultMessage = generateDefaultMessage(employeeName, policyTitle);
                document.getElementById('reminderMessage').value = defaultMessage;
                document.getElementById('previewText').textContent = defaultMessage;
            }
        });
        
        // Handle template checkbox
        const useTemplateCheckbox = document.getElementById('useTemplate');
        const messageTextarea = document.getElementById('reminderMessage');
        const previewText = document.getElementById('previewText');
        const employeeName = document.getElementById('reminderEmployeeName').value;
        const policyTitle = document.getElementById('reminderPolicyTitle').value;
        
        if (useTemplateCheckbox) {
            useTemplateCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    const empName = document.getElementById('reminderEmployeeName').value;
                    const polTitle = document.getElementById('reminderPolicyTitle').value;
                    messageTextarea.value = generateDefaultMessage(empName, polTitle);
                } else {
                    messageTextarea.value = '';
                }
                updatePreview();
            });
        }
        
        // Update preview on message change
        if (messageTextarea) {
            messageTextarea.addEventListener('input', updatePreview);
        }
        
        function updatePreview() {
            const message = messageTextarea.value || 'No message entered';
            previewText.textContent = message;
        }
        
        // Handle form submission
        const reminderForm = document.getElementById('reminderForm');
        if (reminderForm) {
            reminderForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = reminderForm.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                
                // Get form data
                const formData = new FormData(reminderForm);
                
                // Send AJAX request
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Close modal
                    $('#reminderModal').modal('hide');
                    
                    // Show success message
                    showAlert('Reminder sent successfully!', 'success');
                    
                    // Reset form
                    reminderForm.reset();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to send reminder. Please try again.', 'danger');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
            });
        }
    }
    
    /**
     * Auto-open modal based on URL parameter
     */
    function initAutoOpenModal() {
        const urlParams = new URLSearchParams(window.location.search);
        const action = urlParams.get('action');
        
        if (action === 'add') {
            // Open the add policy modal
            const addPolicyModal = document.getElementById('addPolicyModal');
            if (addPolicyModal) {
                $(addPolicyModal).modal('show');
            }
        }
    }
    
    /**
     * Generate default reminder message
     */
    function generateDefaultMessage(employeeName, policyTitle) {
        const today = new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        return `Dear ${employeeName},

This is a friendly reminder to acknowledge the policy "${policyTitle}".

Please review and acknowledge the policy at your earliest convenience.

If you have any questions or need clarification, please don't hesitate to reach out.

Thank you for your attention to this matter.

Best regards,
Human Resources Department
Date: ${today}`;
    }
    
    /**
     * Show alert message
     */
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible auto-dismiss`;
        alertDiv.setAttribute('data-auto-dismiss', '3000');
        alertDiv.innerHTML = `
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            ${message}
        `;
        
        // Insert at top of content
        const contentWrapper = document.querySelector('.content-wrapper .content');
        if (contentWrapper) {
            contentWrapper.insertBefore(alertDiv, contentWrapper.firstChild);
        }
        
        // Auto dismiss
        setTimeout(function() {
            alertDiv.classList.add('fade-out');
            setTimeout(function() {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 500);
        }, 3000);
    }
    
})(jQuery);
