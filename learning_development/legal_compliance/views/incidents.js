/**
 * Incident Reporting Module JavaScript
 * Handles all client-side interactivity for incident management
 * Follows the same patterns as compliance.js for modal handling
 */

// ================================================
// URGENT CLEANUP - Run BEFORE DOMContentLoaded
// Remove stuck modal backdrops immediately
// ================================================
(function() {
    var backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
})();

document.addEventListener('DOMContentLoaded', function() {
    console.log('Incident Reporting Module loaded');
    
    // Initialize all event handlers
    initializeIncidentForm();
    initializeIncidentModals();
    initializeDataTable();
    initializeFileUpload();
    initializeExportButtons();
    initializeSearchFilters();
    
    // ================================================
    // PAGE LOAD CLEANUP - Remove any leftover backdrops
    // ================================================
    
    jQuery('.modal-backdrop').remove();
    jQuery('body').removeClass('modal-open');
    jQuery('body').css({'overflow': '', 'padding-right': ''});
    
    // Handle incident type change to show/hide "Others" field
    handleIncidentTypeChange();
    
    // Handle anonymous toggle
    handleAnonymousToggle();
});

/**
 * Initialize the incident reporting form
 */
function initializeIncidentForm() {
    const form = document.getElementById('incidentReportForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitIncidentReport();
        });
    }
    
    // Employee search functionality
    const employeeSearch = document.getElementById('employeeSearch');
    if (employeeSearch) {
        employeeSearch.addEventListener('input', debounce(function() {
            searchEmployees(this.value);
        }, 300));
    }
    
    // Employee selection
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('employee-select-item')) {
            selectEmployee(
                e.target.dataset.id,
                e.target.dataset.name,
                e.target.dataset.department
            );
        }
    });
}

/**
 * Initialize modal handling with proper z-index stacking
 */
function initializeIncidentModals() {
    // Handle report incident modal
    jQuery(document).on('show.bs.modal', '#reportIncidentModal', function() {
        const modalId = jQuery(this).attr('id');
        let highestZIndex = 1050;
        
        jQuery('.modal:visible').each(function() {
            const style = jQuery(this).attr('style') || '';
            const match = style.match(/z-index:\s*(\d+)/);
            if (match) {
                const z = parseInt(match[1]);
                if (z > highestZIndex) highestZIndex = z;
            }
        });
        
        const newZIndex = highestZIndex + 20;
        jQuery(this).css('z-index', newZIndex);
        jQuery(this).find('.modal-dialog').css('z-index', newZIndex + 10);
        
        setTimeout(function() {
            jQuery('.modal-backdrop').css('z-index', newZIndex - 1);
        }, 0);
    });
    
    // View Incident Details Modal
    jQuery(document).on('show.bs.modal', '#viewIncidentModal', function() {
        const modalId = jQuery(this).attr('id');
        let highestZIndex = 1050;
        
        jQuery('.modal:visible').each(function() {
            const style = jQuery(this).attr('style') || '';
            const match = style.match(/z-index:\s*(\d+)/);
            if (match) {
                const z = parseInt(match[1]);
                if (z > highestZIndex) highestZIndex = z;
            }
        });
        
        const newZIndex = highestZIndex + 20;
        jQuery(this).css('z-index', newZIndex);
        jQuery(this).find('.modal-dialog').css('z-index', newZIndex + 10);
    });
    
    // Update Incident Modal
    jQuery(document).on('show.bs.modal', '#updateIncidentModal', function() {
        const modalId = jQuery(this).attr('id');
        let highestZIndex = 1050;
        
        jQuery('.modal:visible').each(function() {
            const style = jQuery(this).attr('style') || '';
            const match = style.match(/z-index:\s*(\d+)/);
            if (match) {
                const z = parseInt(match[1]);
                if (z > highestZIndex) highestZIndex = z;
            }
        });
        
        const newZIndex = highestZIndex + 20;
        jQuery(this).css('z-index', newZIndex);
        jQuery(this).find('.modal-dialog').css('z-index', newZIndex + 10);
    });
    
    // Cleanup backdrops when any modal is closed - AGGRESSIVE CLEANUP
    jQuery(document).on('hidden.bs.modal', '.modal', function() {
        // Force complete cleanup of ALL modal artifacts unconditionally
        setTimeout(function() {
            // Remove ALL modal backdrops completely
            jQuery('.modal-backdrop').each(function() {
                jQuery(this).remove();
            });
            
            // Remove modal-open class from body
            jQuery('body').removeClass('modal-open');
            
            // Reset ALL overflow and padding styles
            jQuery('body').css('overflow', '');
            jQuery('body').css('padding-right', '');
            
            console.log('Incident Modal cleanup complete - all backdrops removed');
        }, 200);
    });
}

/**
 * Initialize DataTable for incidents list
 */
function initializeDataTable() {
    if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
        if (jQuery('#incidentsTable').length) {
            jQuery('#incidentsTable').DataTable({
                responsive: true,
                ordering: true,
                searching: true,
                paging: true,
                info: true,
                order: [[0, 'desc']], // Sort by Incident ID descending
                columnDefs: [
                    { orderable: false, targets: -1 } // Disable sorting on Action column
                ]
            });
        }
    }
}

/**
 * Initialize file upload functionality
 */
function initializeFileUpload() {
    const fileInput = document.getElementById('incidentAttachments');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            handleFileSelect(e);
        });
    }
    
    // Drag and drop functionality
    const dropZone = document.getElementById('fileDropZone');
    if (dropZone) {
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            handleFileDrop(e);
        });
    }
}

/**
 * Handle file selection
 */
function handleFileSelect(e) {
    const files = e.target.files;
    const previewContainer = document.getElementById('filePreview');
    if (!previewContainer) return;
    
    previewContainer.innerHTML = '';
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (validateFile(file)) {
            createFilePreview(file, previewContainer);
        }
    }
}

/**
 * Handle file drop
 */
function handleFileDrop(e) {
    const files = e.dataTransfer.files;
    const previewContainer = document.getElementById('filePreview');
    if (!previewContainer) return;
    
    previewContainer.innerHTML = '';
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (validateFile(file)) {
            createFilePreview(file, previewContainer);
        }
    }
}

/**
 * Validate file type and size
 */
function validateFile(file) {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'video/mp4', 'video/webm'];
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    if (!allowedTypes.includes(file.type)) {
        alert('File type not allowed: ' + file.name);
        return false;
    }
    
    if (file.size > maxSize) {
        alert('File too large: ' + file.name + ' (max 10MB)');
        return false;
    }
    
    return true;
}

/**
 * Create file preview element
 */
function createFilePreview(file, container) {
    const div = document.createElement('div');
    div.className = 'file-preview-item';
    div.innerHTML = `
        <i class="fas fa-file-${getFileIcon(file.type)}"></i>
        <span class="file-name">${file.name}</span>
        <span class="file-size">${formatFileSize(file.size)}</span>
        <button type="button" class="btn-remove" onclick="removeFile(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}

/**
 * Get file icon based on type
 */
function getFileIcon(type) {
    if (type.startsWith('image/')) return 'image';
    if (type.startsWith('video/')) return 'video';
    return 'alt';
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Remove file from preview
 */
function removeFile(button) {
    button.parentElement.remove();
}

/**
 * Initialize export buttons
 */
function initializeExportButtons() {
    const exportPdf = document.getElementById('exportPdf');
    if (exportPdf) {
        exportPdf.addEventListener('click', function() {
            exportIncidents('pdf');
        });
    }
    
    const exportCsv = document.getElementById('exportCsv');
    if (exportCsv) {
        exportCsv.addEventListener('click', function() {
            exportIncidents('csv');
        });
    }
}

/**
 * Export incidents to PDF or CSV
 */
function exportIncidents(format) {
    const formData = new FormData();
    formData.append('action', 'export');
    formData.append('format', format);
    
    // Add current filter values
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter && statusFilter.value) {
        formData.append('status', statusFilter.value);
    }
    
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter && categoryFilter.value) {
        formData.append('category', categoryFilter.value);
    }
    
    fetch('incidents.php?action=export', {
        method: 'POST',
        body: formData
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `incidents_${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Failed to export incidents. Please try again.');
    });
}

/**
 * Initialize search and filter functionality
 */
function initializeSearchFilters() {
    const searchInput = document.getElementById('incidentSearch');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            filterIncidents();
        }, 300));
    }
    
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterIncidents();
        });
    }
    
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            filterIncidents();
        });
    }
    
    const severityFilter = document.getElementById('severityFilter');
    if (severityFilter) {
        severityFilter.addEventListener('change', function() {
            filterIncidents();
        });
    }
}

/**
 * Filter incidents based on search and filter values
 */
function filterIncidents() {
    const searchValue = document.getElementById('incidentSearch')?.value?.toLowerCase() || '';
    const statusValue = document.getElementById('statusFilter')?.value || '';
    const categoryValue = document.getElementById('categoryFilter')?.value || '';
    const severityValue = document.getElementById('severityFilter')?.value || '';
    
    const rows = document.querySelectorAll('#incidentsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const rowStatus = row.dataset.status || '';
        const rowCategory = row.dataset.category || '';
        const rowSeverity = row.dataset.severity || '';
        
        const matchesSearch = text.includes(searchValue);
        const matchesStatus = !statusValue || rowStatus === statusValue;
        const matchesCategory = !categoryValue || rowCategory === categoryValue;
        const matchesSeverity = !severityValue || rowSeverity === severityValue;
        
        if (matchesSearch && matchesStatus && matchesCategory && matchesSeverity) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    updateFilteredCount();
}

/**
 * Update the filtered count display
 */
function updateFilteredCount() {
    const visibleRows = document.querySelectorAll('#incidentsTable tbody tr:not([style*="display: none"])');
    const countElement = document.getElementById('filteredCount');
    if (countElement) {
        countElement.textContent = visibleRows.length;
    }
}

/**
 * Handle incident type change
 */
function handleIncidentTypeChange() {
    const incidentType = document.getElementById('incidentType');
    const othersField = document.getElementById('othersTypeField');
    
    if (incidentType && othersField) {
        incidentType.addEventListener('change', function() {
            if (this.value === 'others') {
                othersField.style.display = 'block';
                othersField.querySelector('input').required = true;
            } else {
                othersField.style.display = 'none';
                othersField.querySelector('input').required = false;
                othersField.querySelector('input').value = '';
            }
        });
    }
    
    const incidentCategory = document.getElementById('incidentCategory');
    const othersCategoryField = document.getElementById('othersCategoryField');
    
    if (incidentCategory && othersCategoryField) {
        incidentCategory.addEventListener('change', function() {
            if (this.value === 'others') {
                othersCategoryField.style.display = 'block';
                othersCategoryField.querySelector('input').required = true;
            } else {
                othersCategoryField.style.display = 'none';
                othersCategoryField.querySelector('input').required = false;
                othersCategoryField.querySelector('input').value = '';
            }
        });
    }
}

/**
 * Handle anonymous toggle
 */
function handleAnonymousToggle() {
    const anonymousToggle = document.getElementById('isAnonymous');
    const reporterFields = document.getElementById('reporterFields');
    
    if (anonymousToggle && reporterFields) {
        anonymousToggle.addEventListener('change', function() {
            if (this.checked) {
                reporterFields.style.display = 'none';
                // Clear required fields
                const inputs = reporterFields.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.required = false;
                });
            } else {
                reporterFields.style.display = 'block';
                // Restore required attributes
                const employeeInput = document.getElementById('employeeSearch');
                if (employeeInput) employeeInput.required = true;
            }
        });
    }
}

/**
 * Search employees for selection
 */
function searchEmployees(query) {
    if (query.length < 2) {
        hideEmployeeDropdown();
        return;
    }
    
    fetch(`incidents.php?action=search_employees&q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displayEmployeeDropdown(data);
        })
        .catch(error => {
            console.error('Search error:', error);
        });
}

/**
 * Display employee search results
 */
function displayEmployeeDropdown(employees) {
    let dropdown = document.getElementById('employeeDropdown');
    
    if (!dropdown) {
        dropdown = document.createElement('div');
        dropdown.id = 'employeeDropdown';
        dropdown.className = 'employee-dropdown';
        document.getElementById('employeeSearch').parentElement.appendChild(dropdown);
    }
    
    if (employees.length === 0) {
        dropdown.innerHTML = '<div class="no-results">No employees found</div>';
        dropdown.style.display = 'block';
        return;
    }
    
    dropdown.innerHTML = employees.map(emp => `
        <div class="employee-select-item" data-id="${emp.id}" data-name="${emp.first_name} ${emp.last_name}" data-department="${emp.department || ''}">
            <strong>${emp.first_name} ${emp.last_name}</strong>
            <small>${emp.department || 'No Department'}</small>
        </div>
    `).join('');
    
    dropdown.style.display = 'block';
}

/**
 * Hide employee dropdown
 */
function hideEmployeeDropdown() {
    const dropdown = document.getElementById('employeeDropdown');
    if (dropdown) {
        dropdown.style.display = 'none';
    }
}

/**
 * Select employee from dropdown
 */
function selectEmployee(id, name, department) {
    document.getElementById('employeeSearch').value = name;
    document.getElementById('selectedEmployeeId').value = id;
    document.getElementById('employeeDepartment').value = department || '';
    hideEmployeeDropdown();
}

/**
 * Submit incident report
 */
function submitIncidentReport() {
    const form = document.getElementById('incidentReportForm');
    const formData = new FormData(form);
    
    // Add action
    formData.append('action', 'report_incident');
    
    // Validate
    if (!validateIncidentForm()) {
        return;
    }
    
    // Show loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    fetch('incidents.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Incident reported successfully! ID: ' + data.incident_id);
            jQuery('#reportIncidentModal').modal('hide');
            form.reset();
            document.getElementById('filePreview').innerHTML = '';
            location.reload();
        } else {
            alert(data.message || 'Failed to submit incident');
        }
    })
    .catch(error => {
        console.error('Submit error:', error);
        alert('Failed to submit incident. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

/**
 * Validate incident form
 */
function validateIncidentForm() {
    const employeeId = document.getElementById('selectedEmployeeId');
    const isAnonymous = document.getElementById('isAnonymous');
    
    if (!isAnonymous.checked && (!employeeId || !employeeId.value)) {
        alert('Please select an employee');
        return false;
    }
    
    const incidentDate = document.getElementById('incidentDate');
    if (incidentDate && new Date(incidentDate.value) > new Date()) {
        alert('Incident date cannot be in the future');
        return false;
    }
    
    return true;
}

/**
 * View incident details
 */
function viewIncident(incidentId) {
    fetch(`incidents.php?action=get_incident&id=${incidentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayIncidentDetails(data.incident);
                jQuery('#viewIncidentModal').modal('show');
            } else {
                alert('Failed to load incident details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load incident details');
        });
}

/**
 * Display incident details in modal
 */
function displayIncidentDetails(incident) {
    document.getElementById('viewIncidentId').textContent = incident.incident_id;
    document.getElementById('viewEmployeeName').textContent = incident.employee_name || 'N/A';
    document.getElementById('viewDepartment').textContent = incident.department || 'N/A';
    document.getElementById('viewIncidentDate').textContent = formatDate(incident.incident_date);
    document.getElementById('viewIncidentTime').textContent = incident.incident_time || 'N/A';
    document.getElementById('viewLocation').textContent = incident.location || 'N/A';
    document.getElementById('viewIncidentType').textContent = incident.incident_type;
    document.getElementById('viewCategory').textContent = incident.category;
    document.getElementById('viewSeverity').textContent = incident.severity;
    document.getElementById('viewStatus').textContent = incident.status;
    document.getElementById('viewDescription').textContent = incident.description || 'No description';
    
    // Set status badge class
    const statusBadge = document.getElementById('viewStatus');
    statusBadge.className = 'badge badge-' + getStatusColor(incident.status);
    
    // Set severity badge class
    const severityBadge = document.getElementById('viewSeverity');
    severityBadge.className = 'badge badge-' + getSeverityColor(incident.severity);
    
    // Display people involved
    displayPeopleInvolved(incident);
    
    // Display attachments
    displayAttachments(incident.attachments);
    
    // Display notes
    displayIncidentNotes(incident.notes);
}

/**
 * Display people involved in incident
 */
function displayPeopleInvolved(incident) {
    const container = document.getElementById('viewPeopleInvolved');
    if (!container) return;
    
    let html = '';
    
    if (incident.complainant_name) {
        html += `<p><strong>Complainant:</strong> ${incident.complainant_name}</p>`;
    }
    if (incident.respondent_name) {
        html += `<p><strong>Respondent:</strong> ${incident.respondent_name}</p>`;
    }
    if (incident.witnesses) {
        html += `<p><strong>Witnesses:</strong> ${incident.witnesses}</p>`;
    }
    
    container.innerHTML = html || '<p>No people involved recorded</p>';
}

/**
 * Display incident attachments
 */
function displayAttachments(attachments) {
    const container = document.getElementById('viewAttachments');
    if (!container) return;
    
    if (!attachments || attachments.length === 0) {
        container.innerHTML = '<p>No attachments</p>';
        return;
    }
    
    container.innerHTML = attachments.map(att => `
        <a href="${att.file_path}" target="_blank" class="btn btn-sm btn-info">
            <i class="fas fa-file"></i> ${att.file_name}
        </a>
    `).join(' ');
}

/**
 * Display incident notes
 */
function displayIncidentNotes(notes) {
    const container = document.getElementById('viewNotes');
    if (!container) return;
    
    if (!notes || notes.length === 0) {
        container.innerHTML = '<p>No notes yet</p>';
        return;
    }
    
    container.innerHTML = notes.map(note => `
        <div class="note-item mb-2">
            <small class="text-muted">${note.created_by} - ${formatDate(note.created_at)}</small>
            <p>${note.content}</p>
        </div>
    `).join('');
}

/**
 * Edit incident
 */
function editIncident(incidentId) {
    fetch(`incidents.php?action=get_incident&id=${incidentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditForm(data.incident);
                jQuery('#updateIncidentModal').modal('show');
            } else {
                alert('Failed to load incident');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load incident');
        });
}

/**
 * Populate edit form
 */
function populateEditForm(incident) {
    document.getElementById('editIncidentId').value = incident.id;
    document.getElementById('editIncidentType').value = incident.incident_type;
    document.getElementById('editCategory').value = incident.category;
    document.getElementById('editSeverity').value = incident.severity;
    document.getElementById('editStatus').value = incident.status;
    document.getElementById('editDescription').value = incident.description;
    document.getElementById('editResolutionNotes').value = incident.resolution_notes || '';
}

/**
 * Update incident
 */
function updateIncident() {
    const form = document.getElementById('updateIncidentForm');
    const formData = new FormData(form);
    formData.append('action', 'update_incident');
    
    fetch('incidents.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Incident updated successfully');
            jQuery('#updateIncidentModal').modal('hide');
            location.reload();
        } else {
            alert(data.message || 'Failed to update incident');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update incident');
    });
}

/**
 * Resolve incident
 */
function resolveIncident(incidentId) {
    if (!confirm('Are you sure you want to mark this incident as resolved?')) {
        return;
    }
    
    const resolutionNotes = prompt('Enter resolution notes:');
    if (resolutionNotes === null) return;
    
    const formData = new FormData();
    formData.append('action', 'resolve_incident');
    formData.append('incident_id', incidentId);
    formData.append('resolution_notes', resolutionNotes);
    
    fetch('incidents.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Incident resolved successfully');
            location.reload();
        } else {
            alert(data.message || 'Failed to resolve incident');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to resolve incident');
    });
}

/**
 * Add investigation note
 */
function addInvestigationNote() {
    const incidentId = document.getElementById('viewIncidentId').textContent;
    const noteContent = document.getElementById('investigationNote').value;
    
    if (!noteContent.trim()) {
        alert('Please enter a note');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'add_note');
    formData.append('incident_id', incidentId);
    formData.append('content', noteContent);
    
    fetch('incidents.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('investigationNote').value = '';
            viewIncident(incidentId); // Refresh the details
            alert('Note added successfully');
        } else {
            alert('Failed to add note');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add note');
    });
}

/**
 * Assign HR officer
 */
function assignHROfficer() {
    const incidentId = document.getElementById('editIncidentId').value;
    const officerId = document.getElementById('assignedHROfficer').value;
    
    if (!officerId) {
        alert('Please select an HR officer');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'assign_officer');
    formData.append('incident_id', incidentId);
    formData.append('officer_id', officerId);
    
    fetch('incidents.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('HR officer assigned successfully');
            location.reload();
        } else {
            alert('Failed to assign HR officer');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to assign HR officer');
    });
}

/**
 * Get status badge color
 */
function getStatusColor(status) {
    const colors = {
        'open': 'primary',
        'waiting': 'warning',
        'in_progress': 'info',
        'resolved': 'success',
        'escalated': 'danger',
        'closed': 'secondary'
    };
    return colors[status] || 'secondary';
}

/**
 * Get severity badge color
 */
function getSeverityColor(severity) {
    const colors = {
        'low': 'success',
        'medium': 'warning',
        'high': 'danger',
        'critical': 'dark'
    };
    return colors[severity] || 'secondary';
}

/**
 * Format date
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Make functions globally available
window.viewIncident = viewIncident;
window.editIncident = editIncident;
window.resolveIncident = resolveIncident;
window.addInvestigationNote = addInvestigationNote;
window.assignHROfficer = assignHROfficer;
window.removeFile = removeFile;
window.exportIncidents = exportIncidents;

// =====================================================
// WORKFLOW FUNCTIONS
// =====================================================

// HR Review Functions
window.openHRReview = function(incidentId) {
    document.getElementById('hrReviewIncidentId').textContent = incidentId;
    document.getElementById('hrReviewDetails').innerHTML = '<p>Loading...</p>';
    
    fetch(`incidents.php?action=get_incident&id=${incidentId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const i = data.incident;
                document.getElementById('hrReviewDetails').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Type:</strong> ${i.incident_type}</p>
                            <p><strong>Title:</strong> ${i.title}</p>
                            <p><strong>Description:</strong> ${i.description}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Location:</strong> ${i.location || 'N/A'}</p>
                            <p><strong>Date:</strong> ${i.incident_date}</p>
                            <p><strong>Respondent:</strong> ${i.respondent_name || 'N/A'}</p>
                        </div>
                    </div>
                `;
                document.getElementById('reviewViolationType').value = i.violation_type || 'minor';
                document.getElementById('reviewSeverity').value = i.severity || 'medium';
            }
        });
    
    jQuery('#hrReviewModal').modal('show');
};

window.hrAccept = function() {
    const incidentId = document.getElementById('hrReviewIncidentId').textContent;
    const violationType = document.getElementById('reviewViolationType').value;
    const severity = document.getElementById('reviewSeverity').value;
    const notes = document.getElementById('hrReviewNotes').value;
    
    if (!confirm('Accept this incident and proceed to classification?')) return;
    
    fetch(`incidents.php?action=hr_accept`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `incident_id=${incidentId}&previous_status=open&violation_type=${violationType}&severity=${severity}&notes=${encodeURIComponent(notes)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Incident accepted and moved to Under Review');
            jQuery('#hrReviewModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
};

window.hrReject = function() {
    const incidentId = document.getElementById('hrReviewIncidentId').textContent;
    const reason = document.getElementById('hrReviewNotes').value;
    
    if (!reason) {
        alert('Please provide a reason for rejection');
        return;
    }
    
    if (!confirm('Reject this incident? This action cannot be undone.')) return;
    
    fetch(`incidents.php?action=hr_reject`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `incident_id=${incidentId}&previous_status=open&rejection_reason=${encodeURIComponent(reason)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Incident rejected');
            jQuery('#hrReviewModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
};

window.hrRequestInfo = function() {
    const incidentId = document.getElementById('hrReviewIncidentId').textContent;
    const notes = document.getElementById('hrReviewNotes').value;
    
    if (!notes) {
        alert('Please specify what information is needed');
        return;
    }
    
    fetch(`incidents.php?action=hr_request_info`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `incident_id=${incidentId}&previous_status=open&request_info_notes=${encodeURIComponent(notes)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Information request sent to reporter');
            jQuery('#hrReviewModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
};

// Assign Investigator Functions
window.openAssignInvestigator = function(incidentId) {
    document.getElementById('assignInvestigatorId').textContent = incidentId;
    document.getElementById('selectedOfficerId').value = '';
    document.getElementById('selectedOfficerName').textContent = 'No officer selected';
    document.getElementById('repeatOffenderWarning').style.display = 'none';
    document.getElementById('hrOfficerResults').innerHTML = '';
    
    jQuery('#assignInvestigatorModal').modal('show');
};

window.searchHrOfficers = function() {
    const query = document.getElementById('searchHrOfficer').value;
    if (query.length < 2) return;
    
    fetch(`incidents.php?action=search_hr_officers&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('hrOfficerResults');
            container.innerHTML = data.map(o => `
                <a href="#" class="list-group-item list-group-item-action" onclick="selectHrOfficer(${o.id}, '${o.first_name} ${o.last_name}', '${o.respondent_name}')">
                    ${o.first_name} ${o.last_name} - ${o.position}
                </a>
            `).join('');
        });
};

window.selectHrOfficer = function(id, name, respondentName) {
    document.getElementById('selectedOfficerId').value = id;
    document.getElementById('selectedOfficerName').textContent = name;
    document.getElementById('hrOfficerResults').innerHTML = '';
    
    // Check for repeat offender
    if (respondentName) {
        fetch(`incidents.php?action=check_repeat_offender&respondent_name=${encodeURIComponent(respondentName)}`)
            .then(res => res.json())
            .then(data => {
                if (data.is_repeat_offender) {
                    document.getElementById('repeatOffenderWarning').style.display = 'block';
                }
            });
    }
};

window.assignInvestigator = function() {
    const incidentId = document.getElementById('assignInvestigatorId').textContent;
    const officerId = document.getElementById('selectedOfficerId').value;
    
    if (!officerId) {
        alert('Please select an HR officer');
        return;
    }
    
    fetch(`incidents.php?action=assign_investigator`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `incident_id=${incidentId}&officer_id=${officerId}&previous_status=under_review`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Investigator assigned. Case moved to In Progress. SLA: 5 days');
            if (data.repeat_offender) {
                alert('WARNING: This is a repeat offender!');
            }
            jQuery('#assignInvestigatorModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
};

// Investigation Notes Functions
window.openInvestigationNotes = function(incidentId) {
    document.getElementById('investigationNotesId').textContent = incidentId;
    document.getElementById('investigationNoteContent').value = '';
    
    fetch(`incidents.php?action=get_incident&id=${incidentId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const i = data.incident;
                document.getElementById('investigationCaseDetails').innerHTML = `
                    <p><strong>Type:</strong> ${i.incident_type}</p>
                    <p><strong>Severity:</strong> <span class="badge badge-${getSeverityColor(i.severity)}">${i.severity}</span></p>
                    <p><strong>Classification:</strong> ${i.violation_type}</p>
                    <p><strong>Assigned To:</strong> ${i.assignee_first_name || 'Unassigned'}</p>
                    <p><strong>SLA Deadline:</strong> ${i.sla_deadline ? formatDate(i.sla_deadline) : 'Not set'}</p>
                    ${i.repeat_offender ? '<p><span class="badge badge-danger">REPEAT OFFENDER</span></p>' : ''}
                `;
                
                const evidenceHtml = i.attachments && i.attachments.length > 0 
                    ? i.attachments.map(a => `<a href="${a.file_path}" target="_blank" class="btn btn-sm btn-info mb-1"><i class="fas fa-file"></i> ${a.file_name}</a>`).join('')
                    : '<p>No evidence attached</p>';
                document.getElementById('investigationEvidence').innerHTML = evidenceHtml;
                
                // Display notes timeline
                const notesHtml = i.notes && i.notes.length > 0
                    ? i.notes.map(n => `
                        <div class="timeline-item">
                            <i class="fas fa-note bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> ${formatDate(n.created_at)}</span>
                                <h3 class="timeline-header">${n.first_name} ${n.last_name}</h3>
                                <div class="timeline-body">${n.content}</div>
                            </div>
                        </div>
                    `).join('')
                    : '<p>No notes yet</p>';
                document.getElementById('investigationNotesTimeline').innerHTML = notesHtml;
            }
        });
    
    jQuery('#investigationNotesModal').modal('show');
};

window.addInvestigationNote = function() {
    const incidentId = document.getElementById('investigationNotesId').textContent;
    const content = document.getElementById('investigationNoteContent').value;
    
    if (!content) {
        alert('Please enter a note');
        return;
    }
    
    fetch(`incidents.php?action=add_note`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `incident_id=${incidentId}&content=${encodeURIComponent(content)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('investigationNoteContent').value = '';
            openInvestigationNotes(incidentId);
        } else {
            alert('Error adding note');
        }
    });
};

window.escalateCase = function() {
    const incidentId = document.getElementById('investigationNotesId').textContent;
    
    if (!confirm('Escalate this case to Senior HR?')) return;
    
    fetch(`incidents.php?action=escalate_case`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `incident_id=${incidentId}&previous_status=in_progress`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Case escalated to Senior HR');
            jQuery('#investigationNotesModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
};

window.showDecisionModal = function() {
    const incidentId = document.getElementById('investigationNotesId').textContent;
    jQuery('#investigationNotesModal').modal('hide');
    document.getElementById('decisionIncidentId').textContent = incidentId;
    document.getElementById('finalDecision').value = '';
    document.getElementById('resolutionNotes').value = '';
    document.getElementById('decisionRemarks').value = '';
    document.getElementById('approvalRequirementAlert').style.display = 'none';
    jQuery('#decisionModal').modal('show');
};

window.toggleApprovalRequirement = function() {
    const decision = document.getElementById('finalDecision').value;
    const requiresApproval = ['verbal_warning', 'written_warning', 'suspension', 'termination'].includes(decision);
    document.getElementById('approvalRequirementAlert').style.display = requiresApproval ? 'block' : 'none';
};

window.submitDecision = function() {
    const incidentId = document.getElementById('decisionIncidentId').textContent;
    const decision = document.getElementById('finalDecision').value;
    const resolutionNotes = document.getElementById('resolutionNotes').value;
    const remarks = document.getElementById('decisionRemarks').value;
    
    if (!decision) {
        alert('Please select a decision');
        return;
    }
    
    fetch(`incidents.php?action=submit_decision`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `incident_id=${incidentId}&previous_status=in_progress&decision=${decision}&resolution_notes=${encodeURIComponent(resolutionNotes)}&remarks=${encodeURIComponent(remarks)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Decision submitted. ' + (data.new_status === 'pending_approval' ? 'Pending Manager approval.' : 'Case resolved.'));
            jQuery('#decisionModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
};

// Manager Approval Functions
window.openApprovalModal = function(incidentId) {
    document.getElementById('approvalIncidentId').textContent = incidentId;
    document.getElementById('managerComments').value = '';
    
    fetch(`incidents.php?action=get_incident&id=${incidentId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const i = data.incident;
                document.getElementById('approvalDetails').innerHTML = `
                    <div class="alert alert-info">
                        <p><strong>HR Decision:</strong> ${i.decision}</p>
                        <p><strong>Resolution Notes:</strong> ${i.resolution_notes || 'N/A'}</p>
                        <p><strong>Submitted By:</strong> ${i.assignee_first_name} ${i.assignee_last_name}</p>
                    </div>
                `;
            }
        });
    
    jQuery('#approvalModal').modal('show');
};

window.approveDecision = function() {
    const incidentId = document.getElementById('approvalIncidentId').textContent;
    
    if (!confirm('Approve this decision and resolve the case?')) return;
    
    fetch(`incidents.php?action=approve_decision`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `incident_id=${incidentId}&previous_status=pending_approval`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Decision approved. Case resolved.');
            jQuery('#approvalModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
};

window.rejectDecision = function() {
    const incidentId = document.getElementById('approvalIncidentId').textContent;
    const comments = document.getElementById('managerComments').value;
    
    if (!comments) {
        alert('Please provide comments for rejection');
        return;
    }
    
    if (!confirm('Reject this decision and return to investigation?')) return;
    
    fetch(`incidents.php?action=reject_decision`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `incident_id=${incidentId}&previous_status=pending_approval&rejection_reason=${encodeURIComponent(comments)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Decision rejected. Case returned to investigation.');
            jQuery('#approvalModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
};

// Audit Log Functions
window.openAuditLog = function(incidentId) {
    document.getElementById('auditLogIncidentId').textContent = incidentId;
    
    fetch(`incidents.php?action=get_incident&id=${incidentId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.incident.audit_log) {
                const logs = data.incident.audit_log;
                document.getElementById('auditLogTimeline').innerHTML = logs.map(log => `
                    <div class="timeline-item">
                        <i class="fas fa-history bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> ${formatDate(log.created_at)}</span>
                            <h3 class="timeline-header">${log.action}</h3>
                            <div class="timeline-body">
                                <p><strong>From:</strong> ${log.old_status || 'N/A'} → <strong>To:</strong> ${log.new_status || 'N/A'}</p>
                                <p><strong>By:</strong> ${log.first_name} ${log.last_name}</p>
                                ${log.notes ? `<p><strong>Notes:</strong> ${log.notes}</p>` : ''}
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                document.getElementById('auditLogTimeline').innerHTML = '<p>No audit history available</p>';
            }
        });
    
    jQuery('#auditLogModal').modal('show');
};
