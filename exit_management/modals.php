<!-- Exit Management Modals -->

<!-- Resignation Modal -->
<div class="modal fade exit-modal" id="resignationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employeeSelect">Employee *</label>
                                <select class="form-control" id="employeeSelect" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                </select>
                                <div id="eligibilityMessage" class="mt-2" style="display: none;"></div>
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

                    <div class="form-group" id="resignationLetterSection" style="display: none;">
                        <label>Resignation Letter</label>
                        <div class="form-control-plaintext border rounded px-3 py-2 bg-light">
                            <a id="resignationLetterLink" href="#" target="_blank" rel="noopener" style="display: none;">
                                <i class="fas fa-file-alt mr-1"></i><span id="resignationLetterName">View resignation letter</span>
                            </a>
                            <span id="resignationLetterMissing" class="text-danger" style="display: none;">No resignation letter attached.</span>
                        </div>
                    </div>

                    <!-- Approval section (for admins) -->
                    <div id="approvalSection" style="display: none;">
                        <hr>
                        <h6>Approval</h6>
                        <div class="form-group">
                            <label for="approvalStatus">Status</label>
                            <select class="form-control" id="approvalStatus" name="status">
                                <option value="pending_legal_review">Approve HR review (send to Legal)</option>
                                <option value="approved">Approve final</option>
                                <option value="rejected">Reject</option>
                                <option value="rejected_by_legal">Reject by Legal</option>
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
                    <button type="submit" class="btn btn-primary" id="resignationSubmitBtn">Save Decision</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Termination Modal -->
<div class="modal fade exit-modal" id="terminationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="terminationModalTitle">Initiate Termination</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="terminationForm">
                <div class="modal-body">
                    <input type="hidden" id="terminationId" name="termination_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="terminationEmployeeSelect">Employee *</label>
                                <select class="form-control" id="terminationEmployeeSelect" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                </select>
                                <div id="terminationEligibilityMessage" class="mt-2" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="terminationEffectiveDate">Effective Date *</label>
                                <input type="date" class="form-control" id="terminationEffectiveDate" name="effective_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="terminationReason">Termination Reason *</label>
                        <textarea class="form-control" id="terminationReason" name="termination_reason" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="terminationComments">Additional Comments</label>
                        <textarea class="form-control" id="terminationComments" name="comments" rows="2"></textarea>
                    </div>

                    <div id="terminationApprovalSection" style="display: none;">
                        <hr>
                        <h6>Approval</h6>
                        <div class="form-group">
                            <label for="terminationApprovalStatus">Status</label>
                            <select class="form-control" id="terminationApprovalStatus" name="status">
                                <option value="pending_legal_review">Send to Legal Review</option>
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                                <option value="rejected_by_legal">Reject by Legal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="terminationApprovalComments">Approval Comments</label>
                            <textarea class="form-control" id="terminationApprovalComments" name="approval_comments" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="terminationSubmitBtn">Submit Termination</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Exit Interview Modal -->
<div class="modal fade exit-modal" id="interviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
                                <label for="interviewCaseSelect">Approved Exit Case *</label>
                                <select class="form-control" id="interviewCaseSelect" required>
                                    <option value="">Select Approved Exit Case</option>
                                </select>
                                <div id="interviewCaseDisplay" class="form-control-plaintext" style="display:none; white-space: normal; word-break: break-word;"></div>
                                <small id="interviewCaseHelpText" class="form-text text-danger" style="display:none;">Please select an approved exit case before scheduling the interview.</small>
                                <input type="hidden" id="interviewExitCaseType" name="exit_case_type" />
                                <input type="hidden" id="interviewExitCaseId" name="exit_case_id" />
                                <input type="hidden" id="interviewEmployeeId" name="employee_id" />
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
                                <label>Interview Time *</label>
                                <div class="row">
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="interviewHour" min="1" max="12" placeholder="HH">
                                    </div>
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="interviewMinute" min="0" max="59" step="5" placeholder="MM">
                                    </div>
                                    <div class="col-4">
                                        <select class="form-control" id="interviewMeridiem">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" id="interviewTime" name="scheduled_time">
                                <small class="form-text text-muted">Enter the interview time in separate fields.</small>
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
                            <label for="interviewOverallSatisfaction">Overall Satisfaction</label>
                            <select class="form-control" id="interviewOverallSatisfaction" name="overall_satisfaction">
                                <option value="">Select Rating</option>
                                <option value="1">1 - Very Dissatisfied</option>
                                <option value="2">2 - Dissatisfied</option>
                                <option value="3">3 - Neutral</option>
                                <option value="4">4 - Satisfied</option>
                                <option value="5">5 - Very Satisfied</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="interviewWorkEnvironmentRating">Work Environment Rating</label>
                                <select class="form-control" id="interviewWorkEnvironmentRating" name="work_environment_rating">
                                    <option value="">Select Rating</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="interviewManagementRating">Management Rating</label>
                                <select class="form-control" id="interviewManagementRating" name="management_rating">
                                    <option value="">Select Rating</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="interviewCompensationRating">Compensation Rating</label>
                                <select class="form-control" id="interviewCompensationRating" name="compensation_rating">
                                    <option value="">Select Rating</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="interviewWorkLifeBalanceRating">Work-Life Balance Rating</label>
                                <select class="form-control" id="interviewWorkLifeBalanceRating" name="work_life_balance_rating">
                                    <option value="">Select Rating</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="interviewReasonForLeaving">Reason for Leaving</label>
                            <textarea class="form-control" id="interviewReasonForLeaving" name="reason_for_leaving" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="interviewWouldRecommend">Would Recommend Working Here?</label>
                            <select class="form-control" id="interviewWouldRecommend" name="would_recommend">
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="interviewAdditionalComments">Additional Comments</label>
                            <textarea class="form-control" id="interviewAdditionalComments" name="additional_comments" rows="3"></textarea>
                        </div>
                    </div>
                    <!-- Employee Info (read-only) -->
                    <div id="employeeInfoSection" class="mt-3" style="display: none;">
                        <hr>
                        <h6>Employee Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Employee</label>
                                    <div id="employeeFullName" class="form-control-plaintext"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Department</label>
                                    <div id="employeeDepartment" class="form-control-plaintext"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Position</label>
                                    <div id="employeePosition" class="form-control-plaintext"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date Hired</label>
                                    <div id="employeeDateHired" class="form-control-plaintext"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Years of Service</label>
                                    <div id="employeeYearsOfService" class="form-control-plaintext"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Manager</label>
                                    <div id="employeeManager" class="form-control-plaintext"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exit Case Info (read-only) -->
                    <div id="exitCaseInfoSection" class="mt-3" style="display: none;">
                        <hr>
                        <h6>Exit Case Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Exit Reason</label>
                                    <div id="exitCaseReason" class="form-control-plaintext"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Notice Date</label>
                                    <div id="exitCaseNoticeDate" class="form-control-plaintext"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Working / Effective Date</label>
                                    <div id="exitCaseDate" class="form-control-plaintext"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Case Approved By / At</label>
                                    <div id="exitCaseApproved" class="form-control-plaintext"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Engagement placeholder -->
                    <div id="engagementSection" class="mt-3" style="display: none;">
                        <hr>
                        <h6>Employee Engagement</h6>
                        <div class="form-group">
                            <div id="engagementPlaceholder" class="text-muted">Survey integration placeholder (post-exit surveys will appear here).</div>
                        </div>
                    </div>

                    <!-- HR Assessment (admin editable) -->
                    <div id="hrAssessmentSection" class="mt-3" style="display: none;">
                        <hr>
                        <h6>HR Assessment</h6>
                        <div class="form-group">
                            <label for="hrSummary">Interview Summary</label>
                            <textarea class="form-control" id="hrSummary" name="hr_summary" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="hrKeyFindings">Key Findings</label>
                            <textarea class="form-control" id="hrKeyFindings" name="hr_key_findings" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="hrRecommendations">HR Recommendations</label>
                            <textarea class="form-control" id="hrRecommendations" name="hr_recommendations" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="hrFollowUpActions">Follow-up Actions</label>
                            <textarea class="form-control" id="hrFollowUpActions" name="hr_follow_up_actions" rows="2"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="hrRehireEligibility">Rehire Eligibility</label>
                                <select class="form-control" id="hrRehireEligibility" name="hr_rehire_eligibility">
                                    <option value="">Select</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                    <option value="conditional">Conditional</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="hrKnowledgeTransfer">Knowledge Transfer Required</label>
                                <div class="form-control-plaintext"><input type="checkbox" id="hrKnowledgeTransfer" name="hr_knowledge_transfer"> Yes</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info" id="editInterviewBtn" style="display:none;">Edit Interview</button>
                    <button type="button" class="btn btn-primary" id="saveHrAssessmentBtn" style="display:none;">Save HR Assessment</button>
                    <button type="submit" class="btn btn-success" id="interviewSubmitBtn">Schedule Interview</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Knowledge Transfer Modal -->
<div class="modal fade exit-modal" id="transferModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
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
                                            <option value="other">Other</option>
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
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12 mb-2">
                                        <textarea class="form-control" name="items[0][description]" rows="2" placeholder="Description"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <textarea class="form-control" name="items[0][notes]" rows="2" placeholder="Notes"></textarea>
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
                    <button type="button" class="btn btn-info" id="editTransferBtn" style="display:none;">Edit Transfer Plan</button>
                    <button type="submit" class="btn btn-warning" id="transferSubmitBtn">Create Transfer Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Settlement Modal -->
<div class="modal fade exit-modal" id="settlementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="settlementModalTitle">Request Settlement</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="settlementForm">
                <div class="modal-body">
                    <input type="hidden" id="settlementId" name="settlement_id">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="settlementCaseSelect">Approved Exit Case *</label>
                                <select class="form-control" id="settlementCaseSelect" required>
                                    <option value="">Select Approved Exit Case</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="settlementEmployeeId" name="employee_id" value="">
                    <input type="hidden" id="settlementExitCaseType" name="exit_case_type" value="">
                    <input type="hidden" id="settlementExitCaseId" name="exit_case_id" value="">
                    <input type="hidden" id="settlementResignationId" name="resignation_id" value="">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="settlementDate">Settlement Date *</label>
                                <input type="date" class="form-control" id="settlementDate" name="settlement_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                This form submits a settlement request. Payroll will perform all financial calculations and determine the final net payable amount.
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="settlementStatus" name="status" value="pending_approval">
                    <input type="hidden" id="netPayable" name="net_payable" value="0">
                    <input type="hidden" id="basicSalary" name="basic_salary" value="0">
                    <input type="hidden" id="remainingSalary" name="remaining_salary" value="0">
                    <input type="hidden" id="unusedLeaveConversion" name="unused_leave_conversion" value="0">
                    <input type="hidden" id="overtimePay" name="overtime_pay" value="0">
                    <input type="hidden" id="holidayPay" name="holiday_pay" value="0">
                    <input type="hidden" id="bonuses" name="bonuses" value="0">
                    <input type="hidden" id="commission" name="commission" value="0">
                    <input type="hidden" id="hra" name="hra" value="0">
                    <input type="hidden" id="conveyance" name="conveyance" value="0">
                    <input type="hidden" id="lta" name="lta" value="0">
                    <input type="hidden" id="medicalAllowance" name="medical_allowance" value="0">
                    <input type="hidden" id="otherAllowances" name="other_allowances" value="0">
                    <input type="hidden" id="separationPay" name="separation_pay" value="0">
                    <input type="hidden" id="tax" name="tax" value="0">
                    <input type="hidden" id="sss" name="sss" value="0">
                    <input type="hidden" id="philhealth" name="philhealth" value="0">
                    <input type="hidden" id="pagibig" name="pagibig" value="0">
                    <input type="hidden" id="cashAdvance" name="cash_advance" value="0">
                    <input type="hidden" id="companyLoan" name="company_loan" value="0">
                    <input type="hidden" id="equipmentDamage" name="equipment_damage" value="0">
                    <input type="hidden" id="missingAssets" name="missing_assets" value="0">
                    <input type="hidden" id="lateDeductions" name="late_deductions" value="0">
                    <input type="hidden" id="absenceDeductions" name="absence_deductions" value="0">
                    <input type="hidden" id="providentFund" name="provident_fund" value="0">
                    <input type="hidden" id="gratuity" name="gratuity" value="0">
                    <input type="hidden" id="noticePay" name="notice_pay" value="0">
                    <input type="hidden" id="outstandingLoans" name="outstanding_loans" value="0">
                    <input type="hidden" id="otherDeductions" name="other_deductions" value="0">

                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Request Details</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">HR will only submit the employee, related resignation, and settlement date. Payroll will review the request, calculate the settlement components, and set the final net amount.</p>
                        </div>
                    </div>

                    <div class="card mt-3 d-none" id="payrollSettlementSummaryCard">
                        <div class="card-header">
                            <h6 class="card-title">Payroll Settlement Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        <tr><th>Net Payable</th><td id="payrollSettlementSummary_net_payable">0.00</td></tr>
                                        <tr><th>Basic Salary</th><td id="payrollSettlementSummary_basic_salary">0.00</td></tr>
                                        <tr><th>Remaining Salary</th><td id="payrollSettlementSummary_remaining_salary">0.00</td></tr>
                                        <tr><th>Unused Leave Conversion</th><td id="payrollSettlementSummary_unused_leave_conversion">0.00</td></tr>
                                        <tr><th>Overtime Pay</th><td id="payrollSettlementSummary_overtime_pay">0.00</td></tr>
                                        <tr><th>Holiday Pay</th><td id="payrollSettlementSummary_holiday_pay">0.00</td></tr>
                                        <tr><th>Bonuses</th><td id="payrollSettlementSummary_bonuses">0.00</td></tr>
                                        <tr><th>Commission</th><td id="payrollSettlementSummary_commission">0.00</td></tr>
                                        <tr><th>HRA</th><td id="payrollSettlementSummary_hra">0.00</td></tr>
                                        <tr><th>Conveyance</th><td id="payrollSettlementSummary_conveyance">0.00</td></tr>
                                        <tr><th>LTA</th><td id="payrollSettlementSummary_lta">0.00</td></tr>
                                        <tr><th>Medical Allowance</th><td id="payrollSettlementSummary_medical_allowance">0.00</td></tr>
                                        <tr><th>Other Allowances</th><td id="payrollSettlementSummary_other_allowances">0.00</td></tr>
                                        <tr><th>Separation Pay</th><td id="payrollSettlementSummary_separation_pay">0.00</td></tr>
                                        <tr><th>Tax</th><td id="payrollSettlementSummary_tax">0.00</td></tr>
                                        <tr><th>SSS</th><td id="payrollSettlementSummary_sss">0.00</td></tr>
                                        <tr><th>PhilHealth</th><td id="payrollSettlementSummary_philhealth">0.00</td></tr>
                                        <tr><th>Pag-IBIG</th><td id="payrollSettlementSummary_pagibig">0.00</td></tr>
                                        <tr><th>Cash Advance</th><td id="payrollSettlementSummary_cash_advance">0.00</td></tr>
                                        <tr><th>Company Loan</th><td id="payrollSettlementSummary_company_loan">0.00</td></tr>
                                        <tr><th>Equipment Damage</th><td id="payrollSettlementSummary_equipment_damage">0.00</td></tr>
                                        <tr><th>Missing Assets</th><td id="payrollSettlementSummary_missing_assets">0.00</td></tr>
                                        <tr><th>Late Deductions</th><td id="payrollSettlementSummary_late_deductions">0.00</td></tr>
                                        <tr><th>Absence Deductions</th><td id="payrollSettlementSummary_absence_deductions">0.00</td></tr>
                                        <tr><th>Provident Fund</th><td id="payrollSettlementSummary_provident_fund">0.00</td></tr>
                                        <tr><th>Gratuity</th><td id="payrollSettlementSummary_gratuity">0.00</td></tr>
                                        <tr><th>Notice Pay</th><td id="payrollSettlementSummary_notice_pay">0.00</td></tr>
                                        <tr><th>Outstanding Loans</th><td id="payrollSettlementSummary_outstanding_loans">0.00</td></tr>
                                        <tr><th>Other Deductions</th><td id="payrollSettlementSummary_other_deductions">0.00</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="alert alert-warning mb-0">
                                <strong>Payroll owns settlement calculation:</strong> No payroll financial fields are editable in this form.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info" id="settlementEditBtn" style="display:none;">Edit Settlement</button>
                    <button type="submit" class="btn btn-danger" id="settlementSubmitBtn">Save Settlement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Upload Modal -->
<div class="modal fade exit-modal" id="documentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
                    <input type="hidden" id="documentExitCaseType" name="exit_case_type">
                    <input type="hidden" id="documentExitCaseId" name="exit_case_id">

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
                                    <option value="certificate">Experience Certificate</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="documentCaseSelect">Link to Exit Case</label>
                        <select class="form-control" id="documentCaseSelect" name="document_case_select">
                            <option value="">No exit case linked</option>
                        </select>
                        <small class="form-text text-muted">Optional: link the document to a resignation or termination case.</small>
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
<div class="modal fade exit-modal" id="surveyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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

<!-- Record Feedback Modal -->
<div class="modal fade exit-modal" id="answerSurveyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="answerSurveyTitle">Record Post-Exit Feedback</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="answerSurveyForm">
                <div class="modal-body">
                    <input type="hidden" id="answerSurveyId" name="survey_id">
                    <input type="hidden" id="answerSurveyEmployeeId" name="employee_id">
                    <input type="hidden" id="answerSurveyExitCaseType" name="exit_case_type">
                    <input type="hidden" id="answerSurveyExitCaseId" name="exit_case_id">

                    <div class="form-group">
                        <label for="answerSurveyCaseSelect">Approved Exit Case *</label>
                        <select class="form-control" id="answerSurveyCaseSelect" required>
                            <option value="">Select Approved Exit Case</option>
                        </select>
                        <small class="form-text text-muted">Select the exit case associated with this survey response.</small>
                    </div>

                    <div class="card mb-3 border-left-info">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-clock text-info mr-2"></i>Survey Schedule</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="surveyType">Survey Type *</label>
                                        <select class="form-control" id="surveyType" name="survey_type" required>
                                            <option value="">Select Survey Type</option>
                                            <option value="post_exit_feedback">Post-Exit Feedback</option>
                                            <option value="exit_interview_summary">Exit Interview Summary</option>
                                            <option value="clearance_survey">Clearance Survey</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <small class="form-text text-muted">Choose the type of survey being recorded.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="surveyScheduledDate">Survey Date *</label>
                                        <input type="date" class="form-control" id="surveyScheduledDate" name="scheduled_date" required>
                                        <small class="form-text text-muted">When the survey was administered or scheduled.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="surveyScheduledTime">Survey Time *</label>
                                        <input type="time" class="form-control" id="surveyScheduledTime" name="scheduled_time" required>
                                        <small class="form-text text-muted">Time the survey session was recorded.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
<div class="modal fade exit-modal" id="confirmationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
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

<!-- Archived Interviews Modal -->
<div class="modal fade exit-modal" id="archivedInterviewsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title">Archived Exit Interviews</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Interviewer</th>
                                <th>Scheduled Date</th>
                                <th>Status</th>
                                <th>Archived At</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="archived-interviews-tbody">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Loading archived interviews...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="archived-interviews-pagination" class="mt-2 d-flex justify-content-end"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Archived Transfer Plans Modal -->
<div class="modal fade exit-modal" id="archivedTransfersModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title">Archived Transfer Plans</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Archived At</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="archived-transfers-tbody">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Loading archived transfers...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="archived-transfers-pagination" class="mt-2 d-flex justify-content-end"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Archive Resignation Modal -->
<div class="modal fade exit-modal" id="archiveResignationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Archive Resignation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="archiveResignationForm">
                <div class="modal-body">
                    <input type="hidden" id="archiveResignationId" name="resignation_id">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Archiving will move this resignation record to the archive database.
                        The record will be completely removed from active resignations and stored in the exit_archive table.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveEmployeeId">Employee ID</label>
                                <input type="text" class="form-control" id="archiveEmployeeId" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveEmployeeName">Employee Name</label>
                                <input type="text" class="form-control" id="archiveEmployeeName" readonly>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="archiveReason" name="archive_reason" value="Process completed; archived.">
                    <div class="form-group">
                        <label>Archive Reason</label>
                        <div class="form-control-plaintext">Process completed; archived.</div>
                        <small class="form-text text-muted">This reason is generated automatically when the process completes.</small>
                    </div>

                    <div class="form-group">
                        <label for="archiveNotes">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="archiveNotes" name="archive_notes" rows="2" placeholder="Any additional notes about this archive action..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-archive"></i> Archive Resignation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
<!-- Archived Resignations Modal -->
<div class="modal fade exit-modal" id="archivedResignationsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Archived Resignations</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped table-sm">
                    <colgroup>
                        <col style="width: 15%;">
                        <col style="width: 8%;">
                        <col style="width: 14%;">
                        <col style="width: 10%;">
                        <col style="width: 11%;">
                        <col style="width: 8%;">
                        <col style="width: 10%;">
                        <col style="width: 8%;">
                        <col style="width: 10%;">
                        <col style="width: 6%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>Reason</th>
                            <th>Notice Date</th>
                            <th>Last Working Date</th>
                            <th>Comments</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="modal-archived-resignations-tbody">
                        <tr><td colspan="10" class="text-center text-muted">Loading archived resignations...</td></tr>
                    </tbody>
                </table>
                <div id="modal-archived-resignations-pagination" class="mt-2 d-flex justify-content-end"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Archived Settlements Modal -->
<div class="modal fade exit-modal" id="archivedSettlementsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Archived Settlements</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Settlement Date</th>
                            <th>Net Payable</th>
                            <th>Status</th>
                            <th>Archived At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="modal-archived-settlements-tbody">
                        <tr><td colspan="6" class="text-center text-muted">Loading archived settlements...</td></tr>
                    </tbody>
                </table>
                <div id="modal-archived-settlements-pagination" class="mt-2 d-flex justify-content-end"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Archived Settlement Details Modal -->
<div class="modal fade exit-modal" id="viewArchivedSettlementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">Archived Settlement Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewArchivedSettlementBody">
                <p class="text-muted">Select an archived settlement to view its details.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Archive Settlement Modal -->
<div class="modal fade exit-modal" id="archiveSettlementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Archive Settlement</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="archiveSettlementForm">
                <div class="modal-body">
                    <input type="hidden" id="archiveSettlementId" name="settlement_id">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Archiving will move this settlement record to the archive database.
                        The record will be completely removed from active settlements and stored in the exit_archive table.
                    </div>

                    <div class="form-group">
                        <label for="archiveSettlementEmployeeName">Employee Name</label>
                        <input type="text" class="form-control" id="archiveSettlementEmployeeName" readonly>
                    </div>

                    <input type="hidden" id="archiveSettlementReason" name="archive_reason" value="Process completed; archived.">
                    <div class="form-group">
                        <label>Archive Reason</label>
                        <div class="form-control-plaintext">Process completed; archived.</div>
                        <small class="form-text text-muted">This reason is generated automatically when the process completes.</small>
                    </div>

                    <div class="form-group">
                        <label for="archiveSettlementNotes">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="archiveSettlementNotes" name="archive_notes" rows="2" placeholder="Any additional notes about this archive action..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-archive"></i> Archive Settlement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Archive Interview Modal -->
<div class="modal fade exit-modal" id="archiveInterviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Archive Interview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="archiveInterviewForm">
                <div class="modal-body">
                    <input type="hidden" id="archiveInterviewId" name="interview_id">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Archiving will move this interview record to the archive database.
                        The record will be completely removed from active interviews and stored in the exit_archive table.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveInterviewEmployeeId">Employee ID</label>
                                <input type="text" class="form-control" id="archiveInterviewEmployeeId" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveInterviewEmployeeName">Employee Name</label>
                                <input type="text" class="form-control" id="archiveInterviewEmployeeName" readonly>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="archiveInterviewReason" name="archive_reason" value="Process completed; archived.">
                    <div class="form-group">
                        <label>Archive Reason</label>
                        <div class="form-control-plaintext">Process completed; archived.</div>
                        <small class="form-text text-muted">This reason is generated automatically when the process completes.</small>
                    </div>

                    <div class="form-group">
                        <label for="archiveInterviewNotes">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="archiveInterviewNotes" name="archive_notes" rows="2" placeholder="Any additional notes about this archive action..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-archive"></i> Archive Interview
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Archive Termination Modal -->
<div class="modal fade exit-modal" id="archiveTerminationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Archive Termination</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="archiveTerminationForm">
                <div class="modal-body">
                    <input type="hidden" id="archiveTerminationId" name="termination_id">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Archiving will move this termination record to the archive database.
                        The record will be completely removed from active terminations and stored in the exit_archive table.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveTerminationEmployeeId">Employee ID</label>
                                <input type="text" class="form-control" id="archiveTerminationEmployeeId" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveTerminationEmployeeName">Employee Name</label>
                                <input type="text" class="form-control" id="archiveTerminationEmployeeName" readonly>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="archiveTerminationReason" name="archive_reason" value="Process completed; archived.">
                    <div class="form-group">
                        <label>Archive Reason</label>
                        <div class="form-control-plaintext">Process completed; archived.</div>
                        <small class="form-text text-muted">This reason is generated automatically when the process completes.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-archive"></i> Archive Termination
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Archived Terminations List Modal -->
<div class="modal fade exit-modal" id="archivedTerminationsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">Archived Terminations</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="modal-archived-terminations-table" class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Position</th>
                                <th>Reason</th>
                                <th>Effective Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="modal-archived-terminations-tbody">
                            <tr><td colspan="7" class="text-center text-muted">Loading archived terminations...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="modal-archived-terminations-pagination" class="mt-2 d-flex justify-content-end"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Archive Document Modal -->
<div class="modal fade exit-modal" id="archiveDocumentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Archive Document</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="archiveDocumentForm">
                <div class="modal-body">
                    <input type="hidden" id="archiveDocumentId" name="document_id">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Archiving will move this document record to the archive database.
                        The record will be completely removed from active documents and stored in the exit_archive table.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveDocumentEmployeeId">Employee ID</label>
                                <input type="text" class="form-control" id="archiveDocumentEmployeeId" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveDocumentEmployeeName">Employee Name</label>
                                <input type="text" class="form-control" id="archiveDocumentEmployeeName" readonly>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="archiveDocumentReason" name="archive_reason" value="Process completed; archived.">
                    <div class="form-group">
                        <label>Archive Reason</label>
                        <div class="form-control-plaintext">Process completed; archived.</div>
                        <small class="form-text text-muted">This reason is generated automatically when the process completes.</small>
                    </div>

                    <div class="form-group">
                        <label for="archiveDocumentNotes">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="archiveDocumentNotes" name="archive_notes" rows="2" placeholder="Any additional notes about this archive action..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-archive"></i> Archive Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Archive Survey Modal -->
<div class="modal fade exit-modal" id="archiveSurveyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Archive Survey</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="archiveSurveyForm">
                <div class="modal-body">
                    <input type="hidden" id="archiveSurveyId" name="survey_id">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Archiving will move this survey record to the archive database.
                        The record will be completely removed from active surveys and stored in the exit_archive table.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveSurveyEmployeeId">Employee ID</label>
                                <input type="text" class="form-control" id="archiveSurveyEmployeeId" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveSurveyEmployeeName">Employee Name</label>
                                <input type="text" class="form-control" id="archiveSurveyEmployeeName" readonly>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="archiveSurveyReason" name="archive_reason" value="Process completed; archived.">
                    <div class="form-group">
                        <label>Archive Reason</label>
                        <div class="form-control-plaintext">Process completed; archived.</div>
                        <small class="form-text text-muted">This reason is generated automatically when the process completes.</small>
                    </div>

                    <div class="form-group">
                        <label for="archiveSurveyNotes">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="archiveSurveyNotes" name="archive_notes" rows="2" placeholder="Any additional notes about this archive action..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-archive"></i> Archive Survey
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Archive Transfer Plan Modal -->
<div class="modal fade exit-modal" id="archiveTransferPlanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Archive Transfer Plan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="archiveTransferPlanForm">
                <div class="modal-body">
                    <input type="hidden" id="archiveTransferPlanId" name="plan_id">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Archiving will move this transfer plan record to the archive database.
                        The record will be completely removed from active transfer plans and stored in the exit_archive table.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveTransferPlanEmployeeId">Employee ID</label>
                                <input type="text" class="form-control" id="archiveTransferPlanEmployeeId" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveTransferPlanEmployeeName">Employee Name</label>
                                <input type="text" class="form-control" id="archiveTransferPlanEmployeeName" readonly>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="archiveTransferPlanReason" name="archive_reason" value="Process completed; archived.">
                    <div class="form-group">
                        <label>Archive Reason</label>
                        <div class="form-control-plaintext">Process completed; archived.</div>
                        <small class="form-text text-muted">This reason is generated automatically when the process completes.</small>
                    </div>

                    <div class="form-group">
                        <label for="archiveTransferPlanNotes">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="archiveTransferPlanNotes" name="archive_notes" rows="2" placeholder="Any additional notes about this archive action..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-archive"></i> Archive Transfer Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Archive Transfer Item Modal -->
<div class="modal fade exit-modal" id="archiveTransferItemModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Archive Transfer Item</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="archiveTransferItemForm">
                <div class="modal-body">
                    <input type="hidden" id="archiveTransferItemId" name="item_id">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Archiving will move this transfer item record to the archive database.
                        The record will be completely removed from active transfer items and stored in the exit_archive table.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveTransferItemEmployeeId">Employee ID</label>
                                <input type="text" class="form-control" id="archiveTransferItemEmployeeId" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveTransferItemEmployeeName">Employee Name</label>
                                <input type="text" class="form-control" id="archiveTransferItemEmployeeName" readonly>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="archiveTransferItemReason" name="archive_reason" value="Process completed; archived.">
                    <div class="form-group">
                        <label>Archive Reason</label>
                        <div class="form-control-plaintext">Process completed; archived.</div>
                        <small class="form-text text-muted">This reason is generated automatically when the process completes.</small>
                    </div>

                    <div class="form-group">
                        <label for="archiveTransferItemNotes">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="archiveTransferItemNotes" name="archive_notes" rows="2" placeholder="Any additional notes about this archive action..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-archive"></i> Archive Transfer Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>