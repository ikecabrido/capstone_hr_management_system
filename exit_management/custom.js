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

            $('#employeeSelect, #interviewEmployeeSelect, #transferEmployeeSelect, #settlementEmployeeSelect, #documentEmployeeSelect').html(employeeOptions);
        } else {
            console.warn('No employees returned or not an array:', response);
        }
    }, 'json').fail(function(err) {
        console.error('Error loading employees:', err);
    });
}

// Load interviewers for interview modal
function loadInterviewers() {
    $.post('exit_management.php', {
        ajax_action: 'get_eligible_employees'
    }, function(response) {
        if (response && response.length > 0) {
            const interviewerOptions = '<option value="">Select Interviewer</option>' +
                response.map(emp => `<option value="${emp.id}">${emp.full_name} (${emp.username})</option>`).join('');

            $('#interviewerSelect').html(interviewerOptions);
        }
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
    }
    $('#resignationModal').modal('show');
}

function showInterviewModal(interviewId = null) {
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
}

function toggleQuestionOptions($select) {
    const $container = $select.closest('.question-item').find('.options-container');
    const type = $select.val();

    if (['radio', 'checkbox', 'select'].includes(type)) {
        $container.show();
    } else {
        $container.hide();
    }
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
        <div class="question-item mb-3 p-3 border rounded">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="questions[${index}][text]" placeholder="Question text" required>
                </div>
                <div class="col-md-3">
                    <select class="form-control question-type" name="questions[${index}][type]" required>
                        <option value="text">Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="radio">Radio Buttons</option>
                        <option value="checkbox">Checkboxes</option>
                        <option value="select">Select</option>
                        <option value="rating">Rating (1-5)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="questions[${index}][required]" checked>
                        <label class="form-check-label">Required</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-question">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="options-container mt-2" style="display: none;">
                <textarea class="form-control" name="questions[${index}][options]" rows="2" placeholder="Options (one per line)"></textarea>
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
            // Populate form fields
            Object.keys(response).forEach(key => {
                $(`#${key}`).val(response[key]);
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
function loadResignationsTable() {
    $.post('exit_management.php', {
        ajax_action: 'get_resignations',
        controller: 'resignation'
    }, function(response) {
        console.log('Resignations Response:', response);
        const tbody = $('#resignations-tbody');
        tbody.empty();

        if (response && response.length > 0) {
            response.forEach(function(resignation) {
                const statusBadge = getStatusBadge(resignation.status);
                const actions = `
                    <button class="btn btn-sm btn-info" onclick="showResignationModal(${resignation.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteResignation(${resignation.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                `;

                tbody.append(`
                    <tr>
                        <td>${resignation.employee_name || '<em class="text-danger">Missing Employee</em>'}</td>
                        <td>${resignation.department || '-'}</td>
                        <td>${resignation.email || '-'}</td>
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

function loadInterviewsTable() {
    $.post('exit_management.php', {
        ajax_action: 'get_interviews',
        controller: 'interview'
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

function loadTransfersTable() {
    $.post('exit_management.php', {
        ajax_action: 'get_transfer_plans',
        controller: 'transfer'
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

function loadSettlementsTable() {
    $.post('exit_management.php', {
        ajax_action: 'get_settlements',
        controller: 'settlement'
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

function loadDocumentsTable() {
    console.log('=== LOADING DOCUMENTS TABLE ===');
    $.post('exit_management.php', {
        ajax_action: 'get_documents',
        controller: 'documentation'
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

function loadSurveysTable() {
    $.post('exit_management.php', {
        ajax_action: 'get_surveys',
        controller: 'survey'
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
        'draft': 'badge badge-light'
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
}

// Load section data based on section name
function loadSectionData(sectionName) {
    switch (sectionName) {
        case 'resignations':
            loadResignationsTable();
            break;
        case 'interviews':
            loadInterviewsTable();
            break;
        case 'transfers':
            loadTransfersTable();
            break;
        case 'settlements':
            loadSettlementsTable();
            break;
        case 'documents':
            loadDocumentsTable();
            break;
        case 'surveys':
            loadSurveysTable();
            break;
        default:
            loadDashboardData();
    }
}

// Action functions
function deleteResignation(id) {
    if (confirm('Are you sure you want to delete this resignation?')) {
        $.post('exit_management.php', {
            ajax_action: 'delete_resignation',
            controller: 'resignation',
            resignation_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadResignationsTable();
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
    window.open('exit_management.php?ajax_action=view_document&document_id=' + id, '_blank');
}

function downloadDocument(id) {
    window.location.href = 'exit_management.php?ajax_action=download_document&document_id=' + id;
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
                responsesHtml += `<tr><td>${resp.question_text}</td><td>${resp.response_value}</td><td>${resp.respondent_name}</td><td>${resp.submitted_at}</td></tr>`;
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
            
            // Load survey questions
            loadSurveyQuestionsForAnswering(survey);
            
            // Show modal
            $('#answerSurveyModal').modal('show');
        } else {
            showToast('error', 'Failed to load survey');
        }
    });
}

function loadSurveyQuestionsForAnswering(survey) {
    const questionsContainer = $('#surveyQuestionsAnswer');
    questionsContainer.empty();
    
    if (survey.questions && survey.questions.length > 0) {
        survey.questions.forEach(function(question, index) {
            const questionHtml = generateQuestionAnswerField(question, index);
            questionsContainer.append(questionHtml);
        });
    } else {
        questionsContainer.html('<p class="text-muted">No questions in this survey</p>');
    }
}

function generateQuestionAnswerField(question, index) {
    const questionId = question.id || index;
    const questionText = question.question_text || question.text || '';
    const questionType = question.question_type || question.type || 'text';
    const required = question.required ? ' required' : '';
    let fieldHtml = `
        <div class="form-group mb-4 p-3 border rounded">
            <label class="font-weight-bold">${index + 1}. ${questionText}${question.required ? '<span class="text-danger">*</span>' : ''}</label>
    `;
    
    switch(questionType) {
        case 'text':
            fieldHtml += `<input type="text" class="form-control" name="responses[${questionId}]"${required}>`;
            break;
            
        case 'textarea':
            fieldHtml += `<textarea class="form-control" name="responses[${questionId}]" rows="4"${required}></textarea>`;
            break;
            
        case 'radio':
            let radioOptions = [];
            if (question.options) {
                if (Array.isArray(question.options)) {
                    radioOptions = question.options;
                } else if (typeof question.options === 'string') {
                    radioOptions = question.options.split('\n');
                }
            }
            radioOptions.forEach(function(option) {
                const optionText = typeof option === 'string' ? option.trim() : option;
                if (optionText) {
                    fieldHtml += `
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="responses[${questionId}]" value="${optionText}" id="radio_${questionId}_${optionText.replace(/\s+/g, '_')}"${required}>
                            <label class="form-check-label" for="radio_${questionId}_${optionText.replace(/\s+/g, '_')}">
                                ${optionText}
                            </label>
                        </div>
                    `;
                }
            });
            break;
            
        case 'checkbox':
            let checkboxOptions = [];
            if (question.options) {
                if (Array.isArray(question.options)) {
                    checkboxOptions = question.options;
                } else if (typeof question.options === 'string') {
                    checkboxOptions = question.options.split('\n');
                }
            }
            fieldHtml += `<input type="hidden" name="responses[${questionId}]" value="">`;
            checkboxOptions.forEach(function(option) {
                const optionText = typeof option === 'string' ? option.trim() : option;
                if (optionText) {
                    fieldHtml += `
                        <div class="form-check mt-2">
                            <input class="form-check-input checkbox-group" type="checkbox" name="responses[${questionId}]" value="${optionText}" id="checkbox_${questionId}_${optionText.replace(/\s+/g, '_')}">
                            <label class="form-check-label" for="checkbox_${questionId}_${optionText.replace(/\s+/g, '_')}">
                                ${optionText}
                            </label>
                        </div>
                    `;
                }
            });
            break;
            
        case 'select':
            let selectOptions = [];
            if (question.options) {
                if (Array.isArray(question.options)) {
                    selectOptions = question.options;
                } else if (typeof question.options === 'string') {
                    selectOptions = question.options.split('\n');
                }
            }
            fieldHtml += `<select class="form-control" name="responses[${questionId}]"${required}>
                <option value="">-- Select an option --</option>`;
            selectOptions.forEach(function(option) {
                const optionText = typeof option === 'string' ? option.trim() : option;
                if (optionText) {
                    fieldHtml += `<option value="${optionText}">${optionText}</option>`;
                }
            });
            fieldHtml += `</select>`;
            break;
            
        case 'rating':
            fieldHtml += `<div class="rating-group mt-2">`;
            for (let i = 1; i <= 5; i++) {
                fieldHtml += `
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="responses[${questionId}]" value="${i}" id="rating_${questionId}_${i}"${required}>
                        <label class="form-check-label" for="rating_${questionId}_${i}">
                            ${'⭐'.repeat(i)} ${i}
                        </label>
                    </div>
                `;
            }
            fieldHtml += `</div>`;
            break;
            
        default:
            fieldHtml += `<input type="text" class="form-control" name="responses[${questionId}]"${required}>`;
    }
    
    fieldHtml += `</div>`;
    return fieldHtml;
}

// Handle answer survey form submission
$('#answerSurveyForm').on('submit', function(e) {
    e.preventDefault();
    submitSurveyAnswers();
});

function submitSurveyAnswers() {
    const surveyId = $('#answerSurveyId').val();
    const formData = new FormData($('#answerSurveyForm')[0]);
    
    formData.append('ajax_action', 'submit_survey_response');
    formData.append('controller', 'survey');
    formData.append('employee_id', 0); // Will be set by controller from session

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
            $('#answerSurveySubmitBtn').prop('disabled', false).html('Submit Answers');
        }
    });
}