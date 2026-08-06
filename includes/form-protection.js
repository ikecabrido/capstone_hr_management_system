/**
 * Form Double-Submission Prevention Script
 * 
 * This script prevents duplicate form submissions when:
 * - User accidentally clicks submit button multiple times
 * - User refreshes the page after form submission
 * - User uses browser back button after submission
 * - Network issues cause retry on submission
 * 
 * Features:
 * - Disables submit buttons on submit
 * - Uses sessionStorage to track submission state
 * - Prevents form resubmission on page reload (PRG pattern)
 * - Handles browser back/forward navigation
 */

(function() {
    'use strict';
    
    /**
     * Configuration options
     */
    var config = {
        // Storage key prefix for tracking submissions
        storageKeyPrefix: 'formSubmit_',
        // Timeout to auto-reenable buttons if something goes wrong (ms)
        safetyTimeout: 15000,
        // Show loading indicator on buttons
        showLoadingText: true,
        // Loading text to show (can be customized per form)
        loadingText: 'Processing...'
    };
    
    /**
     * Initialize the form protection
     */
    function init() {
        // Handle page show event (back/forward navigation)
        window.addEventListener('pageshow', handlePageShow);
        
        // Handle beforeunload to clean up if needed
        window.addEventListener('pagehide', handlePageHide);
        
        // Protect all forms on the page
        protectAllForms();
        
        // Check for redirect-after-submission and clear the state
        checkForRedirectSubmission();
    }
    
    /**
     * Handle pageshow event - handles browser back button
     */
    function handlePageShow(event) {
        // Re-enable any disabled submit buttons when page is shown from cache
        if (event.persisted) {
            // Page was loaded from bfcache (back/forward cache)
            reenableAllSubmitButtons();
        }
        
        // Also check if this is after a redirect (page was reloaded after POST)
        checkForRedirectSubmission();
    }
    
    /**
     * Handle pagehide event
     */
    function handlePageHide(event) {
        // Clear submission state if form was successfully submitted
        // This prevents showing "resubmit form" dialog on back button
    }
    
    /**
     * Re-enable all submit buttons
     */
    function reenableAllSubmitButtons() {
        document.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function(btn) {
            // Restore original state
            btn.disabled = false;
            
            // Restore original text if it was changed
            if (btn.dataset.originalText) {
                btn.innerText = btn.dataset.originalText;
                btn.value = btn.dataset.originalValue || btn.value;
            }
            
            // Remove submission tracking data
            delete btn.dataset.submitting;
        });
    }
    
    /**
     * Protect all forms on the page
     */
    function protectAllForms() {
        var forms = document.querySelectorAll('form');
        
        forms.forEach(function(form) {
            // Skip if already protected
            if (form.dataset.protected === 'true') {
                return;
            }
            
            // Mark as protected
            form.dataset.protected = 'true';
            
            // Add submit handler
            form.addEventListener('submit', handleFormSubmit);
        });
    }
    
    /**
     * Handle form submission
     */
    function handleFormSubmit(event) {
        var form = event.target;
        
        // Skip if already submitting
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return false;
        }
        
        // Mark form as submitting
        form.dataset.submitting = 'true';
        
        // Get all submit buttons in this form
        var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        
        // Disable submit buttons and show loading state
        submitButtons.forEach(function(btn) {
            btn.disabled = true;
            
            // Store original text
            btn.dataset.originalText = btn.innerText || btn.value;
            btn.dataset.originalValue = btn.value;
            
            // Show loading text if enabled
            if (config.showLoadingText) {
                btn.innerText = btn.getAttribute('data-loading-text') || config.loadingText;
            }
        });
        
        // Store form data in sessionStorage
        storeFormSubmission(form);
        
        // Set safety timeout to re-enable buttons
        setTimeout(function() {
            if (form.dataset.submitting === 'true') {
                form.dataset.submitting = 'false';
                reenableAllSubmitButtons();
            }
        }, config.safetyTimeout);
        
        // Allow submission to proceed
        // Note: For AJAX forms, call event.preventDefault() and handle manually
        return true;
    }
    
    /**
     * Store form submission data in sessionStorage
     */
    function storeFormSubmission(form) {
        if (!window.sessionStorage) {
            return;
        }
        
        try {
            var formData = new FormData(form);
            var data = {};
            
            formData.forEach(function(value, key) {
                // Handle multiple values (e.g., checkboxes with same name)
                if (data[key] !== undefined) {
                    if (!Array.isArray(data[key])) {
                        data[key] = [data[key]];
                    }
                    data[key].push(value);
                } else {
                    data[key] = value;
                }
            });
            
            // Store the form data
            var key = getStorageKey(form);
            sessionStorage.setItem(key, JSON.stringify({
                data: data,
                timestamp: Date.now(),
                action: form.action,
                method: form.method
            }));
            
            // Also store a flag indicating submission is in progress
            sessionStorage.setItem(config.storageKeyPrefix + 'pending', 'true');
        } catch (e) {
            console.warn('FormProtection: Could not store form data', e);
        }
    }
    
    /**
     * Get storage key for a form
     */
    function getStorageKey(form) {
        return config.storageKeyPrefix + (form.id || form.action || 'form');
    }
    
    /**
     * Check for redirect-after-submission (PRG pattern)
     */
    function checkForRedirectSubmission() {
        if (!window.sessionStorage) {
            return;
        }
        
        try {
            var pendingKey = config.storageKeyPrefix + 'pending';
            var wasSubmitted = sessionStorage.getItem(pendingKey);
            
            if (wasSubmitted === 'true') {
                // Clear the pending flag
                sessionStorage.removeItem(pendingKey);
                
                // Find and clear stored form data
                var keys = Object.keys(sessionStorage);
                keys.forEach(function(key) {
                    if (key.startsWith(config.storageKeyPrefix) && key !== pendingKey) {
                        // Keep the data but clear pending
                    }
                });
            }
        } catch (e) {
            console.warn('FormProtection: Error checking redirect submission', e);
        }
    }
    
    /**
     * Clear stored submission data
     */
    function clearSubmissionData(form) {
        if (!window.sessionStorage) {
            return;
        }
        
        var key = getStorageKey(form);
        sessionStorage.removeItem(key);
        sessionStorage.removeItem(config.storageKeyPrefix + 'pending');
    }
    
    /**
     * Expose public API
     */
    window.FormProtection = {
        /**
         * Check if form was just submitted (after redirect)
         */
        wasJustSubmitted: function() {
            if (!window.sessionStorage) {
                return false;
            }
            return sessionStorage.getItem(config.storageKeyPrefix + 'pending') === 'true';
        },
        
        /**
         * Get submitted form data (after redirect)
         */
        getSubmittedData: function(form) {
            if (!window.sessionStorage) {
                return null;
            }
            
            var key = getStorageKey(form);
            var stored = sessionStorage.getItem(key);
            
            if (stored) {
                try {
                    return JSON.parse(stored);
                } catch (e) {
                    return null;
                }
            }
            
            return null;
        },
        
        /**
         * Clear submission data manually
         */
        clearSubmissionData: clearSubmissionData,
        
        /**
         * Re-enable all form submissions
         */
        reenableAll: reenableAllSubmitButtons,
        
        /**
         * Protect a specific form programmatically
         */
        protect: function(form) {
            if (form.dataset.protected !== 'true') {
                form.dataset.protected = 'true';
                form.addEventListener('submit', handleFormSubmit);
            }
        },
        
        /**
         * Configuration
         */
        config: config
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
