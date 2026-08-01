/**
 * Employee Contributions Checklist JavaScript
 * Handles interactive functionality for the contributions tracking module
 */

$(document).ready(function() {
    // Initialize DataTable
    const table = $('#contributionsTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[1, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": 0 }, // Checkbox column
            { "orderable": false, "targets": 7 }  // Actions column
        ],
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered FROM lc__MAX_ total entries)",
            "zeroRecords": "No matching records found",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });

    // Select All Checkbox
    $('#selectAll').on('click', function() {
        const isChecked = $(this).prop('checked');
        $('.employee-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    });

    // Individual Checkbox
    $('.employee-checkbox').on('click', function() {
        updateSelectedCount();
        
        // Update select all checkbox
        const totalCheckboxes = $('.employee-checkbox').length;
        const checkedCheckboxes = $('.employee-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Update Selected Count
    function updateSelectedCount() {
        const count = $('.employee-checkbox:checked').length;
        $('#selectedCount').text(count);
    }

    // View Details Button
    $('.view-details').on('click', function() {
        const employeeId = $(this).data('employee-id');
        const row = $(`tr[data-employee-id="${employeeId}"]`);
        
        const employeeName = row.find('td:eq(1)').text();
        const department = row.find('td:eq(2)').text();
        const position = row.find('td:eq(3)').text();
        const sssStatus = row.find('td:eq(4) .badge').text();
        const pagibigStatus = row.find('td:eq(5) .badge').text();
        const philhealthStatus = row.find('td:eq(6) .badge').text();
        
        // Get contribution numbers FROM lc_data attributes
        const sssNumber = row.find('td:eq(4)').data('sss-number') || 'Not provided';
        const pagibigNumber = row.find('td:eq(5)').data('pagibig-number') || 'Not provided';
        const philhealthNumber = row.find('td:eq(6)').data('philhealth-number') || 'Not provided';
        
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Employee Information</h6>
                    <p><strong>Name:</strong> ${employeeName}</p>
                    <p><strong>Department:</strong> ${department}</p>
                    <p><strong>Position:</strong> ${position}</p>
                </div>
                <div class="col-md-6">
                    <h6>Contribution Status</h6>
                    <p><strong>SSS:</strong> <span class="badge badge-${getStatusBadgeClass(sssStatus)}">${sssStatus}</span></p>
                    <p><strong>SSS Number:</strong> ${sssNumber}</p>
                    <p><strong>PAGIBIG:</strong> <span class="badge badge-${getStatusBadgeClass(pagibigStatus)}">${pagibigStatus}</span></p>
                    <p><strong>PAG-IBIG MID Number:</strong> ${pagibigNumber}</p>
                    <p><strong>PhilHealth:</strong> <span class="badge badge-${getStatusBadgeClass(philhealthStatus)}">${philhealthStatus}</span></p>
                    <p><strong>PhilHealth Number:</strong> ${philhealthNumber}</p>
                </div>
            </div>
        `;
        
        $('#viewDetailsContent').html(content);
        $('#viewDetailsModal').modal('show');
    });

    // Edit Contribution Button
    $('.edit-contribution').on('click', function() {
        const employeeId = $(this).data('employee-id');
        const row = $(`tr[data-employee-id="${employeeId}"]`);
        
        const employeeName = row.find('td:eq(1)').text();
        const department = row.find('td:eq(2)').text();
        const position = row.find('td:eq(3)').text();
        
        // Get current status values
        const sssStatus = getStatusValue(row.find('td:eq(4) .badge').text());
        const pagibigStatus = getStatusValue(row.find('td:eq(5) .badge').text());
        const philhealthStatus = getStatusValue(row.find('td:eq(6) .badge').text());
        
        // Populate modal
        $('#modalEmployeeId').val(employeeId);
        $('#modalEmployeeName').text(employeeName);
        $('#modalDepartment').text(department);
        $('#modalPosition').text(position);
        $('#modalSssStatus').val(sssStatus);
        $('#modalPagibigStatus').val(pagibigStatus);
        $('#modalPhilhealthStatus').val(philhealthStatus);
        
        // Clear contribution numbers
        $('#modalSssNumber').val('');
        $('#modalPagibigNumber').val('');
        $('#modalPhilhealthNumber').val('');
        
        $('#editContributionModal').modal('show');
    });

    // Save Contributions
    $('#saveContributions').on('click', function() {
        const employeeId = $('#modalEmployeeId').val();
        
        // Validate contribution number formats
        const sssNumber = $('#modalSssNumber').val();
        const pagibigNumber = $('#modalPagibigNumber').val();
        const philhealthNumber = $('#modalPhilhealthNumber').val();
        
        // SSS format: XX-XXXXXXX-X
        if (sssNumber && !/^\d{2}-\d{7}-\d{1}$/.test(sssNumber)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid SSS Number',
                text: 'SSS number must be in format: XX-XXXXXXX-X (e.g., 12-3456789-0)',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        // PAG-IBIG format: XXXX-XXXX-XXXX
        if (pagibigNumber && !/^\d{4}-\d{4}-\d{4}$/.test(pagibigNumber)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid PAG-IBIG Number',
                text: 'PAG-IBIG number must be in format: XXXX-XXXX-XXXX (e.g., 1234-5678-9012)',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        // PhilHealth format: XXXX-XXXX-XXXX
        if (philhealthNumber && !/^\d{4}-\d{4}-\d{4}$/.test(philhealthNumber)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid PhilHealth Number',
                text: 'PhilHealth number must be in format: XXXX-XXXX-XXXX (e.g., 1234-5678-9012)',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        const contributions = [
            { type: 'sss', status: $('#modalSssStatus').val(), contribution_number: sssNumber },
            { type: 'pagibig', status: $('#modalPagibigStatus').val(), contribution_number: pagibigNumber },
            { type: 'philhealth', status: $('#modalPhilhealthStatus').val(), contribution_number: philhealthNumber }
        ];
        
        let completed = 0;
        let hasError = false;
        
        // Show loading state
        const saveBtn = $(this);
        const originalText = saveBtn.html();
        saveBtn.html('<span class="loading-spinner"></span> Saving...').prop('disabled', true);
        
        // Update each contribution
        contributions.forEach(function(contribution) {
            $.ajax({
                url: 'employee_contributions.php',
                method: 'POST',
                data: {
                    action: 'update_contribution',
                    employee_id: employeeId,
                    contribution_type: contribution.type,
                    status: contribution.status,
                    contribution_number: contribution.contribution_number
                },
                dataType: 'json',
                success: function(response) {
                    completed++;
                    
                    if (!response.success) {
                        hasError = true;
                        console.error('Error updating ' + contribution.type + ':', response.message);
                    }
                    
                    if (completed === contributions.length) {
                        saveBtn.html(originalText).prop('disabled', false);
                        
                        if (hasError) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Some contributions could not be updated. Please try again.',
                                confirmButtonColor: '#dc3545'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Contributions updated successfully!',
                                confirmButtonColor: '#28a745',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload();
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    completed++;
                    hasError = true;
                    console.error('AJAX error for ' + contribution.type + ':', error);
                    
                    if (completed === contributions.length) {
                        saveBtn.html(originalText).prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating contributions. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                }
            });
        });
    });

    // Bulk Update Button
    $('#applyBulkUpdate').on('click', function() {
        const selectedIds = [];
        $('.employee-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select at least one employee.',
                confirmButtonColor: '#ffc107'
            });
            return;
        }
        
        const contributionType = $('#bulkUpdateForm select[name="contribution_type"]').val();
        const status = $('#bulkUpdateForm select[name="status"]').val();
        
        // Show loading state
        const applyBtn = $(this);
        const originalText = applyBtn.html();
        applyBtn.html('<span class="loading-spinner"></span> Applying...').prop('disabled', true);
        
        $.ajax({
            url: 'employee_contributions.php',
            method: 'POST',
            data: {
                action: 'bulk_update',
                employee_ids: JSON.stringify(selectedIds),
                contribution_type: contributionType,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                applyBtn.html(originalText).prop('disabled', false);
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Bulk update completed successfully!',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Bulk update failed. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr, status, error) {
                applyBtn.html(originalText).prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred during bulk update. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });

    // Helper Functions
    function getStatusBadgeClass(status) {
        switch (status.toLowerCase()) {
            case 'submitted':
                return 'success';
            case 'pending':
                return 'warning';
            default:
                return 'secondary';
        }
    }

    function getStatusValue(badgeText) {
        const text = badgeText.toLowerCase().trim();
        if (text === 'submitted') return 'submitted';
        return 'pending';
    }

    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();

    // Input formatting for contribution numbers
    // SSS Number: XX-XXXXXXX-X format
    $('#modalSssNumber').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        if (value.length > 10) value = value.substring(0, 10);
        
        if (value.length >= 2) {
            value = value.substring(0, 2) + '-' + value.substring(2);
        }
        if (value.length >= 10) {
            value = value.substring(0, 10) + '-' + value.substring(10);
        }
        
        $(this).val(value);
    });

    // PAG-IBIG and PhilHealth Number: XXXX-XXXX-XXXX format
    $('#modalPagibigNumber, #modalPhilhealthNumber').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        if (value.length > 12) value = value.substring(0, 12);
        
        if (value.length >= 4) {
            value = value.substring(0, 4) + '-' + value.substring(4);
        }
        if (value.length >= 9) {
            value = value.substring(0, 9) + '-' + value.substring(9);
        }
        
        $(this).val(value);
    });

    // Auto-refresh every 5 minutes (optional)
    // setInterval(function() {
    //     location.reload();
    // }, 300000);
});
