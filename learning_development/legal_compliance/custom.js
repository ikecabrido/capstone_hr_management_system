const toggleBtn = document.getElementById("darkToggle");
const icon = document.getElementById("themeIcon");

// Current filter state
var currentFilter = 'all';

function setTheme(mode, animate = true) {
  if (animate) {
    icon.classList.remove("animate");
    void icon.offsetWidth;
    icon.classList.add("animate");
  }

  setTimeout(() => {
    if (mode === "dark") {
      document.body.classList.add("dark-mode");
      icon.classList.replace("fa-moon", "fa-sun");
      localStorage.setItem("theme", "dark");
    } else {
      document.body.classList.remove("dark-mode");
      icon.classList.replace("fa-sun", "fa-moon");
      localStorage.setItem("theme", "light");
    }
    icon.classList.remove("animate");
  }, 200);
}

toggleBtn.addEventListener("click", function (e) {
  e.preventDefault();
  const isDark = document.body.classList.contains("dark-mode");
  setTheme(isDark ? "light" : "dark");
});

/**
 * Filter employees by compliance status
 */
function filterEmployees(filter) {
    currentFilter = filter;
    
    // Remove highlight from all info-boxes
    document.querySelectorAll('.info-box.clickable').forEach(function(box) {
        box.classList.remove('filter-active');
    });
    
    // Add highlight to clicked box (find by onclick attribute)
    document.querySelectorAll('.info-box.clickable').forEach(function(box) {
        if (box.getAttribute('onclick') && box.getAttribute('onclick').includes("filterEmployees('" + filter + "')")) {
            box.classList.add('filter-active');
        }
    });
    
    // Show/hide employee rows based on filter
    var employeeRows = document.querySelectorAll('.employee-compliance-row');
    
    employeeRows.forEach(function(row) {
        var status = row.getAttribute('data-status');
        var show = false;
        
        switch(filter) {
            case 'all':
                show = true;
                break;
            case 'compliant':
                show = (status === 'compliant');
                break;
            case 'at_risk':
                show = (status === 'at_risk');
                break;
            case 'non_compliant':
                show = (status === 'non_compliant');
                break;
            case 'pending_ack':
                show = (row.getAttribute('data-pending-ack') === 'true');
                break;
            case 'active_cases':
                show = (row.getAttribute('data-active-cases') === 'true');
                break;
            case 'high_risk':
                show = (status === 'high_risk');
                break;
            default:
                show = true;
        }
        
        if (show) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show filter message
    updateFilterMessage(filter);
}

function updateFilterMessage(filter) {
    var messageEl = document.getElementById('filterMessage');
    if (!messageEl) {
        // Create message element if not exists
        var tableCard = document.querySelector('.card-table');
        if (tableCard) {
            messageEl = document.createElement('div');
            messageEl.id = 'filterMessage';
            messageEl.className = 'alert alert-info alert-dismissible';
            messageEl.style.marginBottom = '15px';
            messageEl.innerHTML = '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            tableCard.parentNode.insertBefore(messageEl, tableCard);
        }
    }
    
    if (messageEl) {
        if (filter === 'all') {
            messageEl.style.display = 'none';
        } else {
            var filterNames = {
                'compliant': 'Compliant Employees',
                'at_risk': 'At Risk Employees',
                'non_compliant': 'Non-Compliant Employees',
                'pending_ack': 'Pending Acknowledgments',
                'active_cases': 'Active Cases',
                'high_risk': 'High Risk Employees'
            };
            messageEl.innerHTML = '<button type="button" class="close" data-dismiss="alert">&times;</button>Showing: <strong>' + (filterNames[filter] || filter) + '</strong> <a href="#" onclick="filterEmployees(\'all\'); return false;">Clear filter</a>';
            messageEl.style.display = 'block';
        }
    }
}

/**
 * Show policies modal
 */
function showPolicies() {
    // Scroll to policies section or open modal
    var policiesSection = document.getElementById('policiesSection');
    if (policiesSection) {
        policiesSection.scrollIntoView({behavior: 'smooth'});
    } else {
        alert('Policies section will be shown here');
    }
}

const savedTheme = localStorage.getItem("theme") || "light";
setTheme(savedTheme, false);

// Calculate days between dates
function calculateDays(startDate, endDate) {
  const start = new Date(startDate);
  const end = new Date(endDate);
  const diffTime = Math.abs(end - start);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
  return diffDays;
}

// Auto-calculate leave days
document.addEventListener('DOMContentLoaded', function() {
  const startDateInput = document.getElementById('start_date');
  const endDateInput = document.getElementById('end_date');
  const totalDaysInput = document.getElementById('total_days');
  
  if (startDateInput && endDateInput && totalDaysInput) {
    endDateInput.addEventListener('change', function() {
      if (startDateInput.value && endDateInput.value) {
        const days = calculateDays(startDateInput.value, endDateInput.value);
        totalDaysInput.value = days;
      }
    });
  }
  
  // Initialize clickable card event listeners
  initClickableCards();
});

/**
 * Initialize clickable cards with event listeners
 * Uses event delegation for dynamically loaded content
 */
function initClickableCards() {
    // Use event delegation on document for dynamically loaded content
    document.addEventListener('click', function(e) {
        // Find the closest clickable info-box element
        const card = e.target.closest('.info-box.clickable');
        
        if (card) {
            // Prevent triggering if clicking on child elements that have their own handlers
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || 
                e.target.closest('a') || e.target.closest('button')) {
                return;
            }
            
            // Add click animation
            card.classList.add('clicked');
            setTimeout(function() {
                card.classList.remove('clicked');
            }, 300);
            
            // Create ripple effect
            createRippleEffect(e, card);
            
            // Show card details modal
            showCardDetails(card);
        }
    });
    
    // Add keyboard support for accessibility
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            const focusedCard = document.activeElement;
            if (focusedCard && focusedCard.classList.contains('info-box') && 
                focusedCard.classList.contains('clickable')) {
                e.preventDefault();
                showCardDetails(focusedCard);
            }
        }
    });
}

/**
 * Create ripple effect on click
 */
function createRippleEffect(event, element) {
    const ripple = document.createElement('span');
    ripple.classList.add('ripple');
    
    const rect = element.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    
    element.appendChild(ripple);
    
    setTimeout(function() {
        ripple.remove();
    }, 600);
}

/**
 * Show card details in modal
 */
function showCardDetails(cardElement) {
    // Get data attributes from the card
    const title = cardElement.getAttribute('data-title') || 'Card Details';
    const subtitle = cardElement.getAttribute('data-subtitle') || '';
    const description = cardElement.getAttribute('data-description') || 'No description available.';
    const score = cardElement.getAttribute('data-score') || cardElement.getAttribute('data-count') || '';
    const icon = cardElement.getAttribute('data-icon') || 'fa-info-circle';
    const iconClass = cardElement.getAttribute('data-icon-class') || 'bg-primary';
    const cardType = cardElement.getAttribute('data-card-type') || 'default';
    
    // Get modal elements
    const modal = document.getElementById('cardDetailsModal');
    const modalTitle = document.getElementById('cardDetailsTitle');
    const modalSubtitle = document.getElementById('cardDetailsSubtitle');
    const modalDescription = document.getElementById('cardDetailsDescription');
    const modalIcon = document.getElementById('cardDetailsIcon');
    
    // Set modal content
    modalTitle.textContent = title;
    modalSubtitle.textContent = subtitle;
    modalDescription.textContent = description;
    
    // Update icon
    modalIcon.className = 'info-box-icon ' + iconClass + ' elevation-1';
    modalIcon.innerHTML = '<i class="fas ' + icon + ' fa-2x"></i>';
    
    // Show the modal with Bootstrap
    if (modal) {
        $(modal).modal('show');
    }
}

/**
 * Show expanded card details in larger modal
 * This can be used for more detailed view if needed
 */
function showExpandedCardDetails(cardElement) {
    const title = cardElement.getAttribute('data-title') || 'Card Details';
    const cardType = cardElement.getAttribute('data-card-type') || 'default';
    const score = cardElement.getAttribute('data-score') || cardElement.getAttribute('data-count') || '';
    const subtitle = cardElement.getAttribute('data-subtitle') || '';
    const description = cardElement.getAttribute('data-description') || 'No description available.';
    
    const modal = document.getElementById('clickableCardModal');
    const modalTitle = document.getElementById('clickableCardTitle');
    const modalContent = document.getElementById('clickableCardContent');
    
    modalTitle.textContent = title;
    
    // Build detailed content based on card type
    let detailedContent = '';
    
    switch(cardType) {
        case 'overall':
            detailedContent = buildOverallComplianceContent(score, description);
            break;
        case 'non-compliant':
            detailedContent = buildNonCompliantContent(cardElement, description);
            break;
        case 'at-risk':
            detailedContent = buildAtRiskContent(cardElement, description);
            break;
        case 'active-cases':
            detailedContent = buildActiveCasesContent(cardElement, description);
            break;
        default:
            detailedContent = '<p>' + description + '</p>';
    }
    
    modalContent.innerHTML = detailedContent;
    
    if (modal) {
        $(modal).modal('show');
    }
}

/**
 * Build content for overall compliance card
 */
function buildOverallComplianceContent(score, description) {
    const scoreNum = parseInt(score) || 0;
    let statusClass = scoreNum >= 90 ? 'success' : (scoreNum >= 70 ? 'warning' : 'danger');
    let statusText = scoreNum >= 90 ? 'Excellent' : (scoreNum >= 70 ? 'Needs Attention' : 'Critical');
    
    return '
        <div class="text-center mb-4">
            <div class="score-circle ' + (scoreNum >= 90 ? 'excellent' : (scoreNum >= 70 ? 'good' : 'danger')) + ' mx-auto">
                ' + scoreNum + '%
            </div>
            <h4 class="mt-3 text-' + statusClass + '">' + statusText + '</h4>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-check-circle text-success mr-2"></i>Compliant</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-success">' + Math.round(scoreNum * 1.1) + '</h3>
                        <small class="text-muted">Employees</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-exclamation-circle text-warning mr-2"></i>Needs Action</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-warning">' + Math.round(scoreNum * 0.3) + '</h3>
                        <small class="text-muted">Employees</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> <strong>About Overall Compliance:</strong><br>
            ' + description + '
        </div>
    ';
}

/**
 * Build content for non-compliant card
 */
function buildNonCompliantContent(cardElement, description) {
    const count = cardElement.getAttribute('data-count') || '0';
    
    return '
        <div class="text-center mb-4">
            <div class="info-box-icon bg-danger elevation-1" style="width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                <i class="fas fa-times-circle fa-3x"></i>
            </div>
            <h3 class="mt-3 text-danger">' + count + ' Non-Compliant</h3>
            <p class="text-muted">Employees below 70% compliance</p>
        </div>
        <div class="alert alert-danger mt-3">
            <i class="fas fa-exclamation-triangle"></i> <strong>Action Required:</strong><br>
            ' + description + '
        </div>
        <div class="mt-3">
            <button class="btn btn-danger btn-block" onclick="filterEmployees(\'non_compliant\'); $(\'#clickableCardModal\').modal(\'hide\');">
                <i class="fas fa-filter"></i> View Non-Compliant Employees
            </button>
        </div>
    ';
}

/**
 * Build content for at-risk card
 */
function buildAtRiskContent(cardElement, description) {
    const count = cardElement.getAttribute('data-count') || '0';
    
    return '
        <div class="text-center mb-4">
            <div class="info-box-icon bg-warning elevation-1" style="width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                <i class="fas fa-exclamation-circle fa-3x"></i>
            </div>
            <h3 class="mt-3 text-warning">' + count + ' At Risk</h3>
            <p class="text-muted">Employees with 70-89% compliance</p>
        </div>
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle"></i> <strong>Attention Needed:</strong><br>
            ' + description + '
        </div>
        <div class="mt-3">
            <button class="btn btn-warning btn-block" onclick="filterEmployees(\'at_risk\'); $(\'#clickableCardModal\').modal(\'hide\');">
                <i class="fas fa-filter"></i> View At-Risk Employees
            </button>
        </div>
    ';
}

/**
 * Build content for active cases card
 */
function buildActiveCasesContent(cardElement, description) {
    const count = cardElement.getAttribute('data-count') || '0';
    
    return '
        <div class="text-center mb-4">
            <div class="info-box-icon bg-info elevation-1" style="width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                <i class="fas fa-gavel fa-3x"></i>
            </div>
            <h3 class="mt-3 text-info">' + count + ' Active Cases</h3>
            <p class="text-muted">Open compliance incidents</p>
        </div>
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> <strong>Active Cases:</strong><br>
            ' + description + '
        </div>
        <div class="mt-3">
            <button class="btn btn-info btn-block" onclick="filterEmployees(\'active_cases\'); $(\'#clickableCardModal\').modal(\'hide\');">
                <i class="fas fa-filter"></i> View Cases
            </button>
        </div>
    ';
}

// Make showCardDetails available globally
window.showCardDetails = showCardDetails;
window.showExpandedCardDetails = showExpandedCardDetails;
window.initClickableCards = initClickableCards;

/**
 * Handle nested modal stacking - ensures child modals appear on top
 * and parent modal's backdrop is properly managed
 */
function setupNestedModalHandling() {
    // When Send Reminder modal is opened from Risk Flag Details modal
    const sendReminderModal = document.getElementById('sendReminderModal');
    const riskFlagDetailsModal = document.getElementById('riskFlagDetailsModal');
    
    if (sendReminderModal) {
        sendReminderModal.addEventListener('shown.bs.modal', function() {
            // Get the highest z-index from all visible modals
            let highestZIndex = 1050; // Bootstrap's default modal z-index
            document.querySelectorAll('.modal:visible').forEach(function(modal) {
                const style = modal.getAttribute('style') || '';
                const match = style.match(/z-index:\s*(\d+)/);
                if (match) {
                    const z = parseInt(match[1]);
                    if (z > highestZIndex) highestZIndex = z;
                }
            });
            
            // Set z-index higher than the highest visible modal
            const newZIndex = highestZIndex + 20;
            sendReminderModal.style.zIndex = newZIndex;
            sendReminderModal.querySelector('.modal-dialog').style.zIndex = newZIndex + 10;
            
            // Only fix the topmost backdrop z-index, not all backdrops
            const backdrops = document.querySelectorAll('.modal-backdrop');
            if (backdrops.length > 0) {
                backdrops[backdrops.length - 1].style.zIndex = newZIndex - 10;
            }
        });
        
        // When Send Reminder modal is closed, properly clean up
        sendReminderModal.addEventListener('hidden.bs.modal', function() {
            // Only restore backdrop if there's still another modal visible
            const visibleModals = document.querySelectorAll('.modal.show');
            if (visibleModals.length > 0) {
                // Restore the parent modal's backdrop
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(function(backdrop) {
                    backdrop.style.display = '';
                    backdrop.classList.add('show');
                });
            }
            // Don't manually add show class - let Bootstrap handle it
        });
    }
    
    // Handle employee details modal
    const employeeDetailsModal = document.getElementById('employeeDetailsModal');
    if (employeeDetailsModal) {
        employeeDetailsModal.addEventListener('shown.bs.modal', function() {
            adjustModalZIndex(employeeDetailsModal, 1050);
        });
    }
}

/**
 * Adjust modal z-index to ensure proper stacking
 */
function adjustModalZIndex(modal, baseZIndex) {
    const modalContainer = modal.closest('.modal-open');
    if (modalContainer) {
        // Find all visible modals and adjust their z-index
        const allModals = modalContainer.querySelectorAll('.modal.show');
        let highestZIndex = baseZIndex;
        
        allModals.forEach(function(m) {
            const currentZ = parseInt(m.style.zIndex) || baseZIndex;
            if (currentZ > highestZIndex) {
                highestZIndex = currentZ;
            }
        });
        
        // Set this modal to be on top
        modal.style.zIndex = highestZIndex + 10;
        const dialog = modal.querySelector('.modal-dialog');
        if (dialog) {
            dialog.style.zIndex = highestZIndex + 11;
        }
    }
}

/**
 * Open a modal while properly handling parent modal's backdrop
 */
function openModalWithBackdropFix(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    // Let Bootstrap handle the backdrop - don't hide them manually
    $(modal).modal('show');
}

/**
 * Close nested modal and restore parent modal functionality
 */
function closeNestedModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    // Hide this modal
    $(modal).modal('hide');
    $(modal).modal('hide');
    
    // Restore any hidden backdrops after a short delay
    setTimeout(function() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(function(bp) {
            bp.style.display = '';
            bp.classList.add('show');
        });
    }, 300);
}

// Initialize nested modal handling when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setupNestedModalHandling();
});

// Make functions globally available
window.openModalWithBackdropFix = openModalWithBackdropFix;
window.closeNestedModal = closeNestedModal;

/**
 * Open Send Reminder Modal from Risk Flag Details
 * Properly handles the backdrop of the parent modal
 */
function sendReminderToEmployee() {
    // First, hide the Risk Flag Details modal's backdrop
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(function(backdrop) {
        backdrop.style.display = 'none';
        backdrop.classList.remove('show');
    });
    
    // Remove modal-open class from body
    document.body.classList.remove('modal-open');
    
    // Now show the Send Reminder modal
    const reminderModal = document.getElementById('sendReminderModal');
    if (reminderModal) {
        // Use default Bootstrap behavior (not static backdrop)
        $(reminderModal).modal('show');
    }
}

/**
 * Submit reminder - handles closing modals properly
 */
function submitReminder() {
    // Close the reminder modal
    const reminderModal = document.getElementById('sendReminderModal');
    if (reminderModal) {
        $(reminderModal).modal('hide');
    }
    
    // Restore any hidden backdrops after a delay
    setTimeout(function() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(function(backdrop) {
            backdrop.style.display = '';
            backdrop.classList.add('show');
        });
        
        // Focus back on the Risk Flag Details modal
        const riskModal = document.getElementById('riskFlagDetailsModal');
        if (riskModal) {
            $(riskModal).modal('handleUpdate');
        }
    }, 300);
    
    // Show success message (would normally send via AJAX)
    alert('Reminder sent successfully!');
}

// Make sendReminderToEmployee and submitReminder available globally
window.sendReminderToEmployee = sendReminderToEmployee;
window.submitReminder = submitReminder;

/**
 * Auto-hide Overall Compliance Score after 3 seconds
 * Uses jQuery fadeOut with proper cleanup to prevent memory leaks
 */
(function() {
    // Store timeout reference for cleanup
    var autoHideTimeout = null;
    
    function initOverallScoreAutoHide() {
        var $element = $('#overallComplianceScoreContainer');
        
        // Only proceed if element exists and hasn't been hidden already
        if ($element.length && !$element.hasClass('fade-out-hidden')) {
            
            // Set timeout with reference for potential cleanup
            autoHideTimeout = setTimeout(function() {
                // Add class to prevent multiple triggers
                $element.addClass('fade-out-hidden');
                
                // Use jQuery fadeOut for smooth animation
                $element.fadeOut(500, function() {
                    // Animation complete callback - element is now hidden
                    // This ensures proper cleanup
                    $(this).addClass('fade-out');
                });
                
                // Clear timeout reference after execution
                autoHideTimeout = null;
                
            }, 3000); // 3 seconds delay
        }
    }
    
    // Initialize on DOM ready
    $(document).ready(function() {
        initOverallScoreAutoHide();
    });
    
    // Cleanup function - can be called if needed to cancel the auto-hide
    window.cancelOverallScoreAutoHide = function() {
        if (autoHideTimeout) {
            clearTimeout(autoHideTimeout);
            autoHideTimeout = null;
        }
    };
    
    // Re-show function - useful for testing or reset
    window.showOverallComplianceScore = function() {
        var $element = $('#overallComplianceScoreContainer');
        $element.removeClass('fade-out fade-out-hidden');
        $element.show();
    };
})();
