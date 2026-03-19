// JavaScript functions for policy_admin.php
// Force modals to work properly
$(document).on('show.bs.modal', '.modal', function() {
    const modalId = $(this).attr('id');
    // Get the highest z-index from all visible modals to ensure proper stacking
    let highestZIndex = 1050; // Bootstrap's default modal z-index
    $('.modal:visible').each(function() {
        const style = $(this).attr('style') || '';
        const match = style.match(/z-index:\s*(\d+)/);
        if (match) {
            const z = parseInt(match[1]);
            if (z > highestZIndex) highestZIndex = z;
        }
    });
    
    // For sendReminderModal, always calculate a higher z-index than any visible modal
    if (modalId === 'sendReminderModal') {
        // Set z-index higher than the highest visible modal
        const newZIndex = highestZIndex + 20;
        $(this).css('z-index', newZIndex);
        $(this).find('.modal-dialog').css('z-index', newZIndex + 10);
        setTimeout(function() {
            // Fix the backdrop z-index to be below the modal
            $('.modal-backdrop').each(function() {
                $(this).css('z-index', newZIndex - 10);
            });
        }, 0);
        return;
    }
    
    // For other priority modals, preserve inline z-index if available
    const highPriorityModals = ['sendReminderModal', 'riskFlagDetailsModal', 'employeeDetailsModal', 'editPolicyModal', 'viewPolicyModal', 'approvePolicyModal', 'supervisorApproveModal', 'rejectPolicyModal', 'publishPolicyModal'];
    let zIndex;
    
    if (highPriorityModals.includes(modalId)) {
        const style = $(this).attr('style') || '';
        if (style.includes('z-index')) {
            const match = style.match(/z-index:\s*(\d+)/);
            if (match) {
                zIndex = parseInt(match[1]);
            } else {
                zIndex = highestZIndex + 10;
            }
        } else {
            zIndex = highestZIndex + 10;
        }
    } else {
        zIndex = highestZIndex + 10;
    }
    
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

// Ensure modal content is clickable
$(document).on('shown.bs.modal', '.modal', function() {
    $('.modal-content').css('pointer-events', 'auto');
    $('.modal-backdrop').css('cursor', 'pointer');
});

// Allow clicking backdrop to close
$(document).on('click', '.modal-backdrop', function() {
    $('.modal:visible').modal('hide');
});

/**
 * View policy details - shows policy in pretty-print format
 */
function viewPolicy(id) {
    $.ajax({
        url: 'policy_admin.php?action=get&id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#viewPolicyTitle').text(response.title || 'Policy Details');
            $('#viewPolicyContent').html('<pre style="white-space: pre-wrap; font-family: inherit; font-size: 14px; line-height: 1.6; padding: 15px; background: #f8f9fa; border-radius: 5px;">' + (response.content || 'No content') + '</pre>');
            $('#viewPolicyCategory').text(response.category || 'N/A');
            $('#viewPolicyVersion').text(response.version || '1.0');
            $('#viewPolicyStatus').text(response.status || 'N/A');
            
            if (response.is_mandatory == 1) {
                $('#viewPolicyMandatory').html('<span class="badge badge-danger">Mandatory</span>');
            } else {
                $('#viewPolicyMandatory').html('<span class="badge badge-secondary">Optional</span>');
            }
            
            if (response.acknowledgment_required == 1) {
                $('#viewPolicyAck').html('<span class="badge badge-warning">Required</span>');
            } else {
                $('#viewPolicyAck').html('<span class="badge badge-secondary">Not Required</span>');
            }
            
            $('#viewPolicyModal').modal('show');
        },
        error: function() {
            alert('Failed to load policy details');
        }
    });
}

/**
 * Edit policy - opens edit modal with policy data loaded
 */
function editPolicy(id) {
    // Show loading state first
    $('#editPolicyBody').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Loading policy data...</div>');
    $('#editPolicyModal').modal('show');
    
    $.ajax({
        url: 'policy_admin.php?action=get&id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // Build the edit form with policy data
            $('#editPolicyBody').html(
                '<input type="hidden" name="action" value="update">' +
                '<input type="hidden" name="policy_id" value="' + id + '">' +
                
                '<div class="row">' +
                    '<div class="col-md-8">' +
                        '<div class="form-group">' +
                            '<label>Policy Title *</label>' +
                            '<input type="text" name="title" class="form-control" value="' + (response.title || '') + '" required>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<div class="form-group">' +
                            '<label>Category *</label>' +
                            '<select name="category" class="form-control" required>' +
                                '<option value="">Select Category</option>' +
                                '<option value="HR Policies" ' + (response.category === 'HR Policies' ? 'selected' : '') + '>HR Policies</option>' +
                                '<option value="IT Policies" ' + (response.category === 'IT Policies' ? 'selected' : '') + '>IT Policies</option>' +
                                '<option value="Security Policies" ' + (response.category === 'Security Policies' ? 'selected' : '') + '>Security Policies</option>' +
                                '<option value="General Policies" ' + (response.category === 'General Policies' ? 'selected' : '') + '>General Policies</option>' +
                                '<option value="Compliance" ' + (response.category === 'Compliance' ? 'selected' : '') + '>Compliance</option>' +
                            '</select>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                
                '<div class="row">' +
                    '<div class="col-md-4">' +
                        '<div class="form-group">' +
                            '<label>Version</label>' +
                            '<input type="text" name="version" class="form-control" value="' + (response.version || '1.0') + '">' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<div class="form-group">' +
                            '<label>Changes Summary</label>' +
                            '<input type="text" name="changes_summary" class="form-control" placeholder="Describe changes...">' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                
                '<div class="form-group">' +
                    '<label>Policy Content *</label>' +
                    '<textarea name="content" class="form-control" rows="15" required>' + (response.content || '') + '</textarea>' +
                '</div>'
            );
        },
        error: function() {
            $('#editPolicyBody').html('<div class="alert alert-danger">Error loading policy data. Please try again.</div>');
        }
    });
}

/**
 * Delete policy - with AJAX and confirmation
 */
function deletePolicy(id) {
    if (confirm('Are you sure you want to delete this policy? This action cannot be undone.')) {
        $.ajax({
            url: 'policy_admin.php',
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                action: 'delete',
                policy_id: id
            },
            success: function(response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.success) {
                        alert('Policy deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (result.error || 'Failed to delete policy'));
                    }
                } catch (e) {
                    alert('Policy deleted successfully!');
                    location.reload();
                }
            },
            error: function() {
                alert('Error deleting policy');
            }
        });
    }
}

/**
 * Submit policy for approval
 */
function submitForApproval(id) {
    if (confirm('Submit this policy for approval?')) {
        $.ajax({
            url: 'policy_admin.php',
            type: 'POST',
            data: { action: 'submit', policy_id: id },
            success: function(response) {
                if (response.includes('success')) {
                    alert('Policy submitted for approval');
                    location.reload();
                } else {
                    alert('Failed to submit policy');
                }
            },
            error: function() {
                alert('Failed to submit policy');
            }
        });
    }
}

/**
 * Approve policy
 */
function approvePolicy(id) {
    $('#approvePolicyId').val(id);
    $('#approvePolicyModal').modal('show');
}

/**
 * Supervisor approve
 */
function supervisorApprove(id) {
    $('#supervisorPolicyId').val(id);
    $('#supervisorApproveModal').modal('show');
}

/**
 * Reject policy
 */
function rejectPolicy(id) {
    $('#rejectPolicyId').val(id);
    $('#rejectPolicyModal').modal('show');
}

/**
 * Publish policy
 */
function publishPolicy(id) {
    $('#publishPolicyId').val(id);
    $('#publishPolicyModal').modal('show');
}

/**
 * Filter policies by status
 */
function filterPolicies(status) {
    var url = new URL(window.location.href);
    url.searchParams.set('status', status);
    window.location.href = url.toString();
}

/**
 * Search policies
 */
function searchPolicies() {
    var searchTerm = document.getElementById('policySearch').value;
    var url = new URL(window.location.href);
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    } else {
        url.searchParams.delete('search');
    }
    window.location.href = url.toString();
}

// Initialize on document ready
$(document).ready(function() {
    // Add enter key listener for search
    $('#policySearch').keypress(function(e) {
        if (e.which == 13) {
            searchPolicies();
        }
    });
    
    // Handle Edit Policy Form Submission
    $('#editPolicyForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'policy_admin.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.includes('success') || response.includes('updated successfully')) {
                    alert('Policy updated successfully!');
                    $('#editPolicyModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: Policy update failed');
                }
            },
            error: function() {
                alert('Error updating policy');
            }
        });
    });
});
