<!-- Exit Management Modals -->

<!-- Resignation Modal -->
<div class="modal fade" id="resignationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="resignationModalTitle">Submit Resignation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="resignationForm">
                <div class="modal-body">
                    <input type="hidden" id="resignationId" name="resignation_id">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="employeeSelect">Employee *</label>
                                <select class="form-control" id="employeeSelect" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                </select>
                                <div id="eligibilityMessage" class="mt-2" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="resignationType">Resignation Type *</label>
                                <select class="form-control" id="resignationType" name="resignation_type" required>
                                    <option value="voluntary">Voluntary</option>
                                    <option value="involuntary">Involuntary</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="preclearanceDeskPerson">Pre-clearance Desk Person *</label>
                                <select class="form-control" id="preclearanceDeskPerson" name="preclearance_desk_person" required>
                                    <option value="">Select Desk Person</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reason">Reason *</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="noticeDate">Notice Date *</label>
                                <input type="date" class="form-control" id="noticeDate" name="notice_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastWorkingDate">Last Working Date *</label>
                                <input type="date" class="form-control" id="lastWorkingDate" name="last_working_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="comments">Additional Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="2"></textarea>
                    </div>

                    <!-- Approval section (for admins) -->
                    <div id="approvalSection" style="display: none;">
                        <hr>
                        <h6>Approval</h6>
                        <div class="form-group">
                            <label for="approvalStatus">Status</label>
                            <select class="form-control" id="approvalStatus" name="status">
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="approvalComments">Approval Comments</label>
                            <textarea class="form-control" id="approvalComments" name="approval_comments" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="resignationSubmitBtn">Submit Resignation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Exit Interview Modal -->
<div class="modal fade" id="interviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="interviewModalTitle">Schedule Exit Interview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="interviewForm">
                <div class="modal-body">
                    <input type="hidden" id="interviewId" name="interview_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="interviewEmployeeSelect">Employee *</label>
                                <select class="form-control" id="interviewEmployeeSelect" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="interviewerSelect">Interviewer *</label>
                                <select class="form-control" id="interviewerSelect" name="interviewer_id" required>
                                    <option value="">Select Interviewer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="interviewDate">Interview Date *</label>
                                <input type="date" class="form-control" id="interviewDate" name="scheduled_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="interviewTime">Interview Time</label>
                                <input type="time" class="form-control" id="interviewTime" name="scheduled_time">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="interviewLocation">Location</label>
                        <input type="text" class="form-control" id="interviewLocation" name="location" value="Virtual">
                    </div>

                    <div class="form-group">
                        <label for="interviewNotes">Notes</label>
                        <textarea class="form-control" id="interviewNotes" name="notes" rows="2"></textarea>
                    </div>

                    <!-- Feedback section (for completed interviews) -->
                    <div id="feedbackSection" style="display: none;">
                        <hr>
                        <h6>Interview Feedback</h6>
                        <div class="form-group">
                            <label for="interviewFeedback">Feedback</label>
                            <textarea class="form-control" id="interviewFeedback" name="feedback" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="interviewRating">Overall Rating (1-5)</label>
                            <select class="form-control" id="interviewRating" name="rating">
                                <option value="">Select Rating</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Below Average</option>
                                <option value="3">3 - Average</option>
                                <option value="4">4 - Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="interviewSubmitBtn">Schedule Interview</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Knowledge Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="transferModalTitle">Create Knowledge Transfer Plan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="transferForm">
                <div class="modal-body">
                    <input type="hidden" id="transferPlanId" name="plan_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transferEmployeeSelect">Employee Leaving *</label>
                                <select class="form-control" id="transferEmployeeSelect" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="successorSelect">Successor</label>
                                <select class="form-control" id="successorSelect" name="successor_id">
                                    <option value="">Select Successor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transferStartDate">Start Date *</label>
                                <input type="date" class="form-control" id="transferStartDate" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transferEndDate">End Date *</label>
                                <input type="date" class="form-control" id="transferEndDate" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Items Section -->
                    <div class="form-group">
                        <label>Knowledge Transfer Items</label>
                        <div id="transferItemsContainer">
                            <div class="transfer-item mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select class="form-control" name="items[0][type]" required>
                                            <option value="">Select Type</option>
                                            <option value="process">Process</option>
                                            <option value="system">System</option>
                                            <option value="contact">Contact</option>
                                            <option value="document">Document</option>
                                            <option value="skill">Skill</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="items[0][title]" placeholder="Title" required>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control" name="items[0][priority]">
                                            <option value="medium">Medium</option>
                                            <option value="low">Low</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="items[0][due_date]">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <textarea class="form-control" name="items[0][description]" rows="2" placeholder="Description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addTransferItem">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="transferSubmitBtn">Create Transfer Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Settlement Modal -->
<div class="modal fade" id="settlementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="settlementModalTitle">Calculate Final Settlement</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="settlementForm">
                <div class="modal-body">
                    <input type="hidden" id="settlementId" name="settlement_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="settlementEmployeeSelect">Employee *</label>
                                <select class="form-control" id="settlementEmployeeSelect" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="settlementResignationSelect">Related Resignation</label>
                                <select class="form-control" id="settlementResignationSelect" name="resignation_id">
                                    <option value="">Select Resignation</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="settlementDate">Settlement Date *</label>
                                <input type="date" class="form-control" id="settlementDate" name="settlement_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="paymentDate">Payment Date</label>
                                <input type="date" class="form-control" id="paymentDate" name="payment_date">
                            </div>
                        </div>
                    </div>

                    <!-- Salary Components -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Salary Components</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="basicSalary">Basic Salary *</label>
                                        <input type="number" step="0.01" class="form-control" id="basicSalary" name="basic_salary" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hra">HRA</label>
                                        <input type="number" step="0.01" class="form-control" id="hra" name="hra" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="conveyance">Conveyance</label>
                                        <input type="number" step="0.01" class="form-control" id="conveyance" name="conveyance" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lta">LTA</label>
                                        <input type="number" step="0.01" class="form-control" id="lta" name="lta" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicalAllowance">Medical Allowance</label>
                                        <input type="number" step="0.01" class="form-control" id="medicalAllowance" name="medical_allowance" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="otherAllowances">Other Allowances</label>
                                        <input type="number" step="0.01" class="form-control" id="otherAllowances" name="other_allowances" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title">Deductions</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="providentFund">Provident Fund</label>
                                        <input type="number" step="0.01" class="form-control" id="providentFund" name="provident_fund" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gratuity">Gratuity</label>
                                        <input type="number" step="0.01" class="form-control" id="gratuity" name="gratuity" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="noticePay">Notice Pay</label>
                                        <input type="number" step="0.01" class="form-control" id="noticePay" name="notice_pay" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="outstandingLoans">Outstanding Loans</label>
                                        <input type="number" step="0.01" class="form-control" id="outstandingLoans" name="outstanding_loans" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="otherDeductions">Other Deductions</label>
                                <input type="number" step="0.01" class="form-control" id="otherDeductions" name="other_deductions" value="0">
                            </div>
                        </div>
                    </div>

                    <!-- Net Payable -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="netPayable">Net Payable Amount *</label>
                                        <input type="number" step="0.01" class="form-control" id="netPayable" name="net_payable" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-info btn-block" id="calculateNetPayable">
                                            <i class="fas fa-calculator"></i> Calculate
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="settlementSubmitBtn">Save Settlement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Upload Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="documentModalTitle">Upload Document</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="documentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="documentId" name="document_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="documentEmployeeSelect">Employee *</label>
                                <select class="form-control" id="documentEmployeeSelect" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="documentType">Document Type *</label>
                                <select class="form-control" id="documentType" name="document_type" required>
                                    <option value="">Select Type</option>
                                    <option value="resignation_letter">Resignation Letter</option>
                                    <option value="clearance_form">Clearance Form</option>
                                    <option value="handover_document">Handover Document</option>
                                    <option value="settlement_receipt">Settlement Receipt</option>
                                    <option value="exit_interview">Exit Interview Notes</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="documentTitle">Document Title *</label>
                        <input type="text" class="form-control" id="documentTitle" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="documentFile">File *</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="documentFile" name="document_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            <label class="custom-file-label" for="documentFile">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info" id="documentSubmitBtn">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Survey Modal -->
<div class="modal fade" id="surveyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="surveyModalTitle">
                    <i class="fas fa-poll mr-2"></i>Create Post-Exit Survey
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="surveyForm">
                <div class="modal-body">
                    <input type="hidden" id="surveyId" name="survey_id">

                    <!-- Survey Overview Section -->
                    <div class="card mb-4 border-left-primary">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle text-primary mr-2"></i>Survey Overview</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label for="surveyTitle" class="font-weight-bold">Survey Title <span class="text-danger">*</span></label>
                                <small class="form-text text-muted d-block mb-2">Give your survey a clear, descriptive title</small>
                                <input type="text" class="form-control" id="surveyTitle" name="title" placeholder="e.g., Exit Experience Survey 2026" required>
                            </div>

                            <div class="form-group mt-3 mb-0">
                                <label for="surveyDescription" class="font-weight-bold">Description</label>
                                <small class="form-text text-muted d-block mb-2">Optional: Add context or instructions for respondents</small>
                                <textarea class="form-control" id="surveyDescription" name="description" rows="2" placeholder="e.g., Please help us improve by sharing your feedback..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Survey Timeline Section -->
                    <div class="card mb-4 border-left-info">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-calendar text-info mr-2"></i>Survey Timeline</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="surveyStartDate" class="font-weight-bold">Start Date <span class="text-danger">*</span></label>
                                        <small class="form-text text-muted d-block mb-2">When survey becomes available</small>
                                        <input type="date" class="form-control" id="surveyStartDate" name="start_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="surveyEndDate" class="font-weight-bold">End Date <span class="text-danger">*</span></label>
                                        <small class="form-text text-muted d-block mb-2">When survey closes</small>
                                        <input type="date" class="form-control" id="surveyEndDate" name="end_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Survey Settings Section -->
                    <div class="card mb-4 border-left-success">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-cog text-success mr-2"></i>Survey Settings</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label for="surveyAudience" class="font-weight-bold">Target Audience</label>
                                <small class="form-text text-muted d-block mb-2">Who should receive this survey?</small>
                                <select class="form-control" id="surveyAudience" name="target_audience">
                                    <option value="all">All Ex-Employees</option>
                                    <option value="voluntary">Voluntary Resignations Only</option>
                                    <option value="involuntary">Involuntary Resignations Only</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Survey Questions Section -->
                    <div class="card border-left-warning">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-list-check text-warning mr-2"></i>Survey Questions</h6>
                            <small class="text-muted">Build your survey by adding questions below</small>
                        </div>
                        <div class="card-body">
                            <div id="surveyQuestionsContainer">
                                <!-- First question template -->
                                <div class="question-item card mb-3 shadow-sm border-0">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <span class="font-weight-bold">
                                            <i class="fas fa-question-circle text-primary mr-2"></i>Question 1
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-question" title="Delete question">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <div class="card-body pt-3 pb-2">
                                        <!-- Question Text -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Question Text <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg" name="questions[0][text]" placeholder="Enter your question here..." required>
                                        </div>

                                        <!-- Question Type & Required -->
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-bold">Question Type <span class="text-danger">*</span></label>
                                                    <select class="form-control question-type" name="questions[0][type]" required>
                                                        <option value="">-- Select Type --</option>
                                                        <option value="text">📝 Short Text</option>
                                                        <option value="textarea">📄 Long Text / Paragraph</option>
                                                        <option value="radio">⭕ Multiple Choice (Single Answer)</option>
                                                        <option value="checkbox">☑️ Multiple Choice (Multiple Answers)</option>
                                                        <option value="select">⬇️ Dropdown List</option>
                                                        <option value="rating">⭐ Rating Scale (1-5)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-bold">Question Settings</label>
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="checkbox" id="req0" name="questions[0][required]" checked>
                                                        <label class="form-check-label" for="req0">
                                                            Required <span class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Options Container -->
                                        <div class="options-container mt-3 pt-3 border-top" style="display: none;">
                                            <label class="font-weight-bold">Answer Options <span class="text-danger">*</span></label>
                                            <small class="form-text text-muted d-block mb-2">Enter each option on a new line</small>
                                            <textarea class="form-control" name="questions[0][options]" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Question Button -->
                            <button type="button" class="btn btn-outline-primary btn-block mt-3" id="addSurveyQuestion">
                                <i class="fas fa-plus-circle mr-2"></i>Add Another Question
                            </button>
                        </div>
                    </div>

                    <!-- Help Text -->
                    <div class="alert alert-info alert-sm mt-3" role="alert">
                        <i class="fas fa-lightbulb mr-2"></i>
                        <strong>Tip:</strong> Use rating scales for satisfaction, yes/no multiple choice for quick feedback, and text fields for detailed comments.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="surveySubmitBtn">
                        <i class="fas fa-save mr-2"></i>Create Survey
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Answer Survey Modal -->
<div class="modal fade" id="answerSurveyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="answerSurveyTitle">Answer Survey</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="answerSurveyForm">
                <div class="modal-body">
                    <input type="hidden" id="answerSurveyId" name="survey_id">

                    <!-- Progress Bar -->
                    <div class="progress mb-4" style="height: 8px;">
                        <div class="progress-bar bg-success" id="surveyProgress" style="width: 0%"></div>
                    </div>

                    <div class="survey-info mb-4">
                        <h6 id="answerSurveyDesc" class="text-muted"></h6>
                    </div>

                    <!-- Question Container -->
                    <div id="surveyQuestionContainer" class="text-center">
                        <!-- Current question will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" id="prevQuestionBtn" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <div>
                        <span class="text-muted mr-3" id="questionCounter">Question 1 of 1</span>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="nextQuestionBtn">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="answerSurveySubmitBtn" style="display: none;">
                            <i class="fas fa-paper-plane"></i> Submit Survey
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="confirmationMessage">Are you sure you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmActionBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>