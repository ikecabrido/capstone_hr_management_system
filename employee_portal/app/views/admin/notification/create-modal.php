<div class="modal fade" id="createNotificationModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-bell me-2 text-primary"></i>
                    Create Notification
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <form method="POST" action="index.php?url=notification-store">

                <input
                    type="hidden"
                    name="created_by_user_id"
                    value="<?= $_SESSION['user_id']; ?>">

                <div class="modal-body">

                    <div class="row">

                        <!-- LEFT COLUMN -->
                        <div class="col-lg-7">

                            <!-- Title -->
                            <div class="mb-3">

                                <label class="form-label fw-semibold">
                                    Notification Title
                                </label>

                                <input
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    placeholder="Enter notification title"
                                    required>

                            </div>

                            <!-- Message -->
                            <div class="mb-3">

                                <label class="form-label fw-semibold">
                                    Message
                                </label>

                                <textarea
                                    name="message"
                                    rows="6"
                                    class="form-control"
                                    placeholder="Type your notification..."
                                    required></textarea>

                            </div>

                            <div class="row">

                                <!-- Type -->
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Notification Type
                                    </label>

                                    <select
                                        name="type"
                                        class="form-select">

                                        <option value="announcement">Announcement</option>
                                        <option value="payroll">Payroll</option>
                                        <option value="leave">Leave</option>
                                        <option value="training">Training</option>
                                        <option value="performance">Performance</option>
                                        <option value="document">Document</option>
                                        <option value="meeting">Meeting</option>
                                        <option value="compliance">Compliance</option>
                                        <option value="general">General</option>

                                    </select>

                                </div>

                                <!-- Priority -->
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Priority
                                    </label>

                                    <select
                                        name="priority"
                                        class="form-select">

                                        <option value="normal">Normal</option>
                                        <option value="important">Important</option>
                                        <option value="urgent">Urgent</option>

                                    </select>

                                </div>

                            </div>

                        </div>

                        <!-- RIGHT COLUMN -->
                        <div class="col-lg-5">

                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <label class="form-label fw-semibold mb-0">
                                    Send To Employees
                                </label>

                                <div>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        id="selectAllEmployees">

                                        <i class="fas fa-check-square me-1"></i>
                                        Select All

                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        id="clearEmployees">

                                        Clear

                                    </button>

                                </div>

                            </div>

                            <select
                                class="form-select"
                                id="employeeSelect"
                                name="employee_ids[]"
                                multiple
                                size="15">

                                <?php foreach ($employeeList as $employee): ?>

                                    <option value="<?= $employee['employee_id']; ?>">

                                        <?= htmlspecialchars($employee['first_name']); ?>
                                        <?= htmlspecialchars($employee['last_name']); ?>

                                        (<?= htmlspecialchars($employee['employee_code']); ?>)

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <small class="text-muted">
                                Hold <strong>Ctrl</strong> (Windows) or
                                <strong>Cmd</strong> (Mac) to select multiple employees.
                            </small>

                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-paper-plane me-2"></i>

                        Send Notification

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>