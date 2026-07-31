<div class="w-full mt-4">
    <div class="content-wrapper">

        <div class="bg-white rounded-4 shadow-sm border">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center p-4 border-bottom">

                <div>
                    <h3 class="fw-bold text-primary mb-1">
                        <i class="fas fa-calendar-check me-2"></i>
                        Attendance Records
                    </h3>

                    <small class="text-muted">
                        View and monitor employee attendance records.
                    </small>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>
                    Export
                </button>

            </div>

            <!-- Table -->
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-primary">

                        <tr>

                            <th>ID</th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Approval</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($attendance)): ?>

                            <?php foreach ($attendance as $row): ?>

                                <tr>

                                    <td>
                                        #<?= $row['attendance_id']; ?>
                                    </td>

                                    <td>

                                        <div class="d-flex align-items-center">

                                            <div class="rounded-circle bg-primary text-white fw-bold d-flex justify-content-center align-items-center"
                                                style="width:45px;height:45px;">

                                                <?= strtoupper(substr($row['first_name'], 0, 1)); ?>

                                            </div>

                                            <div class="ms-3">

                                                <div class="fw-semibold">

                                                    <?= htmlspecialchars(trim(
                                                        $row['first_name'] . ' ' .
                                                            ($row['middle_name'] ? $row['middle_name'] . ' ' : '') .
                                                            $row['last_name']
                                                    )); ?>

                                                </div>

                                                <small class="text-muted">
                                                    Employee #<?= $row['employee_id']; ?>
                                                </small>

                                            </div>

                                        </div>

                                    </td>

                                    <td>

                                        <span class="badge bg-info-subtle text-dark">
                                            <?= htmlspecialchars($row['department']); ?>
                                        </span>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['position']); ?>
                                    </td>

                                    <td>

                                        <?= date('M d, Y', strtotime($row['attendance_date'])); ?>

                                    </td>

                                    <td>

                                        <?= $row['time_in']
                                            ? date('h:i A', strtotime($row['time_in']))
                                            : '-'; ?>

                                    </td>

                                    <td>

                                        <?= $row['time_out']
                                            ? date('h:i A', strtotime($row['time_out']))
                                            : '-'; ?>

                                    </td>

                                    <td>

                                        <?php
                                        $statusClass = match ($row['status']) {
                                            'PRESENT' => 'success',
                                            'ABSENT' => 'danger',
                                            'LATE' => 'warning',
                                            'PENDING_APPROVAL' => 'secondary',
                                            default => 'primary'
                                        };
                                        ?>

                                        <span class="badge bg-<?= $statusClass; ?>">
                                            <?= ucwords(strtolower(str_replace('_', ' ', $row['status']))); ?>
                                        </span>

                                    </td>

                                    <td>

                                        <?php if ($row['is_approved']): ?>

                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Approved
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i>
                                                Pending
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="9" class="text-center py-5">

                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>

                                    <h5 class="text-muted">
                                        No attendance records found.
                                    </h5>

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
</div>