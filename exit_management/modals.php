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
                            <div class="form-group col-md-4">
                                <label for="hrClearanceRecommendation">Clearance Recommendation</label>
                                <select class="form-control" id="hrClearanceRecommendation" name="hr_clearance_recommendation">
                                    <option value="pending">Pending</option>
                                    <option value="clear">Clear</option>
                                    <option value="not_clear">Not Clear</option>
                                </select>
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

<!-- Answer Survey Modal -->
<div class="modal fade exit-modal" id="answerSurveyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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

                    <div class="form-group">
                        <label for="archiveReason">Archive Reason *</label>
                        <textarea class="form-control" id="archiveReason" name="archive_reason" rows="3" placeholder="Please provide a reason for archiving this resignation..." required></textarea>
                        <small class="form-text text-muted">This reason will be stored with the archived record for future reference.</small>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveSettlementEmployeeId">Employee ID</label>
                                <input type="text" class="form-control" id="archiveSettlementEmployeeId" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archiveSettlementEmployeeName">Employee Name</label>
                                <input type="text" class="form-control" id="archiveSettlementEmployeeName" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="archiveSettlementReason">Archive Reason *</label>
                        <textarea class="form-control" id="archiveSettlementReason" name="archive_reason" rows="3" placeholder="Please provide a reason for archiving this settlement..." required></textarea>
                        <small class="form-text text-muted">This reason will be stored with the archived record for future reference.</small>
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

                    <div class="form-group">
                        <label for="archiveInterviewReason">Archive Reason *</label>
                        <textarea class="form-control" id="archiveInterviewReason" name="archive_reason" rows="3" placeholder="Please provide a reason for archiving this interview..." required></textarea>
                        <small class="form-text text-muted">This reason will be stored with the archived record for future reference.</small>
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

                    <div class="form-group">
                        <label for="archiveDocumentReason">Archive Reason *</label>
                        <textarea class="form-control" id="archiveDocumentReason" name="archive_reason" rows="3" placeholder="Please provide a reason for archiving this document..." required></textarea>
                        <small class="form-text text-muted">This reason will be stored with the archived record for future reference.</small>
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

                    <div class="form-group">
                        <label for="archiveSurveyReason">Archive Reason *</label>
                        <textarea class="form-control" id="archiveSurveyReason" name="archive_reason" rows="3" placeholder="Please provide a reason for archiving this survey..." required></textarea>
                        <small class="form-text text-muted">This reason will be stored with the archived record for future reference.</small>
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

                    <div class="form-group">
                        <label for="archiveTransferPlanReason">Archive Reason *</label>
                        <textarea class="form-control" id="archiveTransferPlanReason" name="archive_reason" rows="3" placeholder="Please provide a reason for archiving this transfer plan..." required></textarea>
                        <small class="form-text text-muted">This reason will be stored with the archived record for future reference.</small>
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

                    <div class="form-group">
                        <label for="archiveTransferItemReason">Archive Reason *</label>
                        <textarea class="form-control" id="archiveTransferItemReason" name="archive_reason" rows="3" placeholder="Please provide a reason for archiving this transfer item..." required></textarea>
                        <small class="form-text text-muted">This reason will be stored with the archived record for future reference.</small>
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