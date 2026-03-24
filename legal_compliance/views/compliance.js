/**
 * Compliance Dashboard JavaScript
 * Handles all client-side interactivity
 */

// Global variables for current selections
var currentRiskFlagId = null;
var currentEmployeeId = null;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Compliance Dashboard loaded');
    initDashboard();
});

/**
 * Initialize dashboard event listeners
 */
function initDashboard() {
    console.log('Initializing dashboard...');
    
    // Attach click listeners to stat cards (info-box.clickable)
    var cards = document.querySelectorAll('.info-box.clickable');
    cards.forEach(function(card) {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            var cardType = this.getAttribute('data-card-type');
            console.log('Card clicked:', cardType);
            showCardDetails(this);
        });
    });
    
    // Attach click listeners to employee rows
    var rows = document.querySelectorAll('.employee-compliance-row');
    rows.forEach(function(row) {
        row.addEventListener('click', function(e) {
            e.preventDefault();
            var employeeId = this.getAttribute('data-employee-id');
            if (employeeId) {
                showEmployeeDetails(parseInt(employeeId));
            }
        });
    });
    
    // Attach click listeners to employee links
    var links = document.querySelectorAll('.employee-link');
    links.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var employeeId = this.getAttribute('data-employee-id');
            if (employeeId) {
                showEmployeeDetails(parseInt(employeeId));
            }
        });
    });
    
    // Attach click listeners to risk flag rows
    var flagRows = document.querySelectorAll('tr[onclick*="showRiskFlagDetails"]');
    flagRows.forEach(function(row) {
        row.addEventListener('click', function(e) {
            e.preventDefault();
            var onclickAttr = this.getAttribute('onclick');
            var match = onclickAttr && onclickAttr.match(/showRiskFlagDetails\((\d+)\)/);
            if (match) {
                showRiskFlagDetails(parseInt(match[1]));
            }
        });
    });
    
    console.log('Dashboard initialized');
}

/**
 * Show Card Details Modal
 */
function showCardDetails(element) {
    var modal = document.getElementById('cardDetailsModal');
    if (!modal) return;
    
    var title = element.getAttribute('data-title') || 'Card Details';
    var subtitle = element.getAttribute('data-subtitle') || '';
    var description = element.getAttribute('data-description') || '';
    var iconClass = element.getAttribute('data-icon-class') || 'bg-primary';
    
    document.getElementById('cardDetailsTitle').textContent = title;
    document.getElementById('cardDetailsSubtitle').textContent = subtitle;
    document.getElementById('cardDetailsDescription').textContent = description;
    
    var iconElement = document.getElementById('cardDetailsIcon');
    if (iconElement) {
        iconElement.className = 'info-box-icon ' + iconClass + ' elevation-1';
    }
    
    jQuery('#cardDetailsModal').modal('show');
}

/**
 * Show Risk Flag Details Modal
 */
function showRiskFlagDetails(riskFlagId) {
    var modal = document.getElementById('riskFlagDetailsModal');
    var content = document.getElementById('riskFlagDetailsContent');
    
    if (!modal || !content) {
        console.error('Modal elements not found');
        return;
    }
    
    // Store current risk flag ID
    window.currentRiskFlagId = riskFlagId;
    
    // Show modal with loading
    jQuery('#riskFlagDetailsModal').modal('show');
    content.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i><p class="mt-2">Loading...</p></div>';
    
    // Fetch data via AJAX
    fetch('compliance.php?action=get_risk_flag_details&flag_id=' + riskFlagId)
        .then(response => response.json())
        .then(data => {
            if (data && data.id) {
                content.innerHTML = buildRiskFlagDetailsHTML(data);
            } else {
                content.innerHTML = '<div class="alert alert-warning">No risk flag details found.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading risk flag details.</div>';
        });
}

/**
 * Build HTML for Risk Flag Details
 */
function buildRiskFlagDetailsHTML(data) {
    var severityClass = getSeverityClass(data.severity);
    var severityLabel = data.severity ? data.severity.toUpperCase() : 'UNKNOWN';
    var createdDate = data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A';
    var resolvedDate = data.resolved_at ? new Date(data.resolved_at).toLocaleDateString() : 'Not resolved';
    
    var statusBadge = data.is_resolved == 1 
        ? '<span class="badge badge-success">Resolved</span>' 
        : '<span class="badge badge-' + severityClass + '">' + severityLabel + '</span>';
    
    return '<div class="row">' +
        '<div class="col-md-12">' +
        '<div class="alert alert-' + severityClass + '">' +
        '<h5><i class="fas fa-exclamation-triangle"></i> ' + (data.title || 'Untitled Risk') + '</h5>' +
        '</div></div></div>' +
        '<div class="row">' +
        '<div class="col-md-6">' +
        '<div class="card info-card"><div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle"></i> Risk Information</h3></div>' +
        '<div class="card-body">' +
        '<p><strong>Risk ID:</strong> #' + data.id + '</p>' +
        '<p><strong>Category:</strong> ' + (data.category || 'N/A') + '</p>' +
        '<p><strong>Severity:</strong> ' + statusBadge + '</p>' +
        '<p><strong>Status:</strong> ' + (data.is_resolved == 1 ? '<span class="text-success">Resolved</span>' : '<span class="text-danger">Active</span>') + '</p>' +
        '<p><strong>Created:</strong> ' + createdDate + '</p>' +
        '<p><strong>Resolved:</strong> ' + resolvedDate + '</p>' +
        '</div></div></div>' +
        '<div class="col-md-6">' +
        '<div class="card info-card"><div class="card-header"><h3 class="card-title"><i class="fas fa-user"></i> Affected Employee</h3></div>' +
        '<div class="card-body">' +
        '<p><strong>Name:</strong> ' + (data.first_name || '') + ' ' + (data.last_name || '') + '</p>' +
        '<p><strong>Employee No:</strong> ' + (data.employee_no || 'N/A') + '</p>' +
        '<p><strong>Position:</strong> ' + (data.position || 'N/A') + '</p>' +
        '<p><strong>Department:</strong> ' + (data.department || 'N/A') + '</p>' +
        '</div></div></div></div>' +
        '<div class="row mt-3"><div class="col-md-12">' +
        '<div class="card info-card"><div class="card-header"><h3 class="card-title"><i class="fas fa-align-left"></i> Description</h3></div>' +
        '<div class="card-body"><p>' + (data.description || 'No description available.') + '</p></div></div></div></div>' +
        '<div class="row mt-3"><div class="col-md-12">' +
        '<div class="card bg-light"><div class="card-header"><h3 class="card-title"><i class="fas fa-lightbulb"></i> Recommended Actions</h3></div>' +
        '<div class="card-body"><ul>' +
        '<li>Review the risk details and assess impact</li>' +
        '<li>Contact the employee to discuss required actions</li>' +
        '<li>Document any actions taken</li>' +
        '<li>Follow up to ensure compliance</li>' +
        '</ul></div></div></div></div>';
}

/**
 * Show Employee Details Modal
 */
function showEmployeeDetails(employeeId) {
    var modal = document.getElementById('employeeDetailsModal');
    var content = document.getElementById('employeeDetailsContent');
    
    if (!modal || !content) {
        console.error('Modal elements not found');
        return;
    }
    
    // Store current employee ID
    window.currentEmployeeId = employeeId;
    
    // Show modal with loading
    jQuery('#employeeDetailsModal').modal('show');
    content.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i><p class="mt-2">Loading employee details...</p></div>';
    
    // Fetch data via AJAX
    fetch('compliance.php?action=get_employee_detailed&id=' + employeeId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Employee data received:', data);
            if (data && data.employee) {
                content.innerHTML = buildEmployeeDetailsHTML(data);
            } else if (data && data.id) {
                // Handle case where response is just the employee object (from get_employee_details action)
                content.innerHTML = buildEmployeeDetailsHTML({employee: data, summary: {}, risk_flags: [], policy_acknowledgments: []});
            } else {
                content.innerHTML = '<div class="alert alert-warning">No employee details found.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading employee details. Please try again.</div>';
        });
}

/**
 * Build HTML for Employee Details
 */
function buildEmployeeDetailsHTML(data) {
    var emp = data.employee;
    var summary = data.summary || {};
    var riskFlags = data.risk_flags || [];
    var policyAcks = data.policy_acknowledgments || [];
    
    var overallScore = Math.round(summary.overall_score || 0);
    var status = summary.status || 'non_compliant';
    
    // Determine score class
    var scoreClass;
    if (overallScore >= 90) {
        scoreClass = 'score-excellent';
    } else if (overallScore >= 70) {
        scoreClass = 'score-warning';
    } else {
        scoreClass = 'score-danger';
    }
    
    // Determine status for display
    var statusClass, statusDisplay;
    if (overallScore >= 90) {
        statusClass = 'status-compliant';
        statusDisplay = 'Compliant';
    } else if (overallScore >= 70) {
        statusClass = 'status-at-risk';
        statusDisplay = 'At Risk';
    } else {
        statusClass = 'status-non-compliant';
        statusDisplay = 'Non-Compliant';
    }
    
    // Build HTML
    var html = '';
    
    // Section 1: Employee Information Card
    html += '<div class="emp-compliance-card">';
    html += '<div class="card-header"><i class="fas fa-user-circle"></i>Employee Information</div>';
    html += '<div class="card-body">';
    html += '<div class="emp-info-grid">';
    html += '<div class="emp-info-item">';
    html += '<div class="label">Full Name</div>';
    html += '<div class="value">' + (emp.first_name || '') + ' ' + (emp.last_name || '') + '</div>';
    html += '</div>';
    html += '<div class="emp-info-item">';
    html += '<div class="label">Employee No</div>';
    html += '<div class="value">' + (emp.employee_id || emp.employee_no || 'N/A') + '</div>';
    html += '</div>';
    html += '<div class="emp-info-item">';
    html += '<div class="label">Position</div>';
    html += '<div class="value">' + (emp.position || 'N/A') + '</div>';
    html += '</div>';
    html += '<div class="emp-info-item">';
    html += '<div class="label">Department</div>';
    html += '<div class="value">' + (emp.department || 'N/A') + '</div>';
    html += '</div>';
    html += '<div class="emp-info-item status-active">';
    html += '<div class="label">Status</div>';
    html += '<div class="value">' + (emp.status || 'Active') + '</div>';
    html += '</div>';
    html += '</div></div></div>';
    
    // Section 2: Compliance Overview
    html += '<div class="emp-compliance-card">';
    html += '<div class="card-header"><i class="fas fa-chart-pie"></i>Compliance Overview</div>';
    html += '<div class="card-body">';
    html += '<div class="compliance-overview">';
    html += '<div class="compliance-score-circle ' + scoreClass + '">';
    html += '<span class="score-value">' + overallScore + '%</span>';
    html += '<span class="score-label">Score</span>';
    html += '</div>';
    html += '<div class="compliance-status-badge ' + statusClass + '">' + statusDisplay + '</div>';
    html += '<div class="compliance-metrics">';
    html += '<div class="compliance-metric critical">';
    html += '<div class="metric-value">' + (summary.critical_issues || 0) + '</div>';
    html += '<div class="metric-label">Critical Issues</div>';
    html += '</div>';
    html += '<div class="compliance-metric high">';
    html += '<div class="metric-value">' + (summary.high_risks || 0) + '</div>';
    html += '<div class="metric-label">High Risks</div>';
    html += '</div>';
    html += '<div class="compliance-metric at-risk">';
    html += '<div class="metric-value">' + (summary.at_risk_count || 0) + '</div>';
    html += '<div class="metric-label">At Risk</div>';
    html += '</div>';
    html += '</div></div></div></div>';
    
    // Section 3: Category Scores
    html += '<div class="emp-compliance-card">';
    html += '<div class="card-header"><i class="fas fa-list-check"></i>Category Scores</div>';
    html += '<div class="card-body">';
    html += '<div class="category-scores-list">';
    
    var categories = [
        { name: 'Employment', score: summary.employment_score },
        { name: 'Leave', score: summary.leave_score },
        { name: 'Benefits', score: summary.benefits_score },
        { name: 'Working Conditions', score: summary.working_conditions_score },
        { name: 'Workplace Protection', score: summary.workplace_protection_score },
        { name: 'Data Privacy', score: summary.data_privacy_score }
    ];
    
    categories.forEach(function(cat) {
        var score = Math.round(cat.score || 0);
        var barClass;
        if (score >= 90) {
            barClass = 'excellent';
        } else if (score >= 70) {
            barClass = 'warning';
        } else {
            barClass = 'danger';
        }
        
        html += '<div class="category-score-item">';
        html += '<div class="category-score-header">';
        html += '<span class="category-score-name">' + cat.name + '</span>';
        html += '<span class="category-score-value">' + score + '%</span>';
        html += '</div>';
        html += '<div class="category-score-bar">';
        html += '<div class="category-score-fill ' + barClass + '" style="width: ' + score + '%;"></div>';
        html += '</div>';
        html += '</div>';
    });
    
    html += '</div></div></div>';
    
    // Section 4: Risk Flags
    if (riskFlags.length > 0) {
        html += '<div class="emp-compliance-card">';
        html += '<div class="card-header"><i class="fas fa-exclamation-triangle"></i>Active Risk Flags</div>';
        html += '<div class="card-body" style="padding: 0;">';
        html += '<table class="risk-flags-table"><thead><tr><th>Severity</th><th>Category</th><th>Title</th></tr></thead><tbody>';
        
        riskFlags.forEach(function(rf) {
            var severity = (rf.severity || 'medium').toLowerCase();
            html += '<tr class="severity-' + severity + '">';
            html += '<td><span class="severity-badge severity-' + severity + '">' + (rf.severity || 'N/A').toUpperCase() + '</span></td>';
            html += '<td>' + (rf.category || 'N/A') + '</td>';
            html += '<td>' + (rf.title || 'N/A') + '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div></div>';
    }
    
    // Section 5: Policy Acknowledgments
    if (policyAcks.length > 0) {
        html += '<div class="emp-compliance-card">';
        html += '<div class="card-header"><i class="fas fa-file-signature"></i>Policy Acknowledgments</div>';
        html += '<div class="card-body" style="padding: 0;">';
        html += '<table class="policy-table"><thead><tr><th>Policy</th><th>Category</th><th>Date Acknowledged</th></tr></thead><tbody>';
        
        policyAcks.forEach(function(ack) {
            var ackDate = ack.date_acknowledged ? new Date(ack.date_acknowledged).toLocaleDateString() : null;
            html += '<tr>';
            html += '<td>' + (ack.title || 'N/A') + '</td>';
            html += '<td>' + (ack.category || 'N/A') + '</td>';
            html += '<td>';
            if (ackDate) {
                html += '<span class="policy-status acknowledged">' + ackDate + '</span>';
            } else {
                html += '<span class="policy-status not-acknowledged">Not acknowledged</span>';
            }
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div></div>';
    }
    
    return html;
}

/**
 * Resolve Risk Flag
 */
function resolveRiskFlag() {
    if (!window.currentRiskFlagId) return;
    
    if (confirm('Are you sure you want to mark this risk as resolved?')) {
        fetch('compliance.php?action=resolve_risk_flag&id=' + window.currentRiskFlagId)
            .then(response => response.json())
            .then(data => {
                alert('Risk flag has been resolved!');
                jQuery('#riskFlagDetailsModal').modal('hide');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error resolving risk flag');
            });
    }
}

/**
 * Escalate Risk Flag
 */
function escalateRiskFlag() {
    if (!window.currentRiskFlagId) return;
    
    var notes = prompt('Enter escalation notes:');
    if (notes) {
        var formData = new FormData();
        formData.append('notes', notes);
        
        fetch('compliance.php?action=escalate_risk_flag&id=' + window.currentRiskFlagId, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert('Risk flag has been escalated!');
            jQuery('#riskFlagDetailsModal').modal('hide');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error escalating risk flag');
        });
    }
}

/**
 * Send Reminder to Employee
 */
function sendReminderToEmployee() {
    var employeeId = window.currentEmployeeId;
    var riskFlagId = window.currentRiskFlagId;
    
    if (!employeeId && !riskFlagId) {
        alert('No employee selected. Please click on an employee or risk flag first.');
        return;
    }
    
    // Default reminder content
    var defaultSubject = 'Compliance Reminder - Action Required';
    var defaultMessage = 'Dear Employee,\n\nThis is a reminder regarding your pending compliance requirements. Please complete the necessary documents and training at your earliest convenience.\n\nThank you,\nHuman Resources Department';
    
    // Store IDs for the modal
    window.reminderEmployeeId = employeeId;
    window.reminderRiskFlagId = riskFlagId;
    
    // If we only have risk flag ID, get employee ID first
    if (!employeeId && riskFlagId) {
        fetch('compliance.php?action=get_risk_flag_details&id=' + riskFlagId)
            .then(response => response.json())
            .then(data => {
                if (data && data.employee_id) {
                    window.reminderEmployeeId = data.employee_id;
                    
                    // Customize message based on risk flag details
                    if (data.title) {
                        defaultSubject = 'Compliance Alert: ' + data.title;
                        defaultMessage = 'Dear Employee,\n\nThis is a reminder regarding the following compliance issue that requires your immediate attention:\n\n' + data.title + '\n\nCategory: ' + (data.category || 'General') + '\nSeverity: ' + (data.severity || 'Medium') + '\n\nPlease take immediate action to resolve this matter.\n\nThank you,\nHuman Resources Department';
                    }
                    
                    jQuery('#reminderSubject').val(defaultSubject);
                    jQuery('#reminderMessage').val(defaultMessage);
                    jQuery('#sendReminderModal').modal('show');
                } else {
                    alert('Could not find employee associated with this risk flag.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading reminder details');
            });
    } else {
        // Set default message for employee
        jQuery('#reminderSubject').val(defaultSubject);
        jQuery('#reminderMessage').val(defaultMessage);
        // Show the modal
        jQuery('#sendReminderModal').modal('show');
    }
}

function submitReminder() {
    var subject = jQuery('#reminderSubject').val();
    var message = jQuery('#reminderMessage').val();
    
    if (!subject || subject.trim() === '') {
        alert('Please enter a subject line.');
        return;
    }
    
    if (!message || message.trim() === '') {
        alert('Please enter a reminder message.');
        return;
    }
    
    var employeeId = window.reminderEmployeeId;
    
    if (!employeeId) {
        alert('No employee selected.');
        return;
    }
    
    // Send the reminder with subject and message
    var formData = new FormData();
    formData.append('subject', subject);
    formData.append('message', message);
    
    jQuery('#sendReminderModal').find('button').prop('disabled', true);
    
    fetch('compliance.php?action=send_reminder&id=' + employeeId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        jQuery('#sendReminderModal').modal('hide');
        jQuery('#reminderSubject').val('');
        jQuery('#reminderMessage').val('');
        jQuery('#sendReminderModal').find('button').prop('disabled', false);
        
        if (data && data.success) {
            alert('Reminder has been sent successfully to the employee!');
        } else {
            alert('Reminder sent successfully!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        jQuery('#sendReminderModal').find('button').prop('disabled', false);
        alert('Failed to send reminder. Please try again later.');
    });
}

/**
 * Helper: Get severity CSS class
 */
function getSeverityClass(severity) {
    if (severity === 'critical') return 'danger';
    if (severity === 'high') return 'warning';
    if (severity === 'medium') return 'info';
    if (severity === 'low') return 'secondary';
    return 'primary';
}

/**
 * Helper: Capitalize first letter
 */
function ucFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
