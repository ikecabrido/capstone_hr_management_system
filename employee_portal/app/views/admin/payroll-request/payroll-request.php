<div class="modal fade" id="payrollRequestModal" tabindex="-1" aria-labelledby="payrollRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title font-weight-bold" id="payrollRequestModalLabel">
                        Request Payroll Document
                    </h5>
                    <small class="text-muted">
                        Submit a request for a payroll-related document.
                    </small>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="alert alert-light border mb-4">
                    <small class="text-muted">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        Please provide accurate information. Your request will be reviewed by HR/Payroll.
                    </small>
                </div>

                <form action="index.php?url=payroll-request-store" method="POST" id="payrollRequestForm">

                    <div class="form-group mb-3">
                        <label for="employee_id" class="font-weight-bold">
                            Employee <span class="text-danger">*</span>
                        </label>

                        <select
                            name="employee_id"
                            id="employee_id"
                            class="form-control"
                            required>

                            <option value="">Select employee</option>

                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= htmlspecialchars($employee['id']) ?>">
                                    <?= htmlspecialchars($employee['employee_no']) ?>
                                    -
                                    <?= htmlspecialchars($employee['full_name']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>


                    <div class="form-group mb-3">
                        <label for="request_type" class="font-weight-bold">
                            Payroll Document <span class="text-danger">*</span>
                        </label>

                        <select name="request_type" id="request_type" class="form-control" required>
                            <option value="">Select document</option>
                            <option value="Payslip">Payslip</option>
                            <option value="Certificate of Compensation">Certificate of Compensation</option>
                            <option value="Annual Payroll Summary">Annual Payroll Summary</option>
                            <option value="BIR Form 2316">BIR Form 2316</option>
                            <option value="Certificate of Employment with Compensation">
                                Certificate of Employment with Compensation
                            </option>
                            <option value="Other Payroll Document">Other Payroll Document</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="purpose" class="font-weight-bold">
                            Purpose <span class="text-danger">*</span>
                        </label>

                        <select name="purpose" id="purpose" class="form-control" required>
                            <option value="">Select purpose</option>
                            <option value="Personal record">Personal Record</option>
                            <option value="Loan application">Loan Application</option>
                            <option value="Bank requirement">Bank Requirement</option>
                            <option value="Tax filing">Tax Filing</option>
                            <option value="Visa application">Visa Application</option>
                            <option value="Employment requirement">Employment Requirement</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <?php
                        $periodStart = date('Y-m-d');
                        $periodEnd = date('Y-m-d', strtotime('+3 days'));
                        ?>

                        <div class="form-group col-md-6">
                            <label for="payroll_period_start" class="font-weight-bold">
                                Payroll Period Start
                            </label>

                            <input
                                type="date"
                                name="payroll_period_start"
                                id="payroll_period_start"
                                class="form-control"
                                value="<?= $periodStart ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="payroll_period_end" class="font-weight-bold">
                                Payroll Period End
                            </label>

                            <input
                                type="date"
                                name="payroll_period_end"
                                id="payroll_period_end"
                                class="form-control"
                                value="<?= $periodEnd ?>">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="remarks" class="font-weight-bold">
                            Additional Remarks
                        </label>

                        <textarea
                            name="remarks"
                            id="remarks"
                            class="form-control"
                            rows="3"
                            maxlength="1000"
                            placeholder="Provide any additional information..."></textarea>

                        <small class="form-text text-muted">
                            Optional. Maximum 1,000 characters.
                        </small>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button
                            type="button"
                            class="btn btn-light border mr-2"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i>
                            Submit Request
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>