/**
 * Labor Law Compliance Module JavaScript
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
        initLaborLawModalFix();
        initFormHandling();
        initAutoRefresh();
        initKeyboardShortcuts();
        initModalEnhancements();
        initTableRowHighlighting();
        initAutoDismissAlerts();
        initPersistentTabs();
    }

    /**
     * Labor Law page: open modals via capture-phase clicks + data-labor-law-modal-target.
     * Runs before bubbling handlers (row clicks, AdminLTE, etc.) and does not rely on
     * Bootstrap's data-api or jQuery delegated binding to the trigger nodes.
     */
    function initLaborLawModalFix() {
        if (!document.getElementById('laborLawTabs')) {
            return;
        }

        $('.modal').appendTo(document.body);

        if (window.__laborLawModalCaptureBound) {
            return;
        }
        window.__laborLawModalCaptureBound = true;

        document.addEventListener('click', function(e) {
            if (!document.getElementById('laborLawTabs')) {
                return;
            }
            var el = e.target.closest('[data-labor-law-modal-target]');
            if (!el) {
                return;
            }
            var sel = el.getAttribute('data-labor-law-modal-target');
            if (!sel || sel.charAt(0) !== '#') {
                return;
            }
            // getElementById: querySelector('#viewModal1.5') breaks — "." is a class selector in CSS
            var modalEl = document.getElementById(sel.slice(1));
            if (!modalEl || !modalEl.classList.contains('modal')) {
                return;
            }
            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();
            if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.modal !== 'function') {
                return;
            }
            window.jQuery(modalEl).modal('show');
        }, true);
    }

    /**
     * Initialize persistent tab selection using sessionStorage
     * Saves the active tab before page unload and restores it on page load
     */
    function initPersistentTabs() {
        const tabContainer = document.getElementById('laborLawTabs');
        if (!tabContainer) return;
        
        const STORAGE_KEY = 'laborLawActiveTab';
        const tabs = tabContainer.querySelectorAll('.nav-link');
        
        // Check if there's a stored tab preference FROM lc_a previous page load
        // If the current page doesn't have a view parameter, use the stored value
        const urlParams = new URLSearchParams(window.location.search);
        const currentView = urlParams.get('view');
        
        // Get the stored tab FROM lc_sessionStorage
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

    /**
     * Initialize auto-dismiss alerts
     * Alerts with data-auto-dismiss attribute will automatically disappear after specified milliseconds
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
     * Initialize form handling - uses standard form submission for reliability
     */
    function initFormHandling() {
        const forms = document.querySelectorAll('.labor-law-form');
        
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                }
                
                // Let the form submit normally - this is most reliable
                // The page will reload and PHP will process the form
            });
        });
    }
    
    // ============================================
    // AUTO REFRESH
    // ============================================
    
    /**
     * Initialize auto-refresh functionality
     */
    function initAutoRefresh() {
        // Auto-refresh every 5 minutes (300000 ms)
        const REFRESH_INTERVAL = 300000;
        let refreshTimer;
        
        function startAutoRefresh() {
            refreshTimer = setInterval(function() {
                // Check if page is visible before refreshing
                if (!document.hidden) {
                    // Optionally refresh - uncomment below
                    // window.location.reload();
                }
            }, REFRESH_INTERVAL);
        }
        
        function stopAutoRefresh() {
            if (refreshTimer) {
                clearInterval(refreshTimer);
            }
        }
        
        // Start auto-refresh
        startAutoRefresh();
        
        // Stop on page visibility change (optional)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                startAutoRefresh();
            }
        });
    }
    
    // ============================================
    // KEYBOARD SHORTCUTS
    // ============================================
    
    /**
     * Initialize keyboard shortcuts
     */
    function initKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + N: New compliance item
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                const addModal = document.getElementById('addComplianceModal');
                if (addModal) {
                    $(addModal).modal('show');
                }
            }
            
            // Escape: Close modals
            if (e.key === 'Escape') {
                $('.modal').modal('hide');
            }
            
            // Ctrl/Cmd + F: Focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
    }
    
    // ============================================
    // MODAL ENHANCEMENTS
    // ============================================
    
    /**
     * Initialize modal enhancements
     */
    function initModalEnhancements() {
        // Center modals on resize
        $(window).on('resize', function() {
            $('.modal:visible').each(function() {
                const modal = $(this);
                modal.css('display', 'block');
                const dialog = modal.find('.modal-dialog');
                const top = Math.max(0, (($(window).height() - dialog.outerHeight()) / 2) + $(window).scrollTop());
                dialog.css('margin-top', top);
            });
        });
        
        // Handle modal shown event
        $('.modal').on('shown.bs.modal', function() {
            // Focus first input in modal
            const firstInput = $(this).find('input:not([type="hidden"]), select, textarea')[0];
            if (firstInput) {
                setTimeout(function() {
                    firstInput.focus();
                }, 100);
            }
        });
        
        // Clear form on modal close
        $('.modal').on('hidden.bs.modal', function() {
            const form = $(this).find('form');
            if (form.length) {
                form[0].reset();
            }
        });
    }
    
    // ============================================
    // TABLE ENHANCEMENTS
    // ============================================
    
    /**
     * Initialize table row highlighting
     */
    function initTableRowHighlighting() {
        const tableRows = document.querySelectorAll('.table tbody tr');
        
        tableRows.forEach(function(row) {
            // Hover effect
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f5f5f5';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
            
            // Click to select (do not steal clicks FROM lc_actions / modal triggers)
            row.addEventListener('click', function(e) {
                if (e.target.closest('button, a, [data-labor-law-modal-target], .btn')) {
                    return;
                }
                const checkbox = this.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                }
            });
        });
    }
    
    // ============================================
    // EXPORT FUNCTIONS
    // ============================================
    
    // Export public functions
    window.LaborLawCompliance = {
        /**
         * Refresh compliance data
         */
        refreshData: function() {
            window.location.reload();
        },
        
        /**
         * Show add modal
         */
        showAddModal: function() {
            $('#addComplianceModal').modal('show');
        },
        
        /**
         * Filter by status
         */
        filterByStatus: function(status) {
            const url = new URL(window.location.href);
            url.searchParams.set('status', status);
            url.searchParams.set('view', 'checklist');
            window.location.href = url.toString();
        },
        
        /**
         * Filter by category
         */
        filterByCategory: function(category) {
            const url = new URL(window.location.href);
            url.searchParams.set('category', category);
            url.searchParams.set('view', 'checklist');
            window.location.href = url.toString();
        },
        
        /**
         * Search compliance items
         */
        search: function(query) {
            const url = new URL(window.location.href);
            url.searchParams.set('search', query);
            url.searchParams.set('view', 'checklist');
            window.location.href = url.toString();
        },
        
        /**
         * Clear all filters
         */
        clearFilters: function() {
            const url = new URL(window.location.href);
            url.searchParams.delete('status');
            url.searchParams.delete('category');
            url.searchParams.delete('search');
            window.location.href = url.toString();
        },
        
        /**
         * Navigate to view
         */
        navigateTo: function(view) {
            const url = new URL(window.location.href);
            url.searchParams.set('view', view);
            window.location.href = url.toString();
        }
    };
    
})(jQuery);
