/**
 * Leave Management JavaScript
 * 
 * Handles all client-side functionality including:
 * - Dynamic checklist generation
 * - Leave request filtering
 * - Status updates (approve/reject)
 * - Card view filtering
 * - AJAX interactions
 */

// Global variables
let currentStatusFilter = 'pending';
let currentLeaveId = null;
let cardViewFilter = null;

// Initialize on document ready
document.addEventListener('DOMContentLoaded', function() {
    // Set active nav item
    setActiveNavItem();
    
    // Initialize modal event listeners
    initializeModalListeners();
    
    // Initialize any event listeners
    initializeEventListeners();
    
    // Initialize card filtering
    initializeCardFiltering();
    
    // Add Bootstrap modal event handlers
    if (typeof $ !== 'undefined' && $.fn.modal) {
        // jQuery Bootstrap modal events
        $('#leaveDetailsModal').on('hidden.bs.modal', function() {
            resetModalState();
        });
        $('#uploadDocumentModal').on('hidden.bs.modal', function() {
            const form = document.getElementById('uploadDocumentForm');
            if (form) form.reset();
        });
    } else {
        // Native Bootstrap 5 event listeners
        const leaveModal = document.getElementById('leaveDetailsModal');
        const uploadModal = document.getElementById('uploadDocumentModal');
        
        if (leaveModal) {
            leaveModal.addEventListener('hidden.bs.modal', function() {
                resetModalState();
            });
        }
        
        if (uploadModal) {
            uploadModal.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('uploadDocumentForm');
                if (form) form.reset();
            });
        }
    }
});

/**
 * Set the active navigation item
 */
function setActiveNavItem() {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.href && link.href.includes('leave_management.php')) {
            link.classList.add('active');
        }
    });
}

/**
 * Initialize event listeners
 */
function initializeEventListeners() {
    // Add event delegation for dynamically created elements
    document.addEventListener('click', handleGlobalClick);
}

/**
 * Handle global click events
 */
function handleGlobalClick(event) {
    // View leave details
    if (event.target.closest('[data-action="view-leave"]')) {
        const button = event.target.closest('[data-action="view-leave"]');
        const leaveId = button.dataset.leaveId;
        const leaveType = button.dataset.leaveType;
        viewLeaveDetails(leaveId, leaveType);
    }
}

/**
 * Filter leave requests by status
 */
function filterStatus(status) {
    window.location.href = 'leave_management.php?status=' + status;
}

/**
 * View leave details and generate dynamic checklist
 */
function viewLeaveDetails(leaveId, leaveType) {
    currentLeaveId = leaveId;
    
    // Fetch leave details via AJAX
    fetch('leave_management.php?action=get_leave_details&id=' + leaveId)
        .then(response => response.json())
        .then(data => {
            if (data) {
                // Populate modal with leave details
                populateLeaveModal(data);
                
                // Determine if we should show as display-only (for approved/rejected)
                const isDisplayOnly = data.status !== 'pending';
                
                // Check if we should skip the checklist for maternity leave
                // (female employee with 6+ months service)
                if (leaveType === 'Maternity Leave' && data.employee_id && !isDisplayOnly) {
                    fetchEmployeeInfo(data.employee_id)
                        .then(empInfo => {
                            // If female and has minimum service, skip checklist
                            if (empInfo && empInfo.is_female && empInfo.has_minimum_service) {
                                showSimplifiedModal(data, leaveType);
                            } else {
                                // Show full checklist
                                generateChecklist(leaveType, data, isDisplayOnly);
                            }
                            
                            // Load documents for this leave
                            loadDocuments(leaveId);
                            
                            // Show modal
                            showLeaveModal();
                        });
                } else {
                    // Show full checklist (displayOnly if approved/rejected)
                    generateChecklist(leaveType, data, isDisplayOnly);
                    
                    // Load documents for this leave
                    loadDocuments(leaveId);
                    
                    // Show modal
                    showLeaveModal();
                }
            }
        })
        .catch(error => {
            console.error('Error fetching leave details:', error);
            alert('Error loading leave details');
        });
}

/**
 * Show simplified modal when checklist is not required
 */
function showSimplifiedModal(data, leaveType) {
    const checklistCard = document.getElementById('checklistCard');
    const eligibilityStatus = document.getElementById('eligibilityStatus');
    const eligibilityMessage = document.getElementById('eligibilityMessage');
    
    if (checklistCard) {
        checklistCard.style.display = 'none';
    }
    
    if (eligibilityStatus) {
        eligibilityStatus.className = 'alert alert-success mb-3';
        eligibilityStatus.innerHTML = '<i class="fas fa-check-circle mr-2"></i><strong>Auto-Approved</strong> - Employee meets all eligibility requirements (Female with 6+ months service)';
    }
}

/**
 * Reset modal to default state (show checklist)
 */
function resetModalState() {
    const checklistCard = document.getElementById('checklistCard');
    const eligibilityStatus = document.getElementById('eligibilityStatus');
    
    if (checklistCard) {
        checklistCard.style.display = 'block';
    }
    
    if (eligibilityStatus) {
        eligibilityStatus.className = 'alert alert-info mb-3';
        eligibilityStatus.innerHTML = '<i class="fas fa-info-circle mr-2"></i>Review the checklist below to determine eligibility';
    }
}

/**
 * Show leave modal
 */
function showLeaveModal() {
    const modal = document.getElementById('leaveDetailsModal');
    const modalDialog = modal ? modal.querySelector('.modal-dialog') : null;
    const approveBtn = document.getElementById('approveBtn');
    
    if (modal) {
        // Create backdrop if not exists
        let backdrop = document.getElementById('leaveDetailsBackdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'leaveDetailsBackdrop';
            modal.parentNode.insertBefore(backdrop, modal.nextSibling);
            
            // Add click handler to backdrop to close modal
            backdrop.addEventListener('click', function() {
                hideLeaveModal();
            });
        }
        
        modal.classList.add('show');
        modal.style.display = 'block';
        modal.setAttribute('aria-modal', 'true');
        modal.removeAttribute('aria-hidden');
        document.body.classList.add('modal-open');
        
        // Set fixed size for modal dialog
        if (modalDialog) {
            modalDialog.style.width = '700px';
            modalDialog.style.maxWidth = '700px';
            modalDialog.style.minWidth = '500px';
            modalDialog.style.height = '80vh';
            modalDialog.style.maxHeight = '80vh';
            modalDialog.style.margin = '10vh auto';
        }
        
        // Add Escape key handler
        document.addEventListener('keydown', handleEscapeKey);
    }
    
    // Hide Approve button by default when modal opens
    // It will be shown only when all requirements are checked
    if (approveBtn) {
        approveBtn.style.display = 'none';
    }
    
    // Trigger initial eligibility check
    updateEligibilityStatus();
}

/**
 * Handle Escape key to close modal
 */
function handleEscapeKey(e) {
    if (e.key === 'Escape') {
        hideLeaveModal();
        document.removeEventListener('keydown', handleEscapeKey);
    }
}

/**
 * Load documents for a leave request
 */
function loadDocuments(leaveId) {
    const documentsList = document.getElementById('documentsList');
    if (!documentsList) return;
    
    fetch('leave_management.php?action=get_documents&leave_id=' + leaveId)
        .then(response => response.json())
        .then(documents => {
            if (documents && documents.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                html += '<thead><tr><th>Type</th><th>File Name</th><th>Uploaded</th><th>Actions</th></tr></thead>';
                html += '<tbody>';
                
                documents.forEach(doc => {
                    const uploadDate = new Date(doc.uploaded_at).toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    const fileIcon = getFileIcon(doc.file_name || doc.file_path);
                    
                    html += '<tr>';
                    html += '<td><i class="' + fileIcon + ' mr-1"></i> ' + doc.document_type + '</td>';
                    html += '<td>' + (doc.file_name || doc.file_path.split('/').pop()) + '</td>';
                    html += '<td>' + uploadDate + '</td>';
                    html += '<td>';
                    html += '<a href="' + doc.file_path + '" target="_blank" class="btn btn-xs btn-info mr-1">';
                    html += '<i class="fas fa-eye"></i> View</a>';
                    html += '<a href="' + doc.file_path + '" download class="btn btn-xs btn-success">';
                    html += '<i class="fas fa-download"></i></a>';
                    html += '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                documentsList.innerHTML = html;
            } else {
                const emptyMsg = '<p class="text-muted text-center">';
                const icon = '<i class="fas fa-folder-open fa-2x mb-2 d-block"></i>';
                const text = 'No documents submitted yet';
                const subtext = '<br><small>Click "Upload Document" to add supporting documents</small></p>';
                documentsList.innerHTML = emptyMsg + icon + text + subtext;
            }
        })
        .catch(error => {
            console.error('Error loading documents:', error);
            documentsList.innerHTML = '<p class="text-danger text-center">Error loading documents</p>';
        });
}

/**
 * Get file icon based on extension
 */
function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    
    if (ext === 'pdf') return 'fas fa-file-pdf text-danger';
    if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'fas fa-file-image text-info';
    if (['doc', 'docx'].includes(ext)) return 'fas fa-file-word text-primary';
    if (['xls', 'xlsx'].includes(ext)) return 'fas fa-file-excel text-success';
    
    return 'fas fa-file text-secondary';
}

/**
 * Show upload document modal
 */
function showUploadModal() {
    const modal = document.getElementById('uploadDocumentModal');
    const uploadLeaveId = document.getElementById('uploadLeaveId');
    
    if (modal && uploadLeaveId) {
        uploadLeaveId.value = currentLeaveId;
        modal.classList.add('show');
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }
}

/**
 * Upload document
 */
function uploadDocument() {
    const leaveId = document.getElementById('uploadLeaveId').value;
    const documentType = document.getElementById('documentType').value;
    const fileInput = document.getElementById('documentFile');
    
    if (!leaveId) {
        alert('No leave request selected');
        return;
    }
    
    if (!documentType) {
        alert('Please select a document type');
        return;
    }
    
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Please select a file');
        return;
    }
    
    const formData = new FormData();
    formData.append('leave_id', leaveId);
    formData.append('document_type', documentType);
    formData.append('document', fileInput.files[0]);
    
    fetch('leave_management.php?action=upload_document', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Close modal
            const modal = document.getElementById('uploadDocumentModal');
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
            
            // Remove modal backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
            
            // Reset form
            document.getElementById('uploadDocumentForm').reset();
            
            // Reload documents
            loadDocuments(leaveId);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error uploading document:', error);
        alert('Error uploading document');
    });
}

/**
 * Populate the leave details modal with data
 */
function populateLeaveModal(data) {
    // Get modal elements
    const employeeName = document.getElementById('modalEmployeeName');
    const leaveType = document.getElementById('modalLeaveType');
    const duration = document.getElementById('modalDuration');
    const startDate = document.getElementById('modalStartDate');
    const endDate = document.getElementById('modalEndDate');
    const reason = document.getElementById('modalReason');
    const decisionCard = document.getElementById('decisionCard');
    const eligibilityMessage = document.getElementById('eligibilityMessage');
    const hrComments = document.getElementById('hrComments');
    
    if (employeeName) {
        employeeName.textContent = (data.first_name || '') + ' ' + (data.last_name || '');
    }
    
    if (leaveType) {
        leaveType.textContent = data.leave_type || '-';
    }
    
    if (duration) {
        duration.textContent = (data.total_days || '0') + ' day(s)';
    }
    
    if (startDate) {
        startDate.textContent = formatDate(data.start_date);
    }
    
    if (endDate) {
        endDate.textContent = formatDate(data.end_date);
    }
    
    if (reason) {
        reason.textContent = data.reason || 'No reason provided';
    }
    
    // Hide/show decision buttons based on status
    if (decisionCard) {
        if (data.status !== 'pending') {
            decisionCard.style.display = 'none';
            if (eligibilityMessage) {
                eligibilityMessage.innerHTML = '<strong>Status:</strong> This request has been ' + data.status.toUpperCase();
                if (data.hr_comments) {
                    eligibilityMessage.innerHTML += '<br><strong>HR Comments:</strong> ' + data.hr_comments;
                }
            }
            
            // Apply checklist data if available (for approved/rejected requests)
            if (data.checklist_data) {
                setTimeout(() => {
                    applyChecklistData(data.checklist_data);
                }, 100);
            }
        } else {
            decisionCard.style.display = 'block';
            if (hrComments) {
                hrComments.value = '';
            }
            if (eligibilityMessage) {
                eligibilityMessage.innerHTML = '<i class="fas fa-info-circle mr-2"></i>Review the checklist below to determine eligibility';
            }
        }
    }
}

/**
 * Format date string to display format
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

/**
 * Generate dynamic checklist based on leave type
 * @param {string} leaveType - The type of leave
 * @param {Object} leaveData - The leave request data
 * @param {boolean} displayOnly - If true, show as read-only list
 */
function generateChecklist(leaveType, leaveData, displayOnly = false) {
    const checklistContainer = document.getElementById('dynamicChecklist');
    const checklistTitle = document.getElementById('checklistTitle');
    
    if (!checklistContainer) return;
    
    // Get checklist configuration from controller via AJAX
    fetch('leave_management.php?action=get_eligibility_checklist&leave_type=' + encodeURIComponent(leaveType))
        .then(response => response.json())
        .then(checklistConfig => {
            if (!checklistConfig) {
                checklistContainer.innerHTML = '<p class="text-muted">No checklist available for this leave type</p>';
                return;
            }
            
            // Fetch employee gender/marital status info (only if not displayOnly)
            return fetchEmployeeInfo(leaveData.employee_id)
                .then(empInfo => {
                    // Modify checklist based on employee info (only if not displayOnly)
                    if (!displayOnly && leaveType === 'Paternity Leave') {
                        // Auto-check gender requirement
                        if (empInfo && empInfo.is_male) {
                            checklistConfig.requirements[0].checked = true;
                        }
                        // Auto-check marriage requirement
                        if (empInfo && empInfo.is_married) {
                            checklistConfig.requirements[1].checked = true;
                        }
                    }
                    
                    let html = generateChecklistHTML(checklistConfig, displayOnly);
                    checklistContainer.innerHTML = html;
                    
                    if (checklistTitle) {
                        checklistTitle.textContent = checklistConfig.title;
                    }
                    
                    // Add event listeners to checkboxes only if not displayOnly
                    if (!displayOnly) {
                        setTimeout(initializeChecklistListeners, 100);
                    }
                });
        })
        .catch(error => {
            console.error('Error loading checklist:', error);
            checklistContainer.innerHTML = '<p class="text-danger">Error loading eligibility checklist</p>';
        });
}

/**
 * Fetch employee gender and marital status info
 */
function fetchEmployeeInfo(employeeId) {
    if (!employeeId) return Promise.resolve(null);
    
    return Promise.all([
        fetch('leave_management.php?action=check_female&employee_id=' + employeeId).then(r => r.json()),
        fetch('leave_management.php?action=check_male&employee_id=' + employeeId).then(r => r.json()),
        fetch('leave_management.php?action=check_married&employee_id=' + employeeId).then(r => r.json()),
        fetch('leave_management.php?action=check_service_duration&employee_id=' + employeeId + '&months=6').then(r => r.json())
    ]).then(([femaleData, maleData, marriedData, serviceData]) => {
        return {
            is_female: femaleData.is_female || false,
            is_male: maleData.is_male || false,
            is_married: marriedData.is_married || false,
            has_minimum_service: serviceData.has_minimum_service || false
        };
    }).catch(error => {
        console.error('Error fetching employee info:', error);
        return null;
    });
}

/**
 * Generate HTML for the checklist
 * @param {Object} checklistConfig - The checklist configuration
 * @param {boolean} displayOnly - If true, show as read-only list with checkmarks instead of checkboxes
 */
function generateChecklistHTML(checklistConfig, displayOnly = false) {
    let html = '';
    
    // Legal Reference
    html += `
        <div class="alert alert-light border-left-${checklistConfig.color} mb-3">
            <i class="fas fa-balance-scale mr-2"></i>
            <strong>Legal Reference:</strong> ${checklistConfig.legalRef}
        </div>
    `;
    
    // Employee Requirements Section
    html += `
        <div class="mb-3">
            <h6 class="text-uppercase text-muted mb-2">
                <i class="fas fa-user-check mr-1"></i> Employee Requirements
            </h6>
            <div class="list-group" id="requirementsList">
    `;
    
    checklistConfig.requirements.forEach((req, index) => {
        const isRequired = req.required ? '<span class="text-danger">*</span>' : '';
        
        if (displayOnly) {
            // Display as read-only list with checkmark/x icon
            html += `
                <div class="list-group-item">
                    <div class="d-flex align-items-center">
                        <span class="requirement-status mr-2" id="status_${req.id}"></span>
                        <span class="${req.required ? 'text-dark' : 'text-muted'}">
                            ${req.label} ${isRequired}
                        </span>
                    </div>
                </div>
            `;
        } else {
            html += `
                <div class="list-group-item">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input requirement-checkbox" id="${req.id}" data-required="${req.required}">
                        <label class="custom-control-label" for="${req.id}">
                            ${req.label} ${isRequired}
                        </label>
                    </div>
                </div>
            `;
        }
    });
    
    html += '</div></div>';
    
    // HR Verification Section
    html += `
        <div class="mb-3">
            <h6 class="text-uppercase text-muted mb-2">
                <i class="fas fa-clipboard-check mr-1"></i> HR Verification Checks
            </h6>
            <div class="list-group" id="hrChecksList">
    `;
    
    checklistConfig.hrChecks.forEach((check, index) => {
        const isRequired = check.required ? '<span class="text-danger">*</span>' : '';
        
        if (displayOnly) {
            // Display as read-only list with checkmark/x icon
            html += `
                <div class="list-group-item">
                    <div class="d-flex align-items-center">
                        <span class="hr-status mr-2" id="status_${check.id}"></span>
                        <span class="${check.required ? 'text-dark' : 'text-muted'}">
                            ${check.label} ${isRequired}
                        </span>
                    </div>
                </div>
            `;
        } else {
            html += `
                <div class="list-group-item">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input hr-verify-checkbox" id="${check.id}" data-required="${check.required}">
                        <label class="custom-control-label" for="${check.id}">
                            ${check.label} ${isRequired}
                        </label>
                    </div>
                </div>
            `;
        }
    });
    
    html += '</div></div>';
    
    return html;
}

/**
 * Initialize event listeners for checklist checkboxes
 */
function initializeChecklistListeners() {
    const checkboxes = document.querySelectorAll('#requirementsList input[type="checkbox"], #hrChecksList input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateEligibilityStatus);
    });
    
    // Initial check
    updateEligibilityStatus();
}

/**
 * Update eligibility status based on checked requirements
 */
function updateEligibilityStatus() {
    const eligibleDiv = document.getElementById('eligibilityStatus');
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');
    
    if (!eligibleDiv) return;
    
    // Count checked items in requirements
    const requirementsCheckboxes = document.querySelectorAll('#requirementsList input[type="checkbox"]');
    const checkedCount = Array.from(requirementsCheckboxes).filter(cb => cb.checked).length;
    const totalCount = requirementsCheckboxes.length;
    
    // Count checked items in HR verification
    const hrCheckboxes = document.querySelectorAll('#hrChecksList input[type="checkbox"]');
    const hrCheckedCount = Array.from(hrCheckboxes).filter(cb => cb.checked).length;
    const hrTotalCount = hrCheckboxes.length;
    
    // Calculate totals
    const allCheckedCount = checkedCount + hrCheckedCount;
    const allTotalCount = totalCount + hrTotalCount;
    const allRequirementsMet = checkedCount === totalCount && hrCheckedCount === hrTotalCount;
    
    // Remove all alert classes
    eligibleDiv.classList.remove('alert-info', 'alert-success', 'alert-danger', 'alert-warning');
    
    if (allRequirementsMet && allTotalCount > 0) {
        eligibleDiv.classList.add('alert-success');
        eligibleDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i><strong>Eligible</strong> - All requirements verified';
    } else if (allCheckedCount < allTotalCount * 0.5 && allTotalCount > 0) {
        eligibleDiv.classList.add('alert-danger');
        eligibleDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i><strong>Not Eligible</strong> - Missing requirements';
    } else {
        eligibleDiv.classList.add('alert-warning');
        eligibleDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i><strong>Partial Eligibility</strong> - Some requirements need verification (' + allCheckedCount + '/' + allTotalCount + ' complete)';
    }
    
    // Show/hide Approve button based on all checks
    if (approveBtn) {
        if (allRequirementsMet) {
            approveBtn.style.display = 'inline-block';
        } else {
            approveBtn.style.display = 'none';
        }
    }
    
    // Reject button always visible
    if (rejectBtn) {
        rejectBtn.style.display = 'inline-block';
    }
}

/**
 * Collect checklist data from the modal
 */
function collectChecklistData() {
    const checklist = {
        requirements: {},
        hrChecks: {}
    };
    
    // Collect requirement checkboxes
    const requirementsCheckboxes = document.querySelectorAll('#requirementsList input[type="checkbox"]');
    requirementsCheckboxes.forEach(checkbox => {
        checklist.requirements[checkbox.id] = checkbox.checked;
    });
    
    // Collect HR verification checkboxes
    const hrCheckboxes = document.querySelectorAll('#hrChecksList input[type="checkbox"]');
    hrCheckboxes.forEach(checkbox => {
        checklist.hrChecks[checkbox.id] = checkbox.checked;
    });
    
    return JSON.stringify(checklist);
}

/**
 * Apply checklist data to the modal checkboxes
 */
function applyChecklistData(checklistData) {
    if (!checklistData) return;
    
    try {
        const checklist = JSON.parse(checklistData);
        
        // Apply requirement checkboxes or status
        if (checklist.requirements) {
            Object.keys(checklist.requirements).forEach(id => {
                const isChecked = checklist.requirements[id];
                
                // Try checkbox first (for pending status)
                const checkbox = document.getElementById(id);
                if (checkbox) {
                    checkbox.checked = isChecked;
                }
                
                // Try status element (for approved/rejected display-only)
                const statusEl = document.getElementById('status_' + id);
                if (statusEl) {
                    if (isChecked) {
                        statusEl.className = 'requirement-status checked';
                    } else {
                        statusEl.className = 'requirement-status unchecked';
                    }
                }
            });
        }
        
        // Apply HR verification checkboxes or status
        if (checklist.hrChecks) {
            Object.keys(checklist.hrChecks).forEach(id => {
                const isChecked = checklist.hrChecks[id];
                
                // Try checkbox first (for pending status)
                const checkbox = document.getElementById(id);
                if (checkbox) {
                    checkbox.checked = isChecked;
                }
                
                // Try status element (for approved/rejected display-only)
                const statusEl = document.getElementById('status_' + id);
                if (statusEl) {
                    if (isChecked) {
                        statusEl.className = 'hr-status checked';
                    } else {
                        statusEl.className = 'hr-status unchecked';
                    }
                }
            });
        }
        
        // Update eligibility status display
        updateEligibilityStatus();
    } catch (e) {
        console.error('Error parsing checklist data:', e);
    }
}

/**
 * Update leave request status (approve/reject)
 */
function updateLeaveStatus(status) {
    if (!currentLeaveId) {
        alert('No leave request selected');
        return;
    }
    
    const hrComments = document.getElementById('hrComments');
    const comments = hrComments ? hrComments.value : '';
    
    // Show confirmation
    const actionText = status === 'approved' ? 'approve' : 'reject';
    if (!confirm('Are you sure you want to ' + actionText + ' this leave request?')) {
        return;
    }
    
    // Collect checklist data
    const checklistData = collectChecklistData();
    
    // Create form data
    const formData = new FormData();
    formData.append('leave_id', currentLeaveId);
    formData.append('status', status);
    formData.append('comments', comments);
    formData.append('checklist_data', checklistData);
    
    // Send AJAX request
    fetch('leave_management.php?action=update_status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            hideLeaveModal();
            // Reload page to show updated status
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
        alert('Error updating leave status');
    });
}

/**
 * Hide the leave details modal
 */
function hideLeaveModal() {
    const modal = document.getElementById('leaveDetailsModal');
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
        
        // Remove modal backdrop
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
}

/**
 * Properly close a modal using Bootstrap's standard behavior
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    // Use Bootstrap's built-in modal hide method
    // This handles all the cleanup including backdrops, aria, body class, etc.
    if (typeof $ !== 'undefined' && $.fn.modal) {
        // jQuery Bootstrap
        $(modal).modal('hide');
    } else if (modal.Modal && modal.Modal._isShown) {
        // Native Bootstrap 5
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) {
            bsModal.hide();
        } else {
            // Create new instance and hide
            const bsModalNew = new bootstrap.Modal(modal);
            bsModalNew.hide();
        }
    } else {
        // Fallback: Manual cleanup
        manualModalClose(modalId);
    }
}

/**
 * Manual modal close fallback
 */
function manualModalClose(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    // Remove show class
    modal.classList.remove('show');
    modal.style.display = 'none';
    
    // Remove aria attributes
    modal.removeAttribute('aria-modal');
    modal.removeAttribute('aria-hidden');
    modal.removeAttribute('inert');
    
    // Remove body classes
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    
    // Remove all modal backdrops
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => {
        backdrop.parentNode.removeChild(backdrop);
    });
    
    // Reset modal state
    if (modalId === 'leaveDetailsModal') {
        resetModalState();
    }
}

/**
 * Hide the leave details modal
 */
function hideLeaveModal() {
    const modal = document.getElementById('leaveDetailsModal');
    const backdrop = document.getElementById('leaveDetailsBackdrop');
    
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
        
        // Remove modal backdrop
        if (backdrop) {
            backdrop.remove();
        }
        
        // Reset modal state
        resetModalState();
    }
}

/**
 * Hide the upload document modal
 */
function hideUploadModal() {
    closeModal('uploadDocumentModal');
    // Reset form
    const form = document.getElementById('uploadDocumentForm');
    if (form) {
        form.reset();
    }
}

/**
 * Initialize modal event listeners
 */
function initializeModalListeners() {
    // Leave Details Modal - Close button (X)
    const leaveCloseBtn = document.getElementById('leaveDetailsCloseBtn');
    if (leaveCloseBtn) {
        leaveCloseBtn.addEventListener('click', function() {
            hideLeaveModal();
        });
    }
    
    // Leave Details Modal - Footer Close button
    const leaveFooterCloseBtn = document.getElementById('leaveDetailsFooterCloseBtn');
    if (leaveFooterCloseBtn) {
        leaveFooterCloseBtn.addEventListener('click', function() {
            hideLeaveModal();
        });
    }
    
    // Escape key for leave details modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const leaveModal = document.getElementById('leaveDetailsModal');
            if (leaveModal && leaveModal.classList.contains('show')) {
                hideLeaveModal();
            }
        }
    });
    
    // Click outside modal to close
    const leaveModal = document.getElementById('leaveDetailsModal');
    if (leaveModal) {
        leaveModal.addEventListener('click', function(e) {
            if (e.target === leaveModal) {
                hideLeaveModal();
            }
        });
    }
}

// Expose functions to global scope for inline event handlers
window.filterStatus = filterStatus;
window.viewLeaveDetails = viewLeaveDetails;
window.updateLeaveStatus = updateLeaveStatus;
window.loadDocuments = loadDocuments;
window.closeModal = closeModal;
window.hideLeaveModal = hideLeaveModal;
