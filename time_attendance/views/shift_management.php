<?php
session_start();
require_once '../app/models/ShiftValidator.php';

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR') {
    header('Location: /time_attendance/time_attendance.php');
    exit;
}

$shiftValidator = new ShiftValidator();
$unassignedCount = $shiftValidator->getUnassignedShiftCount();
$availableShifts = $shiftValidator->getAvailableShifts();
$unassignedEmployees = $shiftValidator->getEmployeesWithoutShift();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .shift-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .shift-header {
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 15px;
            padding-bottom: 15px;
        }
        .employee-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            background: white;
            margin-bottom: 5px;
            border-radius: 4px;
        }
        .employee-row:hover {
            background-color: #f9f9f9;
        }
        .alert-banner {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-assign {
            padding: 6px 12px;
            font-size: 12px;
        }
        .badge-count {
            background-color: #ff6b6b;
            color: white;
            padding: 8px 12px;
            border-radius: 50%;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="mb-0"><i class="fas fa-clock"></i> Shift Management</h1>
                <small class="text-muted">Assign and manage employee shifts</small>
            </div>
        </div>

        <!-- Alert Banner for Unassigned Employees -->
        <?php if ($unassignedCount > 0): ?>
        <div class="alert-banner">
            <i class="fas fa-exclamation-circle fa-2x"></i>
            <div>
                <strong><?php echo $unassignedCount; ?> Employee(s) without shift assignment!</strong>
                <br>
                <small>These employees cannot complete time in/out until a shift is assigned.</small>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-success mb-4">
            <i class="fas fa-check-circle"></i> All active employees have shift assignments!
        </div>
        <?php endif; ?>

        <!-- Unassigned Employees Section -->
        <div class="shift-card">
            <div class="shift-header">
                <h4><i class="fas fa-user-slash"></i> Unassigned Employees (<span class="badge-count"><?php echo $unassignedCount; ?></span>)</h4>
                <button class="btn btn-success float-right" onclick="bulkAssignShifts()" title="Assign shifts to selected employees">
                    <i class="fas fa-check-double"></i> Bulk Assign
                </button>
            </div>
            
            <?php if (count($unassignedEmployees) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAllEmployees" onchange="toggleSelectAll(this)"> 
                                </th>
                                <th>Employee Name</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unassignedEmployees as $emp): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="employee-checkbox" value="<?php echo $emp['employee_id']; ?>" data-name="<?php echo htmlspecialchars($emp['full_name']); ?>">
                                </td>
                                <td><strong><?php echo htmlspecialchars($emp['full_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($emp['department'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($emp['position'] ?? 'N/A'); ?></td>
                                <td><span class="badge badge-warning">Unassigned</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> All employees have been assigned shifts.
                </div>
            <?php endif; ?>
        </div>

        <!-- Available Shifts -->
        <div class="shift-card">
            <div class="shift-header">
                <h4><i class="fas fa-layer-group"></i> Available Shifts</h4>
            </div>
            
            <?php if (count($availableShifts) > 0): ?>
                <div class="row">
                    <?php foreach ($availableShifts as $shift): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-left-primary shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-primary"><?php echo htmlspecialchars($shift['shift_name']); ?></h6>
                                <p class="card-text small mb-0">
                                    <i class="fas fa-clock"></i> 
                                    <?php echo date('h:i A', strtotime($shift['start_time'])) . ' - ' . date('h:i A', strtotime($shift['end_time'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle"></i> No shifts available. Create shifts first.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Assign Shift Modal -->
    <div class="modal fade" id="assignShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Assign Shift to Employee(s)</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="assignShiftForm">
                        <!-- Selected Employees List -->
                        <div class="form-group">
                            <label><strong>Selected Employees:</strong></label>
                            <div id="selectedEmployeesList" style="background: #f9f9f9; padding: 12px; border-radius: 4px; min-height: 50px; max-height: 150px; overflow-y: auto; border: 1px solid #ddd;">
                                <p class="text-muted mb-0">No employees selected yet</p>
                            </div>
                        </div>
                        
                        <!-- Shift Selection -->
                        <div class="form-group">
                            <label>Select Shift *</label>
                            <select id="shiftSelect" class="form-control" required>
                                <option value="">-- Choose Shift --</option>
                                <?php foreach ($availableShifts as $shift): ?>
                                <option value="<?php echo $shift['shift_id']; ?>">
                                    <?php echo htmlspecialchars($shift['shift_name']) . ' (' . 
                                          date('h:i A', strtotime($shift['start_time'])) . ' - ' . 
                                          date('h:i A', strtotime($shift['end_time'])) . ')'; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Date Range -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Effective From *</label>
                                    <input type="date" id="effectiveFrom" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Effective To (Leave empty for ongoing)</label>
                                    <input type="date" id="effectiveTo" class="form-control">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitAssignShift(event)">
                        <i class="fas fa-save"></i> Assign Shift
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Store selected employee IDs
        let selectedEmployees = [];

        // Toggle select all checkboxes
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateSelectedList();
        }

        // Update selected employees list display
        function updateSelectedList() {
            const checkboxes = document.querySelectorAll('.employee-checkbox:checked');
            selectedEmployees = [];
            
            const listDiv = document.getElementById('selectedEmployeesList');
            
            if (checkboxes.length === 0) {
                listDiv.innerHTML = '<p class="text-muted mb-0">No employees selected yet</p>';
                return;
            }

            let html = '<ul style="margin: 0; padding-left: 20px;">';
            checkboxes.forEach(cb => {
                selectedEmployees.push({
                    id: parseInt(cb.value),
                    name: cb.getAttribute('data-name')
                });
                html += '<li>' + cb.getAttribute('data-name') + '</li>';
            });
            html += '</ul>';
            listDiv.innerHTML = html;
        }

        // Open bulk assign modal
        function bulkAssignShifts() {
            const checkboxes = document.querySelectorAll('.employee-checkbox:checked');
            
            if (checkboxes.length === 0) {
                alert('Please select at least one employee to assign shifts');
                return;
            }

            updateSelectedList();
            $('#assignShiftModal').modal('show');
        }

        // Legacy single assign (kept for backward compatibility)
        function assignShift(employeeId, employeeName) {
            // Uncheck all, then check just this one
            document.querySelectorAll('.employee-checkbox').forEach(cb => cb.checked = false);
            const checkbox = document.querySelector(`.employee-checkbox[value="${employeeId}"]`);
            if (checkbox) checkbox.checked = true;
            updateSelectedList();
            $('#assignShiftModal').modal('show');
        }

        // Submit bulk shift assignment
        function submitAssignShift(event) {
            if (selectedEmployees.length === 0) {
                alert('Please select at least one employee');
                return;
            }

            const shiftId = document.getElementById('shiftSelect').value;
            const effectiveFrom = document.getElementById('effectiveFrom').value;
            const effectiveTo = document.getElementById('effectiveTo').value || null;

            if (!shiftId || !effectiveFrom) {
                alert('Please fill in all required fields');
                return;
            }

            // Show loading indicator
            const button = event.currentTarget || event.target;
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';
            }

            // Assign to each selected employee
            let completed = 0;
            let failed = 0;

            selectedEmployees.forEach(employee => {
                fetch('/time_attendance/api/shift_assignment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'assign',
                        employee_id: employee.id,
                        shift_id: shiftId,
                        effective_from: effectiveFrom,
                        effective_to: effectiveTo
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        completed++;
                    } else {
                        failed++;
                    }
                    
                    // Check if all requests are done
                    if (completed + failed === selectedEmployees.length) {
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-save"></i> Assign Shift';
                        
                        if (failed === 0) {
                            alert(`Successfully assigned shifts to ${completed} employee(s)!`);
                            $('#assignShiftModal').modal('hide');
                            location.reload();
                        } else {
                            alert(`Completed: ${completed} assigned, ${failed} failed`);
                            $('#assignShiftModal').modal('hide');
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    failed++;
                    
                    if (completed + failed === selectedEmployees.length) {
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-save"></i> Assign Shift';
                        alert(`Completed with errors. Assigned: ${completed}, Failed: ${failed}`);
                    }
                });
            });
        }

        // Update selected list when checkboxes change
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedList);
            });
        });
    </script>
</body>
</html>
