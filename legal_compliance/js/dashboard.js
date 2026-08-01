/**
 * Legal & Compliance Dashboard - JavaScript
 * Handles chart rendering and interactive functionality
 */

(function() {
    'use strict';
    
    // Chart instances storage
    let incidentChart = null;
    let riskChart = null;
    
    /**
     * Initialize dashboard charts
     * @param {Object} data - Dashboard data object
     */
    function initCharts(data) {
        initIncidentChart(data.incidentByStatus);
        initRiskChart(data.risksByLevel);
    }
    
    /**
     * Initialize Incident Bar Chart
     * @param {Object} incidentData - Incident counts by status
     */
    function initIncidentChart(incidentData) {
        const ctx = document.getElementById('incidentChart');
        if (!ctx) {
            console.error('Canvas element #incidentChart not found!');
            return;
        }
        
        console.log('Initializing Incident Doughnut Chart (v2) with data:', incidentData);
        
        // Destroy existing chart if any
        if (incidentChart) {
            incidentChart.destroy();
        }
        
        const dataValues = [
            incidentData.submitted || 0,
            incidentData.under_review || 0,
            incidentData.investigation || 0,
            incidentData.escalated || 0,
            incidentData.resolved || 0,
            incidentData.closed || 0
        ];

        try {
            incidentChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Submitted', 'Under Review', 'Investigation', 'Escalated', 'Resolved', 'Closed'],
                    datasets: [{
                        data: dataValues,
                        backgroundColor: [
                            '#6c757d', // submitted - gray
                            '#17a2b8', // under_review - info
                            '#ffc107', // investigation - warning
                            '#dc3545', // escalated - danger
                            '#28a745', // resolved - success
                            '#343a40'  // closed - dark
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 60,
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            usePointStyle: true,
                            fontFamily: 'Source Sans Pro',
                            fontSize: 11
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                const dataset = data.datasets[tooltipItem.datasetIndex];
                                const total = dataset.data.reduce((a, b) => a + b, 0);
                                const value = dataset.data[tooltipItem.index];
                                const label = data.labels[tooltipItem.index];
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            });
            console.log('Incident Doughnut Chart created successfully');
        } catch (e) {
            console.error('Error creating Incident Chart:', e);
        }
    }
    
    /**
     * Initialize Risk Doughnut Chart
     * @param {Object} riskData - Risk counts by level
     */
    function initRiskChart(riskData) {
        const ctx = document.getElementById('riskChart');
        if (!ctx) return;
        
        // Destroy existing chart if any
        if (riskChart) {
            riskChart.destroy();
        }
        
        try {
            riskChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Low', 'Medium', 'High'],
                    datasets: [{
                        data: [
                            riskData.low || 0,
                            riskData.medium || 0,
                            riskData.high || 0
                        ],
                        backgroundColor: [
                            '#28a745',
                            '#ffc107',
                            '#dc3545'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 60,
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            fontFamily: 'Source Sans Pro',
                            fontSize: 12
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                const dataset = data.datasets[tooltipItem.datasetIndex];
                                const total = dataset.data.reduce((a, b) => a + b, 0);
                                const value = dataset.data[tooltipItem.index];
                                const label = data.labels[tooltipItem.index];
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Error creating Risk Chart:', e);
        }
    }
    
    /**
     * Refresh dashboard data via AJAX
     */
    function refreshData() {
        // Add refresh button functionality
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                location.reload();
            });
        }
    }
    
    /**
     * Initialize quick action handlers
     */
    function initQuickActions() {
        const quickActions = document.querySelectorAll('.quick-action-btn');
        quickActions.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('onclick');
                if (href) {
                    // Extract URL FROM lc_onclick
                    const match = href.match(/window\.location\.href='([^']+)'/);
                    if (match && match[1]) {
                        window.location.href = match[1];
                    }
                }
            });
        });
    }
    
    /**
     * Initialize alert notification handlers
     */
    function initAlerts() {
        const alertItems = document.querySelectorAll('.alert-item');
        alertItems.forEach(function(item) {
            item.addEventListener('click', function() {
                // Mark as read or navigate to detail
                this.classList.remove('alert-item');
                this.classList.add('alert-item-read');
            });
        });
    }
    
    /**
     * Handle responsive chart resizing
     */
    function handleResize() {
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (incidentChart) incidentChart.resize();
                if (riskChart) riskChart.resize();
            }, 250);
        });
    }
    
    /**
     * Initialize scroll enhancements for data grids
     */
    function initScrollEnhancements() {
        // Add smooth scrolling to all scrollable containers
        const scrollContainers = document.querySelectorAll(
            '.data-grid-scroll-vertical, .data-grid-scroll-horizontal, .data-grid-scroll-both, .table-fixed-header tbody'
        );
        
        scrollContainers.forEach(function(container) {
            // Enable momentum scrolling on iOS
            container.style.webkitOverflowScrolling = 'touch';
            
            // Add scroll event listener for custom behavior
            container.addEventListener('scroll', function() {
                // Store scroll position in sessionStorage for persistence
                const id = container.id || container.className;
                if (id) {
                    sessionStorage.setItem('scroll_' + id, container.scrollTop);
                }
            });
        });
        
        // Restore scroll positions on page load
        scrollContainers.forEach(function(container) {
            const id = container.id || container.className;
            if (id) {
                const savedPosition = sessionStorage.getItem('scroll_' + id);
                if (savedPosition !== null) {
                    container.scrollTop = parseInt(savedPosition, 10);
                }
            }
        });
        
        // Add keyboard navigation support for tables
        const tableRows = document.querySelectorAll('.table-fixed-header tbody tr');
        tableRows.forEach(function(row, index) {
            row.setAttribute('tabindex', '0');
            row.setAttribute('data-row-index', index);
            
            row.addEventListener('keydown', function(e) {
                const tbody = this.closest('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const currentIndex = rows.indexOf(this);
                
                if (e.key === 'ArrowDown' && currentIndex < rows.length - 1) {
                    e.preventDefault();
                    rows[currentIndex + 1].focus();
                } else if (e.key === 'ArrowUp' && currentIndex > 0) {
                    e.preventDefault();
                    rows[currentIndex - 1].focus();
                }
            });
        });
    }
    
    /**
     * Add hover scroll buttons for data grids
     */
    function addScrollButtons() {
        const scrollContainers = document.querySelectorAll('.data-grid-scroll-both, .data-grid-scroll-vertical');
        
        scrollContainers.forEach(function(container) {
            // Add scroll indicator shadows
            container.classList.add('scroll-shadow-container');
        });
    }
    
    /**
     * Initialize dashboard on DOM ready
     */
    function init() {
        console.log('Dashboard init started');
        // Simple alert to test if script runs
        // alert('Dashboard JS is running');
        
        if (typeof Chart === 'undefined') {
            console.error('Chart.js library is NOT loaded!');
            return;
        }
        console.log('Chart.js version:', Chart.version);
        try {
            // Get dashboard data FROM lc_PHP variable
            const dashboardData = window.dashboardData || {
                incidentByStatus: { 
                    submitted: 0, 
                    under_review: 0, 
                    investigation: 0, 
                    escalated: 0, 
                    resolved: 0, 
                    closed: 0 
                },
                risksByLevel: { low: 0, medium: 0, high: 0 }
            };
            console.log('Dashboard data:', dashboardData);
            
            initCharts(dashboardData);
            refreshData();
            initQuickActions();
            initAlerts();
            handleResize();
            initScrollEnhancements();
            addScrollButtons();
        } catch (error) {
            console.error('Dashboard init failed:', error);
        }
    }
    
    // Use jQuery's ready if available, otherwise DOMContentLoaded
    if (window.jQuery) {
        $(document).ready(init);
    } else {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    }
    
    // Export functions for external use
    window.LegalComplianceDashboard = {
        initCharts: initCharts,
        refreshData: refreshData
    };
    
})();