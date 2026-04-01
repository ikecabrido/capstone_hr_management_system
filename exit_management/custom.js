// Exit Management JavaScript
$(document).ready(function() {
    // Initialize all modal functions
    initializeModals();
    loadEmployees();
    loadDashboardData();
});

// Initialize modal event handlers
function initializeModals() {
    // Resignation Modal
    $('#resignationForm').on('submit', function(e) {
        e.preventDefault();
        submitResignationForm();
    });

    // Interview Modal
    $('#interviewForm').on('submit', function(e) {
        e.preventDefault();
        submitInterviewForm();
    });

    // Transfer Modal
    $('#transferForm').on('submit', function(e) {
        e.preventDefault();
        submitTransferForm();
    });

    // Settlement Modal
    $('#settlementForm').on('submit', function(e) {
        e.preventDefault();
        submitSettlementForm();
    });

    $('#calculateNetPayable').on('click', calculateSettlement);

    // Document Modal
    $('#documentForm').on('submit', function(e) {
        e.preventDefault();
        submitDocumentForm();
    });

    $('#documentFile').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Survey Modal
    $('#surveyForm').on('submit', function(e) {
        e.preventDefault();
        submitSurveyForm();
    });

    // Dynamic form elements
    $('#addTransferItem').on('click', addTransferItem);
    $('#addSurveyQuestion').on('click', addSurveyQuestion);

    // Question type change handler
    $(document).on('change', '.question-type', function() {
        toggleQuestionOptions($(this));
    });

    // Remove buttons
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.transfer-item').remove();
    });

    $(document).on('click', '.remove-question', function() {
        $(this).closest('.question-item').remove();
        normalizeQuestionIndexes();
    });

    // Add option button inside question
    $(document).on('click', '.add-option', function() {
        const questionIndex = $(this).data('question-index');
        addOptionInput(questionIndex);
    });

    // Remove a single option row
    $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-row').remove();
    });

    // Employee eligibility check on selection change
    $('#employeeSelect').on('change', function() {
        const employeeId = $(this).val();
        if (employeeId) {
            checkEmployeeEligibility(employeeId);
        } else {
            $('#eligibilityMessage').hide();
        }
    });
}

// Check employee eligibility for resignation
function checkEmployeeEligibility(employeeId) {
    $.post('exit_management.php', {
        ajax_action: 'check_eligibility',
        controller: 'resignation',
        employee_id: employeeId
    }, function(response) {
        const messageDiv = $('#eligibilityMessage');
        messageDiv.removeClass('alert alert-success alert-danger').empty();

        if (response.success) {
            messageDiv.addClass('alert alert-success').html('<i class="fas fa-check-circle"></i> ' + response.message);
            $('#resignationSubmitBtn').prop('disabled', false);
        } else {
            messageDiv.addClass('alert alert-danger').html('<i class="fas fa-exclamation-triangle"></i> ' + response.message);
            $('#resignationSubmitBtn').prop('disabled', true);
        }
        messageDiv.show();
    }, 'json').fail(function(err) {
        console.error('Error checking eligibility:', err);
        $('#eligibilityMessage').removeClass('alert alert-success alert-danger')
            .addClass('alert alert-warning')
            .html('<i class="fas fa-exclamation-triangle"></i> Unable to check eligibility. Please try again.')
            .show();
        $('#resignationSubmitBtn').prop('disabled', false);
    });
}

// Load employees for dropdowns
function loadEmployees() {
    $.post('exit_management.php', {
        ajax_action: 'get_eligible_employees'
    }, function(response) {
        console.log('Employee response:', response);
        if (response && Array.isArray(response) && response.length > 0) {
            const employeeOptions = '<option value="">Select Employee</option>' +
                response.map(emp => `<option value="${emp.id}">${emp.full_name} (${emp.username})</option>`).join('');

            $('#employeeSelect, #interviewEmployeeSelect, #documentEmployeeSelect').html(employeeOptions);
        } else {
            console.warn('No employees returned or not an array:', response);
        }
    }, 'json').fail(function(err) {
        console.error('Error loading employees:', err);
    });

    loadPreclearanceDeskPersons();
}

function loadPreclearanceDeskPersons() {
    $.post('exit_management.php', {
        ajax_action: 'get_eligible_interviewers'
    }, function(response) {
        if (response && Array.isArray(response) && response.length > 0) {
            const deskOptions = '<option value="">Select Desk Person</option>' +
                response.map(user => `<option value="${user.id}">${user.full_name} (${user.username})</option>`).join('');

            $('#preclearanceDeskPerson').html(deskOptions);
        } else {
            $('#preclearanceDeskPerson').html('<option value="">No desk persons found</option>');
        }
    }, 'json').fail(function(err) {
        console.error('Error loading preclearance desk persons:', err);
        $('#preclearanceDeskPerson').html('<option value="">Error loading users</option>');
    });
}

// Load employees with resignations for exit interview modal
function loadEmployeesWithResignations() {
    $.post('exit_management.php', {
        ajax_action: 'get_employees_with_resignations'
    }, function(response) {
        if (response && response.length > 0) {
            const employeeOptions = '<option value="">Select Employee</option>' +
                response.map(emp => `<option value="${emp.id}">${emp.full_name} (${emp.username})</option>`).join('');

            $('#interviewEmployeeSelect').html(employeeOptions);
        } else {
            $('#interviewEmployeeSelect').html('<option value="">No employees with resignations found</option>');
        }
    }).fail(function(err) {
        console.error('Error loading resigning employees:', err);
        $('#interviewEmployeeSelect').html('<option value="">Error loading employees</option>');
    });
}

// Load interviewers for interview modal
function loadInterviewers() {
    $.post('exit_management.php', {
        ajax_action: 'get_eligible_interviewers'
    }, function(response) {
        if (response && response.length > 0) {
            const interviewerOptions = '<option value="">Select Interviewer</option>' +
                response.map(emp => `<option value="${emp.id}">${emp.full_name} (${emp.role})</option>`).join('');

            $('#interviewerSelect').html(interviewerOptions);
        } else {
            $('#interviewerSelect').html('<option value="">No interviewers available</option>');
        }
    }).fail(function(err) {
        console.error('Error loading interviewers:', err);
        $('#interviewerSelect').html('<option value="">Error loading interviewers</option>');
    });
}

// Load employees with resignations for transfer modal (employee leaving)
function loadEmployeesWithResignationsForTransfers() {
    $.post('exit_management.php', {
        ajax_action: 'get_employees_with_resignations'
    }, function(response) {
        if (response && response.length > 0) {
            const employeeOptions = '<option value="">Select Employee Leaving</option>' +
                response.map(emp => `<option value="${emp.id}">${emp.full_name} (${emp.username})</option>`).join('');

            $('#transferEmployeeSelect').html(employeeOptions);
        } else {
            $('#transferEmployeeSelect').html('<option value="">No employees with resignations found</option>');
        }
    }).fail(function(err) {
        console.error('Error loading resigning employees for transfers:', err);
        $('#transferEmployeeSelect').html('<option value="">Error loading employees</option>');
    });
}

// Load successors for transfer modal
function loadSuccessors() {
    $.post('exit_management.php', {
        ajax_action: 'get_eligible_employees'
    }, function(response) {
        if (response && response.length > 0) {
            const successorOptions = '<option value="">Select Successor</option>' +
                response.map(emp => `<option value="${emp.id}">${emp.full_name} (${emp.username})</option>`).join('');

            $('#successorSelect').html(successorOptions);
        }
    });
}

// Load employees with resignations for settlement modal
function loadEmployeesWithResignationsForSettlements() {
    $.post('exit_management.php', {
        ajax_action: 'get_employees_with_resignations'
    }, function(response) {
        if (response && response.length > 0) {
            const employeeOptions = '<option value="">Select Employee</option>' +
                response.map(emp => `<option value="${emp.id}">${emp.full_name} (${emp.username})</option>`).join('');

            $('#settlementEmployeeSelect').html(employeeOptions);
        } else {
            $('#settlementEmployeeSelect').html('<option value="">No employees with resignations found</option>');
        }
    }).fail(function(err) {
        console.error('Error loading resigning employees for settlements:', err);
        $('#settlementEmployeeSelect').html('<option value="">Error loading employees</option>');
    });
}

// Load resignations for settlement modal
function loadResignations() {
    $.post('exit_management.php', {
        ajax_action: 'get_resignations',
        controller: 'resignation'
    }, function(response) {
        if (response && response.length > 0) {
            const resignationOptions = '<option value="">Select Resignation</option>' +
                response.map(res => `<option value="${res.id}">${res.employee_name} - ${res.resignation_type}</option>`).join('');

            $('#settlementResignationSelect').html(resignationOptions);
        }
    });
}

// Modal display functions
function showResignationModal(resignationId = null) {
    loadEmployees(); // Ensure employees are loaded
    if (resignationId) {
        // Edit mode
        $('#resignationModalTitle').text('Edit Resignation');
        loadResignationData(resignationId);
    } else {
        // Create mode
        $('#resignationModalTitle').text('Submit Resignation');
        $('#resignationForm')[0].reset();
        $('#resignationId').val('');
        $('#approvalSection').hide();
        $('#eligibilityMessage').hide();
    }
    $('#resignationModal').modal('show');
}

function showInterviewModal(interviewId = null) {
    loadEmployeesWithResignations();
    loadInterviewers();
    if (interviewId) {
        $('#interviewModalTitle').text('Edit Exit Interview');
        loadInterviewData(interviewId);
    } else {
        $('#interviewModalTitle').text('Schedule Exit Interview');
        $('#interviewForm')[0].reset();
        $('#interviewId').val('');
        $('#feedbackSection').hide();
    }
    $('#interviewModal').modal('show');
}

function showTransferModal(planId = null) {
    loadEmployeesWithResignationsForTransfers();
    loadSuccessors();
    if (planId) {
        $('#transferModalTitle').text('Edit Transfer Plan');
        loadTransferData(planId);
    } else {
        $('#transferModalTitle').text('Create Knowledge Transfer Plan');
        $('#transferForm')[0].reset();
        $('#transferPlanId').val('');
        $('#transferItemsContainer').html(getTransferItemTemplate(0));
    }
    $('#transferModal').modal('show');
}

function showSettlementModal(settlementId = null) {
    loadEmployeesWithResignationsForSettlements();
    loadResignations();
    if (settlementId) {
        $('#settlementModalTitle').text('Edit Settlement');
        loadSettlementData(settlementId);
    } else {
        $('#settlementModalTitle').text('Calculate Final Settlement');
        $('#settlementForm')[0].reset();
        $('#settlementId').val('');
    }
    $('#settlementModal').modal('show');

    // Add event listener for employee selection to auto-populate salary components
    $('#settlementEmployeeSelect').on('change', function() {
        const employeeId = $(this).val();
        if (employeeId) {
            loadEmployeeSalaryComponents(employeeId);
        } else {
            // Clear salary fields if no employee selected
            clearSalaryFields();
        }
    });
}

function showDocumentModal(documentId = null) {
    if (documentId) {
        $('#documentModalTitle').text('Edit Document');
        loadDocumentData(documentId);
    } else {
        $('#documentModalTitle').text('Upload Document');
        $('#documentForm')[0].reset();
        $('#documentId').val('');
    }
    $('#documentModal').modal('show');
}

function showSurveyModal(surveyId = null) {
    if (surveyId) {
        $('#surveyModalTitle').text('Edit Survey');
        loadSurveyData(surveyId);
    } else {
        $('#surveyModalTitle').text('Create Post-Exit Survey');
        $('#surveyForm')[0].reset();
        $('#surveyId').val('');
        $('#surveyQuestionsContainer').html(getSurveyQuestionTemplate(0));
    }
    $('#surveyModal').modal('show');
}

// Form submission functions
function submitResignationForm() {
    const formData = new FormData($('#resignationForm')[0]);
    const resignationId = $('#resignationId').val();
    
    // Add action and controller parameters
    formData.append('ajax_action', resignationId ? 'update_resignation' : 'submit_resignation');
    formData.append('controller', 'resignation');

    $('#resignationSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: 'exit_management.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#resignationModal').modal('hide');
                showToast('success', response.message);
                loadResignationsTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        },
        error: function() {
            showToast('error', 'An error occurred while saving the resignation.');
        },
        complete: function() {
            $('#resignationSubmitBtn').prop('disabled', false).html('Submit Resignation');
        }
    });
}

function submitInterviewForm() {
    const formData = new FormData($('#interviewForm')[0]);
    const interviewId = $('#interviewId').val();
    
    formData.append('ajax_action', interviewId ? 'update_interview' : 'submit_interview');
    formData.append('controller', 'interview');

    $('#interviewSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: 'exit_management.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#interviewModal').modal('hide');
                showToast('success', response.message);
                loadInterviewsTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        },
        error: function() {
            showToast('error', 'An error occurred while saving the interview.');
        },
        complete: function() {
            $('#interviewSubmitBtn').prop('disabled', false).html('Schedule Interview');
        }
    });
}

function submitTransferForm() {
    const formData = new FormData($('#transferForm')[0]);
    const transferPlanId = $('#transferPlanId').val();
    
    formData.append('ajax_action', transferPlanId ? 'update_transfer_plan' : 'submit_transfer_plan');
    formData.append('controller', 'transfer');

    $('#transferSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: 'exit_management.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#transferModal').modal('hide');
                showToast('success', response.message);
                loadTransfersTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        },
        error: function() {
            showToast('error', 'An error occurred while saving the transfer plan.');
        },
        complete: function() {
            $('#transferSubmitBtn').prop('disabled', false).html('Create Transfer Plan');
        }
    });
}

function submitSettlementForm() {
    const formData = new FormData($('#settlementForm')[0]);
    const settlementId = $('#settlementId').val();
    
    formData.append('ajax_action', settlementId ? 'update_settlement' : 'submit_settlement');
    formData.append('controller', 'settlement');

    $('#settlementSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: 'exit_management.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#settlementModal').modal('hide');
                showToast('success', response.message);
                loadSettlementsTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        },
        error: function() {
            showToast('error', 'An error occurred while saving the settlement.');
        },
        complete: function() {
            $('#settlementSubmitBtn').prop('disabled', false).html('Save Settlement');
        }
    });
}

function submitDocumentForm() {
    const formData = new FormData($('#documentForm')[0]);
    const documentId = $('#documentId').val();
    
    // Log form data for debugging
    console.log('=== DOCUMENT FORM SUBMISSION ===');
    console.log('Employee ID:', formData.get('employee_id'));
    console.log('Document Type:', formData.get('document_type'));
    console.log('Title:', formData.get('title'));
    console.log('File:', formData.get('document_file'));
    
    formData.append('ajax_action', documentId ? 'update_document' : 'submit_document');
    formData.append('controller', 'documentation');

    $('#documentSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');

    $.ajax({
        url: 'exit_management.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log('=== UPLOAD RESPONSE ===');
            console.log('Response:', response);
            if (response && response.success) {
                $('#documentModal').modal('hide');
                showToast('success', response.message);
                console.log('Upload succeeded, loading documents table...');
                loadDocumentsTable();
            } else {
                console.error('Upload failed:', response);
                showToast('error', response ? response.message : 'An error occurred while uploading the document.');
            }
        },
        error: function(xhr, status, error) {
            console.error('=== AJAX ERROR ===');
            console.error('Status:', status, 'Error:', error);
            console.error('Response text:', xhr.responseText);
            showToast('error', 'An error occurred while uploading the document.');
        },
        complete: function() {
            $('#documentSubmitBtn').prop('disabled', false).html('Upload Document');
        }
    });
}

function submitSurveyForm() {
    const formData = new FormData($('#surveyForm')[0]);
    const surveyId = $('#surveyId').val();
    
    formData.append('ajax_action', surveyId ? 'update_survey' : 'submit_survey');
    formData.append('controller', 'survey');

    $('#surveySubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: 'exit_management.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#surveyModal').modal('hide');
                showToast('success', response.message);
                loadSurveysTable();
            } else {
                showToast('error', response.message);
            }
        },
        error: function() {
            showToast('error', 'An error occurred while saving the survey.');
        },
        complete: function() {
            $('#surveySubmitBtn').prop('disabled', false).html('Create Survey');
        }
    });
}

// Dynamic form element functions
function addTransferItem() {
    const itemCount = $('#transferItemsContainer .transfer-item').length;
    $('#transferItemsContainer').append(getTransferItemTemplate(itemCount));
}

function addSurveyQuestion() {
    const questionCount = $('#surveyQuestionsContainer .question-item').length;
    $('#surveyQuestionsContainer').append(getSurveyQuestionTemplate(questionCount));
    normalizeQuestionIndexes();
}

function toggleQuestionOptions($select) {
    const $questionItem = $select.closest('.question-item');
    const $container = $questionItem.find('.options-container');
    const type = $select.val();
    const questionIndex = $questionItem.data('question-index');

    if (['radio', 'checkbox', 'select'].includes(type)) {
        $container.show();

        // Add default option fields if none exist
        const optionsList = $questionItem.find('.options-list');
        if (optionsList.children('.option-row').length === 0) {
            addOptionInput(questionIndex);
            addOptionInput(questionIndex);
        }
    } else {
        $container.hide();
    }
}

function addOptionInput(questionIndex, value = '') {
    const optionsList = $(`#options-list-${questionIndex}`);
    if (!optionsList.length) return;

    const optionRow = `
        <div class="option-row d-flex align-items-center mb-2">
            <input type="text" class="form-control form-control-sm mr-2" name="questions[${questionIndex}][options][]" value="${value.replace(/"/g, '&quot;')}" placeholder="Option text" required>
            <button type="button" class="btn btn-sm btn-outline-danger remove-option" title="Remove option">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    `;

    optionsList.append(optionRow);
}

function normalizeQuestionIndexes() {
    $('#surveyQuestionsContainer .question-item').each(function(index) {
        const $item = $(this);
        $item.attr('data-question-index', index);
        $item.find('.card-header span').first().html(`<i class="fas fa-question-circle text-primary mr-2"></i>Question ${index + 1}`);
        $item.find('input[name^="questions"]').each(function() {
            const name = $(this).attr('name');
            const newName = name.replace(/^questions\[\d+\]/, `questions[${index}]`);
            $(this).attr('name', newName);
        });
        $item.find('select[name^="questions"]').each(function() {
            const name = $(this).attr('name');
            const newName = name.replace(/^questions\[\d+\]/, `questions[${index}]`);
            $(this).attr('name', newName);
        });
        $item.find('.question-type').attr('name', `questions[${index}][type]`);
        $item.find('.form-check-input').attr('id', `req${index}`).attr('name', `questions[${index}][required]`);
        $item.find('.form-check-label').attr('for', `req${index}`);
        $item.find('.add-option').attr('data-question-index', index);
        $item.find('.options-list').attr('id', `options-list-${index}`);
    });
}

// Template functions
function getTransferItemTemplate(index) {
    return `
        <div class="transfer-item mb-3 p-3 border rounded">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" name="items[${index}][type]" required>
                        <option value="">Select Type</option>
                        <option value="process">Process</option>
                        <option value="system">System</option>
                        <option value="contact">Contact</option>
                        <option value="document">Document</option>
                        <option value="skill">Skill</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="items[${index}][title]" placeholder="Title" required>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="items[${index}][priority]">
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="items[${index}][due_date]">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <textarea class="form-control" name="items[${index}][description]" rows="2" placeholder="Description"></textarea>
                </div>
            </div>
        </div>
    `;
}

function getSurveyQuestionTemplate(index) {
    return `
        <div class="question-item card mb-3 shadow-sm border-0" data-question-index="${index}">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="font-weight-bold">
                    <i class="fas fa-question-circle text-primary mr-2"></i>Question ${index + 1}
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-question" title="Delete question">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            <div class="card-body pt-3 pb-2">
                <!-- Question Text -->
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Question Text <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" name="questions[${index}][text]" placeholder="Enter your question here..." required>
                </div>

                <!-- Question Type & Required -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Question Type <span class="text-danger">*</span></label>
                            <select class="form-control question-type" name="questions[${index}][type]" required>
                                <option value="">-- Select Type --</option>
                                <option value="text">Short Text</option>
                                <option value="textarea">Long Text / Paragraph</option>
                                <option value="radio">Multiple Choice (Single Answer)</option>
                                <option value="checkbox">Multiple Choice (Multiple Answers)</option>
                                <option value="select">Dropdown List</option>
                                <option value="rating">Rating Scale (1-5)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Question Settings</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="req${index}" name="questions[${index}][required]" checked>
                                <label class="form-check-label" for="req${index}">
                                    Required <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Options Container -->
                <div class="options-container mt-3 pt-3 border-top" style="display: none;">
                    <label class="font-weight-bold">Answer Options <span class="text-danger">*</span></label>
                    <small class="form-text text-muted d-block mb-2">Add options, one row at a time.</small>
                    <div class="options-list" id="options-list-${index}"></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary add-option" data-question-index="${index}">
                        <i class="fas fa-plus"></i> Add Option
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Calculation functions
function calculateSettlement() {
    const basicSalary = parseFloat($('#basicSalary').val()) || 0;
    const hra = parseFloat($('#hra').val()) || 0;
    const conveyance = parseFloat($('#conveyance').val()) || 0;
    const lta = parseFloat($('#lta').val()) || 0;
    const medicalAllowance = parseFloat($('#medicalAllowance').val()) || 0;
    const otherAllowances = parseFloat($('#otherAllowances').val()) || 0;

    const providentFund = parseFloat($('#providentFund').val()) || 0;
    const gratuity = parseFloat($('#gratuity').val()) || 0;
    const noticePay = parseFloat($('#noticePay').val()) || 0;
    const outstandingLoans = parseFloat($('#outstandingLoans').val()) || 0;
    const otherDeductions = parseFloat($('#otherDeductions').val()) || 0;

    const totalEarnings = basicSalary + hra + conveyance + lta + medicalAllowance + otherAllowances;
    const totalDeductions = providentFund + gratuity + noticePay + outstandingLoans + otherDeductions;
    const netPayable = totalEarnings - totalDeductions;

    $('#netPayable').val(netPayable.toFixed(2));
}

// Data loading functions (stubs - need to be implemented based on controller methods)
function loadResignationData(id) {
    // Load resignation data for editing
    $.post('exit_management.php', {
        ajax_action: 'get_resignation',
        controller: 'resignation',
        resignation_id: id
    }, function(response) {
        if (response) {
            // Populate form fields (handle preclearance desk person ID mapping)
            Object.keys(response).forEach(key => {
                if (key === 'preclearance_desk_person') {
                    $('#preclearanceDeskPerson').val(response[key]);
                } else {
                    $(`#${key}`).val(response[key]);
                }
            });
        }
    });
}

function loadInterviewData(id) {
    // Load interview data for editing
    $.post('exit_management.php', {
        ajax_action: 'get_interview',
        controller: 'interview',
        interview_id: id
    }, function(response) {
        if (response) {
            Object.keys(response).forEach(key => {
                $(`#${key}`).val(response[key]);
            });
        }
    });
}

function loadTransferData(id) {
    // Load transfer plan data for editing
}

function loadSettlementData(id) {
    // Load settlement data for editing
}

function loadDocumentData(id) {
    // Load document data for editing
}

function loadSurveyData(id) {
    // Load survey data for editing
}

// Table loading functions
let archivedResignationsData = [];
let archivedResignationPage = 1;
const archivedResignationPageSize = 10;

function loadResignationsTable(status = 'active') {
    let apiStatus = status;
    if (status === 'active' || status === null) {
        apiStatus = null;
    } else if (status === 'all') {
        apiStatus = 'all';
    }

    const payload = {
        ajax_action: 'get_resignations',
        controller: 'resignation',
        status: apiStatus
    };

    if (status === 'archived') {
        // keep archived in separate table via dedicated function
        toggleArchivedResignations(true);
        return;
    } else {
        $('#archived-resignations-container').hide();
        $('#toggle-archived-resignations').text('Show Archived');
    }

    $.post('exit_management.php', payload, function(response) {
        console.log('Resignations Response:', response);
        const tbody = $('#resignations-tbody');
        tbody.empty();

        if (response && response.length > 0) {
            response.forEach(function(resignation) {
                const statusBadge = getStatusBadge(resignation.status);
                const fs = resignation.archived_from_status ? ` (from ${resignation.archived_from_status})` : '';
                const tooltip = resignation.archived_from_status ? `Archived from status: ${resignation.archived_from_status}` : '';

                const actions = `
                    <button class="btn btn-sm btn-info" onclick="showResignationModal(${resignation.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${resignation.status === 'archived' ? `
                    <button class="btn btn-sm btn-success" onclick="unarchiveResignation(${resignation.id})" title="Unarchive Resignation">
                        <i class="fas fa-undo"></i>
                    </button>
                    ` : `
                    <button class="btn btn-sm btn-secondary" onclick="archiveResignation(${resignation.id})" title="Archive Resignation">
                        <i class="fas fa-archive"></i>
                    </button>
                    `}
                `;

                tbody.append(`
                    <tr title="${tooltip}">
                        <td>${resignation.employee_name || '<em class="text-danger">Missing Employee</em>'}</td>
                        <td>${resignation.department || '-'}</td>
                        <td>${resignation.email || '-'}</td>
                        <td>${resignation.preclearance_desk_person_name || '-'}</td>
                        <td>${resignation.resignation_type}</td>
                        <td>${resignation.reason || '-'}</td>
                        <td>${resignation.notice_date}</td>
                        <td>${resignation.last_working_date}</td>
                        <td>${resignation.comments ? resignation.comments.substring(0, 50) + '...' : '-'}</td>
                        <td>${statusBadge}</td>
                        <td>${actions}</td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="10" class="text-center">No resignations found</td></tr>');
        }
    });
}

function loadArchivedResignationsTable(page = 1) {
    $.post('exit_management.php', {
        ajax_action: 'get_archived_resignations',
        controller: 'resignation'
    }, function(response) {
        archivedResignationsData = Array.isArray(response) ? response : [];
        archivedResignationPage = page;
        renderArchivedResignationsPage();
    });
}

function renderArchivedResignationsPage() {
    const tbody = $('#archived-resignations-tbody');
    tbody.empty();

    if (!archivedResignationsData.length) {
        tbody.append('<tr><td colspan="11" class="text-center">No archived resignations found</td></tr>');
        $('#archived-resignations-pagination').empty();
        return;
    }

    const total = archivedResignationsData.length;
    const totalPages = Math.ceil(total / archivedResignationPageSize);
    const page = Math.min(Math.max(1, archivedResignationPage), totalPages);
    const startIndex = (page - 1) * archivedResignationPageSize;
    const endIndex = Math.min(startIndex + archivedResignationPageSize, total);
    const pageItems = archivedResignationsData.slice(startIndex, endIndex);

    pageItems.forEach(function(resignation) {
        const statusBadge = getStatusBadge(resignation.status);
        const actions = `
            <button class="btn btn-sm btn-success" onclick="unarchiveResignation(${resignation.id})" title="Unarchive Resignation">
                <i class="fas fa-undo"></i>
            </button>
        `;

        tbody.append(`
            <tr>
                <td>${resignation.employee_name || '<em class="text-danger">Missing Employee</em>'}</td>
                <td>${resignation.department || '-'}</td>
                <td>${resignation.email || '-'}</td>
                <td>${resignation.preclearance_desk_person_name || '-'}</td>
                <td>${resignation.resignation_type}</td>
                <td>${resignation.reason || '-'}</td>
                <td>${resignation.notice_date}</td>
                <td>${resignation.last_working_date}</td>
                <td>${resignation.comments ? resignation.comments.substring(0, 50) + '...' : '-'}</td>
                <td>${statusBadge}</td>
                <td>${actions}</td>
            </tr>
        `);
    });

    // Pagination controls
    const pagination = $('#archived-resignations-pagination');
    pagination.empty();

    if (totalPages > 1) {
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === page ? 'btn-primary' : 'btn-secondary';
            pagination.append(`
                <button class="btn btn-sm ${activeClass} mr-1" onclick="goToArchivedPage(${i})">${i}</button>
            `);
        }
    }
}

function goToArchivedPage(page) {
    archivedResignationPage = page;
    renderArchivedResignationsPage();
}


function toggleArchivedResignations(open = false) {
    const container = $('#archived-resignations-container');
    const button = $('#toggle-archived-resignations');

    if (open) {
        container.show();
        button.text('Hide Archived');
        loadArchivedResignationsTable();
        return;
    }

    if (container.is(':visible')) {
        container.hide();
        button.text('Show Archived');
    } else {
        container.show();
        button.text('Hide Archived');
        loadArchivedResignationsTable();
    }
}

function loadInterviewsTable(status = 'all') {
    $.post('exit_management.php', {
        ajax_action: 'get_interviews',
        controller: 'interview',
        status: status
    }, function(response) {
        const tbody = $('#interviews-tbody');
        tbody.empty();

        if (response && response.length > 0) {
            response.forEach(function(interview) {
                const statusBadge = getStatusBadge(interview.status);
                const actions = `
                    <button class="btn btn-sm btn-info" onclick="showInterviewModal(${interview.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-success" onclick="completeInterview(${interview.id})">
                        <i class="fas fa-check"></i>
                    </button>
                `;

                tbody.append(`
                    <tr>
                        <td>${interview.employee_name}</td>
                        <td>${interview.interviewer_name}</td>
                        <td>${interview.scheduled_date}</td>
                        <td>${statusBadge}</td>
                        <td>${actions}</td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="5" class="text-center">No interviews found</td></tr>');
        }
    });
}

function loadTransfersTable(status = 'all') {
    $.post('exit_management.php', {
        ajax_action: 'get_transfer_plans',
        controller: 'transfer',
        status: status
    }, function(response) {
        console.log('Transfer plans response:', response);
        const tbody = $('#transfers-tbody');
        tbody.empty();

        if (response && Array.isArray(response) && response.length > 0) {
            response.forEach(function(plan) {
                const statusBadge = getStatusBadge(plan.status);
                const actions = `
                    <button class="btn btn-sm btn-info" onclick="showTransferModal(${plan.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="viewTransferItems(${plan.id})">
                        <i class="fas fa-list"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteTransferPlan(${plan.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                `;

                tbody.append(`
                    <tr>
                        <td>${plan.employee_name}</td>
                        <td>${plan.successor_name || 'Not assigned'}</td>
                        <td>${plan.start_date}</td>
                        <td>${plan.end_date}</td>
                        <td>${statusBadge}</td>
                        <td>${actions}</td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="6" class="text-center">No transfer plans found</td></tr>');
        }
    }, 'json').fail(function(err) {
        console.error('Error loading transfers:', err);
    });
}

function loadSettlementsTable(status = 'all') {
    $.post('exit_management.php', {
        ajax_action: 'get_settlements',
        controller: 'settlement',
        status: status
    }, function(response) {
        const tbody = $('#settlements-tbody');
        tbody.empty();

        if (response && response.length > 0) {
            response.forEach(function(settlement) {
                const statusBadge = getStatusBadge(settlement.status);
                const actions = `
                    <button class="btn btn-sm btn-info" onclick="showSettlementModal(${settlement.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-success" onclick="printSettlement(${settlement.id})">
                        <i class="fas fa-print"></i>
                    </button>
                `;

                tbody.append(`
                    <tr>
                        <td>${settlement.employee_name}</td>
                        <td>${settlement.settlement_date}</td>
                        <td>$${parseFloat(settlement.net_payable).toFixed(2)}</td>
                        <td>${statusBadge}</td>
                        <td>${actions}</td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="5" class="text-center">No settlements found</td></tr>');
        }
    });
}

function loadDocumentsTable(status = 'all') {
    console.log('=== LOADING DOCUMENTS TABLE ===');
    $.post('exit_management.php', {
        ajax_action: 'get_documents',
        controller: 'documentation',
        status: status
    }, function(response) {
        console.log('=== DOCUMENTS TABLE RESPONSE ===');
        console.log('Full response:', response);
        console.log('Response type:', typeof response);
        console.log('Is array:', Array.isArray(response));
        console.log('Response length:', response ? response.length : 'null/undefined');
        
        const tbody = $('#documents-tbody');
        tbody.empty();

        if (response && Array.isArray(response) && response.length > 0) {
            console.log('Found ' + response.length + ' documents');
            response.forEach(function(doc, index) {
                console.log('Document ' + index + ':', doc);
                const actions = `
                    <button class="btn btn-sm btn-info" onclick="viewDocument(${doc.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-success" onclick="downloadDocument(${doc.id})">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteDocument(${doc.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                `;

                tbody.append(`
                    <tr>
                        <td>${doc.employee_name || 'Unknown'}</td>
                        <td>${doc.document_type || 'N/A'}</td>
                        <td>${doc.title || 'N/A'}</td>
                        <td>${doc.created_at || 'N/A'}</td>
                        <td>${actions}</td>
                    </tr>
                `);
            });
        } else {
            console.log('No documents found or invalid response');
            tbody.append('<tr><td colspan="5" class="text-center">No documents found</td></tr>');
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('Error loading documents:', status, error, xhr.responseText);
        const tbody = $('#documents-tbody');
        tbody.empty();
        tbody.append('<tr><td colspan="5" class="text-center text-danger">Error loading documents: ' + error + '</td></tr>');
    });
}

function loadSurveysTable(status = 'all') {
    $.post('exit_management.php', {
        ajax_action: 'get_surveys',
        controller: 'survey',
        status: status
    }, function(response) {
        const tbody = $('#surveys-tbody');
        tbody.empty();

        if (response && response.length > 0) {
            response.forEach(function(survey) {
                const statusBadge = getStatusBadge(survey.status);
                const actions = `
                    <button class="btn btn-sm btn-success" onclick="answerSurvey(${survey.id})">
                        <i class="fas fa-pen"></i> Answer
                    </button>
                    <button class="btn btn-sm btn-info" onclick="showSurveyModal(${survey.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="viewSurveyResponses(${survey.id})">
                        <i class="fas fa-chart-bar"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" onclick="duplicateSurvey(${survey.id})">
                        <i class="fas fa-copy"></i>
                    </button>
                `;

                tbody.append(`
                    <tr>
                        <td>${survey.title}</td>
                        <td>${survey.start_date}</td>
                        <td>${survey.end_date}</td>
                        <td>${statusBadge}</td>
                        <td>${actions}</td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="5" class="text-center">No surveys found</td></tr>');
        }
    });
}

// Helper functions
function getStatusBadge(status) {
    const statusClasses = {
        'pending': 'badge badge-warning',
        'approved': 'badge badge-success',
        'rejected': 'badge badge-danger',
        'completed': 'badge badge-success',
        'active': 'badge badge-primary',
        'inactive': 'badge badge-secondary',
        'scheduled': 'badge badge-info',
        'draft': 'badge badge-light',
        'archived': 'badge badge-dark'
    };

    const cssClass = statusClasses[status] || 'badge badge-secondary';
    return `<span class="${cssClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
}

// Toast notification function
function showToast(type, message) {
    // Assuming you have a toast system in place
    if (typeof showToastMessage === 'function') {
        showToastMessage(type, message);
    } else {
        alert(message);
    }
}

// Load dashboard data
function loadDashboardData() {
    $.post('exit_management.php', {
        ajax_action: 'get_dashboard_stats'
    }, function(response) {
        if (response) {
            $('#pending-resignations').text(response.pending_resignations || 0);
            $('#scheduled-interviews').text(response.scheduled_interviews || 0);
            $('#active-transfers').text(response.active_transfers || 0);
            $('#pending-settlements').text(response.pending_settlements || 0);
        }
    });

    // Load charts and recent resignations
    loadResignationTrendChart();
    loadResignationReasonsChart();
    loadExitStatusChart();
    loadResignationTypeChart();
    loadRecentResignations();
    loadDashboardMetrics();
}

// Load section data based on section name
function onResignationStatusFilterChange() {
    const selectedStatus = $('#resignation-status-filter').val();

    if (selectedStatus === 'archived') {
        $('#archived-resignations-container').show();
        $('#toggle-archived-resignations').text('Hide Archived');
        loadArchivedResignationsTable();
    } else if (selectedStatus === 'all') {
        $('#archived-resignations-container').show();
        $('#toggle-archived-resignations').text('Hide Archived');
        loadResignationsTable('all');
        loadArchivedResignationsTable();
    } else {
        $('#archived-resignations-container').hide();
        $('#toggle-archived-resignations').text('Show Archived');
        loadResignationsTable(selectedStatus);
    }
}

function onInterviewStatusFilterChange() {
    const selectedStatus = $('#interview-status-filter').val();
    loadInterviewsTable(selectedStatus === 'active' ? 'all' : selectedStatus);
}

function onTransferStatusFilterChange() {
    const selectedStatus = $('#transfer-status-filter').val();
    loadTransfersTable(selectedStatus === 'active' ? 'all' : selectedStatus);
}

function onSettlementStatusFilterChange() {
    const selectedStatus = $('#settlement-status-filter').val();
    loadSettlementsTable(selectedStatus === 'active' ? 'all' : selectedStatus);
}

function onDocumentStatusFilterChange() {
    const selectedStatus = $('#document-status-filter').val();
    loadDocumentsTable(selectedStatus === 'active' ? 'all' : selectedStatus);
}

function onSurveyStatusFilterChange() {
    const selectedStatus = $('#survey-status-filter').val();
    loadSurveysTable(selectedStatus === 'active' ? 'all' : selectedStatus);
}

function loadSectionData(sectionName) {
    switch (sectionName) {
        case 'resignations':
            onResignationStatusFilterChange();
            break;
        case 'interviews':
            onInterviewStatusFilterChange();
            break;
        case 'transfers':
            onTransferStatusFilterChange();
            break;
        case 'settlements':
            onSettlementStatusFilterChange();
            break;
        case 'documents':
            onDocumentStatusFilterChange();
            break;
        case 'surveys':
            onSurveyStatusFilterChange();
            break;
        default:
            loadDashboardData();
    }
}

// Action functions
function archiveResignation(id) {
    if (confirm('Are you sure you want to archive this resignation (remove it from active list)?')) {
        $.post('exit_management.php', {
            ajax_action: 'archive_resignation',
            controller: 'resignation',
            resignation_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadResignationsTable();
                loadArchivedResignationsTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        });
    }
}

function unarchiveResignation(id) {
    if (confirm('Are you sure you want to unarchive this resignation?')) {
        $.post('exit_management.php', {
            ajax_action: 'unarchive_resignation',
            controller: 'resignation',
            resignation_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadResignationsTable();
                loadArchivedResignationsTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        });
    }
}

function completeInterview(id) {
    if (confirm('Mark this interview as completed?')) {
        $.post('exit_management.php', {
            ajax_action: 'complete_interview',
            controller: 'interview',
            interview_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadInterviewsTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        });
    }
}

function deleteTransferPlan(id) {
    if (confirm('Are you sure you want to delete this knowledge transfer plan? This will also delete all associated transfer items.')) {
        $.post('exit_management.php', {
            ajax_action: 'delete_transfer_plan',
            controller: 'transfer',
            plan_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadTransfersTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        });
    }
}

function viewTransferItems(id) {
    $.post('exit_management.php', {
        ajax_action: 'get_transfer_items',
        controller: 'transfer',
        plan_id: id
    }, function(response) {
        if (response && response.length > 0) {
            let itemsHtml = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Type</th><th>Title</th><th>Priority</th><th>Due Date</th><th>Status</th></tr></thead><tbody>';
            response.forEach(item => {
                const statusBadge = getStatusBadge(item.status);
                itemsHtml += `<tr><td>${item.type}</td><td>${item.title}</td><td>${item.priority}</td><td>${item.due_date || 'N/A'}</td><td>${statusBadge}</td></tr>`;
            });
            itemsHtml += '</tbody></table></div>';
            
            // Show in a modal or alert
            const modal = `
                <div class="modal fade" id="transferItemsModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Knowledge Transfer Items</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">${itemsHtml}</div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modal);
            $('#transferItemsModal').modal('show');
            $('#transferItemsModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        } else {
            showToast('info', 'No transfer items found for this plan.');
        }
    });
}

function printSettlement(id) {
    // Open print window
    window.open('exit_management.php?ajax_action=print_settlement&settlement_id=' + id, '_blank');
}

function viewDocument(id) {
    // Fetch document details and handle appropriately
    $.get('exit_management.php', {
        ajax_action: 'view_document',
        document_id: id
    }, function(response) {
        if (response.success) {
            const fileExt = response.file_path.split('.').pop().toLowerCase();
            const isViewable = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'].includes(fileExt);
            
            if (isViewable) {
                // For images and PDFs, open the file directly
                window.open(response.file_path, '_blank');
            } else {
                // For other files like .docx, trigger download directly
                downloadFile(response.file_path, response.title);
            }
        } else {
            showToast('error', response.message || 'Failed to load document');
        }
    }, 'json').fail(function() {
        showToast('error', 'Failed to load document');
    });
}

function downloadFile(filePath, fileName) {
    // Create a temporary link and trigger download
    const link = document.createElement('a');
    link.href = filePath;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function downloadDocument(id) {
    // Fetch download info first
    $.get('exit_management.php', {
        ajax_action: 'download_document',
        document_id: id
    }, function(response) {
        if (response.success) {
            downloadFile(response.file_path, response.title);
        } else {
            showToast('error', response.message || 'Failed to download document');
        }
    }, 'json').fail(function() {
        showToast('error', 'Failed to download document');
    });
}

function deleteDocument(id) {
    if (confirm('Are you sure you want to delete this document?')) {
        $.post('exit_management.php', {
            ajax_action: 'delete_document',
            controller: 'documentation',
            document_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadDocumentsTable();
            } else {
                showToast('error', response.message);
            }
        }, 'json');
    }
}

function viewSurveyResponses(id) {
    $.post('exit_management.php', {
        ajax_action: 'get_survey_responses',
        controller: 'survey',
        survey_id: id
    }, function(response) {
        if (response && response.length > 0) {
            let responsesHtml = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Question</th><th>Response</th><th>Respondent</th><th>Date</th></tr></thead><tbody>';
            response.forEach(resp => {
                responsesHtml += `<tr><td>${resp.question_text}</td><td>${resp.answer_value}</td><td>${resp.respondent_name}</td><td>${resp.submitted_at}</td></tr>`;
            });
            responsesHtml += '</tbody></table></div>';
            
            const modal = `
                <div class="modal fade" id="surveyResponsesModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Survey Responses</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">${responsesHtml}</div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modal);
            $('#surveyResponsesModal').modal('show');
            $('#surveyResponsesModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        } else {
            showToast('info', 'No responses found for this survey.');
        }
    });
}

function duplicateSurvey(id) {
    if (confirm('Create a copy of this survey?')) {
        $.post('exit_management.php', {
            ajax_action: 'duplicate_survey',
            controller: 'survey',
            survey_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadSurveysTable();
            } else {
                showToast('error', response.message);
            }
        });
    }
}

// Answer Survey Functions
function answerSurvey(surveyId) {
    $.post('exit_management.php', {
        ajax_action: 'get_survey',
        controller: 'survey',
        survey_id: surveyId
    }, function(response) {
        if (response && response.id) {
            const survey = response;
            
            // Set survey title and description
            $('#answerSurveyTitle').text('Answer: ' + survey.title);
            $('#answerSurveyDesc').text(survey.description || '');
            $('#answerSurveyId').val(surveyId);
            
            // Initialize survey wizard
            initializeSurveyWizard(survey);
            
            // Show modal
            $('#answerSurveyModal').modal('show');
        } else {
            showToast('error', 'Failed to load survey');
        }
    });
}

function initializeSurveyWizard(survey) {
    // Store survey data globally
    window.currentSurvey = survey;
    window.currentQuestionIndex = 0;
    window.surveyAnswers = {};
    
    // Load first question
    loadSurveyQuestion(0);
    updateProgress();
}

function loadSurveyQuestion(index) {
    const survey = window.currentSurvey;
    const question = survey.questions[index];
    
    if (!question) return;
    
    const questionHtml = generateModernQuestionField(question, index);
    $('#surveyQuestionContainer').html(questionHtml);
    
    // Update counter
    $('#questionCounter').text(`Question ${index + 1} of ${survey.questions.length}`);
    
    // Update navigation buttons
    $('#prevQuestionBtn').toggle(index > 0);
    $('#nextQuestionBtn').toggle(index < survey.questions.length - 1);
    $('#answerSurveySubmitBtn').toggle(index === survey.questions.length - 1);
    
    // Restore previous answer if exists
    const answer = window.surveyAnswers[question.id];
    if (answer) {
        restoreQuestionAnswer(question, answer);
    }
}

function generateModernQuestionField(question, index) {
    const questionId = question.id;
    const questionText = question.question_text || question.text || '';
    const questionType = question.question_type || question.type || 'text';
    const required = question.required ? ' <span class="text-danger">*</span>' : '';
    
    let fieldHtml = `
        <div class="question-card animated fadeIn">
            <div class="question-header mb-4">
                <h4 class="question-title">${index + 1}. ${questionText}${required}</h4>
            </div>
            <div class="question-body">
    `;
    
    switch(questionType) {
        case 'text':
            fieldHtml += `
                <div class="form-group">
                    <input type="text" class="form-control form-control-lg" name="responses[${questionId}]" placeholder="Enter your answer..." ${question.required ? 'required' : ''}>
                </div>
            `;
            break;
            
        case 'textarea':
            fieldHtml += `
                <div class="form-group">
                    <textarea class="form-control" name="responses[${questionId}]" rows="4" placeholder="Enter your detailed answer..." ${question.required ? 'required' : ''}></textarea>
                </div>
            `;
            break;
            
        case 'radio':
            let radioOptions = [];
            if (question.options) {
                if (Array.isArray(question.options)) {
                    radioOptions = question.options;
                } else if (typeof question.options === 'string') {
                    radioOptions = question.options.split('\n').map(opt => opt.trim()).filter(opt => opt);
                }
            }
            fieldHtml += '<div class="options-grid">';
            radioOptions.forEach(function(option, optIndex) {
                const optionId = `radio_${questionId}_${optIndex}`;
                fieldHtml += `
                    <div class="option-card" onclick="selectRadioOption('${optionId}')">
                        <input type="radio" name="responses[${questionId}]" value="${option}" id="${optionId}" class="d-none" ${question.required ? 'required' : ''}>
                        <label for="${optionId}" class="option-label">
                            <div class="option-radio"></div>
                            <span class="option-text">${option}</span>
                        </label>
                    </div>
                `;
            });
            fieldHtml += '</div>';
            break;
            
        case 'checkbox':
            let checkboxOptions = [];
            if (question.options) {
                if (Array.isArray(question.options)) {
                    checkboxOptions = question.options;
                } else if (typeof question.options === 'string') {
                    checkboxOptions = question.options.split('\n').map(opt => opt.trim()).filter(opt => opt);
                }
            }
            fieldHtml += '<div class="options-grid">';
            checkboxOptions.forEach(function(option, optIndex) {
                const optionId = `checkbox_${questionId}_${optIndex}`;
                fieldHtml += `
                    <div class="option-card" onclick="toggleCheckboxOption('${optionId}')">
                        <input type="checkbox" name="responses[${questionId}][]" value="${option}" id="${optionId}" class="d-none">
                        <label for="${optionId}" class="option-label">
                            <div class="option-checkbox"></div>
                            <span class="option-text">${option}</span>
                        </label>
                    </div>
                `;
            });
            fieldHtml += '</div>';
            break;
            
        case 'select':
            let selectOptions = [];
            if (question.options) {
                if (Array.isArray(question.options)) {
                    selectOptions = question.options;
                } else if (typeof question.options === 'string') {
                    selectOptions = question.options.split('\n').map(opt => opt.trim()).filter(opt => opt);
                }
            }
            fieldHtml += `
                <div class="form-group">
                    <select class="form-control form-control-lg" name="responses[${questionId}]" ${question.required ? 'required' : ''}>
                        <option value="">-- Select an option --</option>
            `;
            selectOptions.forEach(function(option) {
                fieldHtml += `<option value="${option}">${option}</option>`;
            });
            fieldHtml += `
                    </select>
                </div>
            `;
            break;
            
        case 'rating':
            fieldHtml += `
                <div class="rating-container">
                    <div class="rating-stars">
            `;
            for (let i = 1; i <= 5; i++) {
                fieldHtml += `
                    <div class="rating-star" onclick="selectRating(${questionId}, ${i})" data-rating="${i}">
                        <i class="far fa-star"></i>
                        <span class="rating-label">${i}</span>
                    </div>
                `;
            }
            fieldHtml += `
                    </div>
                    <input type="hidden" name="responses[${questionId}]" id="rating_${questionId}">
                </div>
            `;
            break;
            
        default:
            fieldHtml += `
                <div class="form-group">
                    <input type="text" class="form-control form-control-lg" name="responses[${questionId}]" placeholder="Enter your answer..." ${question.required ? 'required' : ''}>
                </div>
            `;
    }
    
    fieldHtml += `
            </div>
        </div>
    `;
    
    return fieldHtml;
}

function selectRadioOption(optionId) {
    // Unselect all options in the same group
    $(`input[name="${$('#' + optionId).attr('name')}"]`).prop('checked', false);
    $(`input[name="${$('#' + optionId).attr('name')}"]`).closest('.option-card').removeClass('selected');
    
    // Select the clicked option
    $('#' + optionId).prop('checked', true);
    $('#' + optionId).closest('.option-card').addClass('selected');
}

function toggleCheckboxOption(optionId) {
    const checkbox = $('#' + optionId);
    const card = checkbox.closest('.option-card');
    
    if (checkbox.is(':checked')) {
        checkbox.prop('checked', false);
        card.removeClass('selected');
    } else {
        checkbox.prop('checked', true);
        card.addClass('selected');
    }
}

function selectRating(questionId, rating) {
    $(`.rating-star[data-rating]`).removeClass('selected');
    $(`.rating-star[data-rating]`).find('i').removeClass('fas').addClass('far');
    
    for (let i = 1; i <= rating; i++) {
        $(`.rating-star[data-rating="${i}"]`).addClass('selected');
        $(`.rating-star[data-rating="${i}"]`).find('i').removeClass('far').addClass('fas');
    }
    
    $(`#rating_${questionId}`).val(rating);
}

function restoreQuestionAnswer(question, answer) {
    const questionType = question.question_type || question.type;
    
    if (questionType === 'radio') {
        $(`input[name="responses[${question.id}]"][value="${answer}"]`).prop('checked', true);
        $(`input[name="responses[${question.id}]"][value="${answer}"]`).closest('.option-card').addClass('selected');
    } else if (questionType === 'checkbox') {
        if (Array.isArray(answer)) {
            answer.forEach(val => {
                $(`input[name="responses[${question.id}][]"][value="${val}"]`).prop('checked', true);
                $(`input[name="responses[${question.id}][]"][value="${val}"]`).closest('.option-card').addClass('selected');
            });
        }
    } else if (questionType === 'rating') {
        selectRating(question.id, parseInt(answer));
    } else {
        $(`[name="responses[${question.id}]"]`).val(answer);
    }
}

function updateProgress() {
    const progress = ((window.currentQuestionIndex + 1) / window.currentSurvey.questions.length) * 100;
    $('#surveyProgress').css('width', progress + '%');
}

// Navigation functions
$('#prevQuestionBtn').on('click', function() {
    saveCurrentAnswer();
    if (window.currentQuestionIndex > 0) {
        window.currentQuestionIndex--;
        loadSurveyQuestion(window.currentQuestionIndex);
        updateProgress();
    }
});

$('#nextQuestionBtn').on('click', function() {
    if (validateCurrentQuestion()) {
        saveCurrentAnswer();
        if (window.currentQuestionIndex < window.currentSurvey.questions.length - 1) {
            window.currentQuestionIndex++;
            loadSurveyQuestion(window.currentQuestionIndex);
            updateProgress();
        }
    }
});

function saveCurrentAnswer() {
    const question = window.currentSurvey.questions[window.currentQuestionIndex];
    const questionType = question.question_type || question.type;
    let answer = null;
    
    if (questionType === 'checkbox') {
        answer = [];
        $(`input[name="responses[${question.id}][]"]:checked`).each(function() {
            answer.push($(this).val());
        });
    } else if (questionType === 'radio') {
        answer = $(`input[name="responses[${question.id}]"]:checked`).val();
    } else {
        answer = $(`[name="responses[${question.id}]"]`).val();
    }
    
    if (answer !== null && answer !== '') {
        window.surveyAnswers[question.id] = answer;
    }
}

function validateCurrentQuestion() {
    const question = window.currentSurvey.questions[window.currentQuestionIndex];
    const questionType = question.question_type || question.type;
    let isValid = true;
    
    if (question.required) {
        if (questionType === 'checkbox') {
            isValid = $(`input[name="responses[${question.id}][]"]:checked`).length > 0;
        } else if (questionType === 'radio') {
            isValid = $(`input[name="responses[${question.id}]"]:checked`).length > 0;
        } else {
            isValid = $(`[name="responses[${question.id}]"]`).val().trim() !== '';
        }
        
        if (!isValid) {
            showToast('warning', 'Please answer this required question before proceeding.');
            return false;
        }
    }
    
    return true;
}



// Handle answer survey form submission
$('#answerSurveyForm').on('submit', function(e) {
    e.preventDefault();
    submitSurveyAnswers();
});

function submitSurveyAnswers() {
    // Save current answer
    saveCurrentAnswer();
    
    const surveyId = $('#answerSurveyId').val();
    const formData = new FormData();
    
    formData.append('ajax_action', 'submit_survey_response');
    formData.append('controller', 'survey');
    formData.append('survey_id', surveyId);
    formData.append('employee_id', 0); // Will be set by controller from session
    
    // Add all answers
    for (const [questionId, answer] of Object.entries(window.surveyAnswers)) {
        if (Array.isArray(answer)) {
            answer.forEach(val => {
                formData.append(`responses[${questionId}]`, val);
            });
        } else {
            formData.append(`responses[${questionId}]`, answer);
        }
    }

    $('#answerSurveySubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

    $.ajax({
        url: 'exit_management.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#answerSurveyModal').modal('hide');
                showToast('success', 'Survey answers submitted successfully!');
                loadSurveysTable();
            } else {
                showToast('error', response.message || 'Failed to submit survey answers');
            }
        },
        error: function() {
            showToast('error', 'An error occurred while submitting your answers.');
        },
        complete: function() {
            $('#answerSurveySubmitBtn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Survey');
        }
    });
}

// ============================================
// DASHBOARD CHARTS AND VISUALIZATIONS
// ============================================

var charts = {};

// Load resignation trend chart
function loadResignationTrendChart() {
    $.post('exit_management.php', {
        ajax_action: 'get_resignation_trend'
    }, function(response) {
        if (response && response.labels && response.data) {
            renderResignationTrendChart(response.labels, response.data);
        }
    }, 'json');
}

// Render resignation trend line chart
function renderResignationTrendChart(labels, data) {
    const ctx = document.getElementById('resignationTrendChart');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (charts.resignationTrend) {
        charts.resignationTrend.destroy();
    }

    charts.resignationTrend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Resignations',
                data: data,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3498db',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Load resignation reasons chart
function loadResignationReasonsChart() {
    $.post('exit_management.php', {
        ajax_action: 'get_resignation_reasons'
    }, function(response) {
        if (response && response.labels && response.data) {
            renderResignationReasonsChart(response.labels, response.data);
        }
    }, 'json');
}

// Render resignation reasons pie chart
function renderResignationReasonsChart(labels, data) {
    const ctx = document.getElementById('resignationReasonsChart');
    if (!ctx) return;

    if (charts.resignationReasons) {
        charts.resignationReasons.destroy();
    }

    const colors = [
        '#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff',
        '#ff9999', '#66ff99', '#99ccff', '#ffcc99', '#cc99ff'
    ];

    charts.resignationReasons = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Load exit status chart
function loadExitStatusChart() {
    $.post('exit_management.php', {
        ajax_action: 'get_exit_status'
    }, function(response) {
        if (response && response.labels && response.data) {
            renderExitStatusChart(response.labels, response.data);
        }
    }, 'json');
}

// Render exit status bar chart
function renderExitStatusChart(labels, data) {
    const ctx = document.getElementById('exitStatusChart');
    if (!ctx) return;

    if (charts.exitStatus) {
        charts.exitStatus.destroy();
    }

    const colors = ['#27ae60', '#f39c12', '#e74c3c'];

    charts.exitStatus = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Count',
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: colors.slice(0, labels.length),
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Load resignation type chart
function loadResignationTypeChart() {
    $.post('exit_management.php', {
        ajax_action: 'get_resignation_types'
    }, function(response) {
        if (response && response.labels && response.data) {
            renderResignationTypeChart(response.labels, response.data);
        }
    }, 'json');
}

// Render resignation type bar chart
function renderResignationTypeChart(labels, data) {
    const ctx = document.getElementById('resignationTypeChart');
    if (!ctx) return;

    if (charts.resignationType) {
        charts.resignationType.destroy();
    }

    const colors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6'];

    charts.resignationType = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Count',
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: colors.slice(0, labels.length),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Load recent resignations table
function loadRecentResignations() {
    $.post('exit_management.php', {
        ajax_action: 'get_recent_resignations',
        limit: 10
    }, function(response) {
        console.log('loadRecentResignations response:', response);
        if (response && Array.isArray(response)) {
            renderRecentResignations(response);
        } else {
            $('#recent-resignations-tbody').html('<tr><td colspan="8" class="text-center text-muted">No resignations found</td></tr>');
            $('#recent-count').text(0);
        }
    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
        console.error('loadRecentResignations AJAX error:', textStatus, errorThrown, jqXHR.responseText);
        $('#recent-resignations-tbody').html('<tr><td colspan="8" class="text-center text-danger">Error loading resignations</td></tr>');
        $('#recent-count').text(0);
    });
}

// Render recent resignations table
function renderRecentResignations(resignations) {
    const tbody = $('#recent-resignations-tbody');
    $('#recent-count').text(resignations.length);

    if (!resignations || resignations.length === 0) {
        tbody.html('<tr><td colspan="8" class="text-center text-muted">No resignations found</td></tr>');
        return;
    }

    let html = '';
    resignations.forEach(function(res) {
        const statusClass = getStatusBadgeClass(res.status);
        const daysLeft = res.days_left >= 0 ? res.days_left : 'Exited';
        const noticeDate = formatDate(res.notice_date);
        const lastDate = formatDate(res.last_working_date);

        html += `<tr>
                    <td><strong>${res.full_name}</strong></td>
                    <td>${res.department || 'N/A'}</td>
                    <td>${res.resignation_type || 'N/A'}</td>
                    <td>${res.reason || 'N/A'}</td>
                    <td>${noticeDate}</td>
                    <td>${lastDate}</td>
                    <td><span class="badge ${statusClass}">${res.status}</span></td>
                    <td>${daysLeft}</td>
                </tr>`;
    });

    tbody.html(html);
}

// Get status badge CSS class
function getStatusBadgeClass(status) {
    switch(status.toLowerCase()) {
        case 'pending': return 'badge-warning';
        case 'approved': return 'badge-success';
        case 'rejected': return 'badge-danger';
        case 'completed': return 'badge-info';
        default: return 'badge-secondary';
    }
}

// Format date to readable format
function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

// Load employee salary components from payroll database
function loadEmployeeSalaryComponents(employeeId) {
    $.post('exit_management.php', {
        ajax_action: 'get_employee_salary_components',
        controller: 'settlement',
        employee_id: employeeId
    }, function(response) {
        if (response && response.success) {
            // Populate salary fields with data from payroll
            $('#basicSalary').val(response.basic_salary || 0);
            $('#hra').val(response.hra || 0);
            $('#conveyance').val(response.conveyance || 0);
            $('#lta').val(response.lta || 0);
            $('#medicalAllowance').val(response.medical_allowance || 0);
            $('#otherAllowances').val(response.other_allowances || 0);
            $('#providentFund').val(response.provident_fund || 0);
            $('#gratuity').val(response.gratuity || 0);
            $('#noticePay').val(response.notice_pay || 0);
            $('#outstandingLoans').val(response.outstanding_loans || 0);
            $('#otherDeductions').val(response.other_deductions || 0);

            // Calculate net payable after populating fields
            calculateSettlement();
        } else {
            // If no salary data found, show message but don't clear fields
            showToast('info', 'No salary data found for this employee. Please enter manually.');
        }
    }, 'json').fail(function() {
        showToast('error', 'Failed to load salary components. Please enter manually.');
    });
}

// Clear salary component fields
function clearSalaryFields() {
    $('#basicSalary').val('');
    $('#hra').val('');
    $('#conveyance').val('');
    $('#lta').val('');
    $('#medicalAllowance').val('');
    $('#otherAllowances').val('');
    $('#providentFund').val('');
    $('#gratuity').val('');
    $('#noticePay').val('');
    $('#outstandingLoans').val('');
    $('#otherDeductions').val('');
    $('#netPayable').val('');
}

// Load dashboard metrics
function loadDashboardMetrics() {
    $.post('exit_management.php', {
        ajax_action: 'get_dashboard_metrics'
    }, function(response) {
        console.log('loadDashboardMetrics response:', response);
        if (response) {
            $('#total-exited').text(response.total_exited || 0);
            $('#avg-notice').text(response.avg_notice || 0);
            $('#top-reason').text((response.top_reason || 'N/A').substring(0, 20));
            $('#avg-interviews').text(response.interview_rate + '%' || '0%');
        }
    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
        console.error('loadDashboardMetrics AJAX error:', textStatus, errorThrown, jqXHR.responseText);
    });
}