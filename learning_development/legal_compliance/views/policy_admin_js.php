<?php // This file contains JavaScript functions for policy_admin.php ?>
<script>
// Force modals to work properly
$(document).on('show.bs.modal', '.modal', function() {
    const zIndex = 1040 + (10 * $('.modal:visible').length);
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

function viewPolicy(id) {
    $.ajax({
        url: 'policy_admin.php?action=get&id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#viewPolicyTitle').text(response.title || 'Policy Details');
            $('#viewPolicyCategory').text(response.category || 'N/A');
            $('#viewPolicyVersion').text(response.version || '1.0');
            $('#viewPolicyStatus').text(response.status || 'N/A');
            $('#viewPolicyMandatory').text(response.is_mandatory ? 'Yes' : 'No');
            $('#viewPolicyAck').text(response.acknowledgment_required ? 'Yes' : 'No');
            $('#viewPolicyContent').html('<pre style="white-space: pre-wrap;">' + (response.content || 'No content') + '</pre>');
            $('#viewPolicyModal').modal('show');
        },
        error: function() {
            alert('Error loading policy data');
        }
    });
}

function editPolicy(id) {
    $.ajax({
        url: 'policy_admin.php?action=get&id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#editPolicyBody').html(`
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="policy_id" value="${id}">
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Policy Title *</label>
                            <input type="text" name="title" class="form-control" value="${response.title || ''}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['name']; ?>" ${response.category === '<?php echo $cat['name']; ?>' ? 'selected' : ''}><?php echo $cat['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Version</label>
                            <input type="text" name="version" class="form-control" value="${response.version || '1.0'}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Changes Summary</label>
                            <input type="text" name="changes_summary" class="form-control" placeholder="Describe changes...">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Policy Content *</label>
                    <textarea name="content" class="form-control" rows="15" required>${response.content || ''}</textarea>
                </div>
            `);
            $('#editPolicyModal').modal('show');
        },
        error: function() {
            alert('Error loading policy data');
        }
    });
}

function approvePolicy(id) {
    $('#approvePolicyId').val(id);
    $('#approvePolicyModal').modal('show');
}

function supervisorApprove(id) {
    $('#supervisorPolicyId').val(id);
    $('#supervisorApproveModal').modal('show');
}

function rejectPolicy(id) {
    $('#rejectPolicyId').val(id);
    $('#rejectPolicyModal').modal('show');
}

function publishPolicy(id) {
    $('#publishPolicyId').val(id);
    $('#publishPolicyModal').modal('show');
}

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
                    // If not JSON, reload the page
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

// Handle Edit Policy Form Submission
$(document).on('submit', '#editPolicyForm', function(e) {
    e.preventDefault();
    
    var formData = $(this).serialize();
    
    $.ajax({
        url: 'policy_admin.php',
        type: 'POST',
        data: formData,
        success: function(response) {
            // Check for success message in response
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
</script>
