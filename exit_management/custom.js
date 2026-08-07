// Exit Management JavaScript
let interviewModalViewOnlyMode = false;
let transferModalViewOnlyMode = false;

$(document).ready(function() {
    $.ajaxSetup({
        cache: false,
        timeout: 15000,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    // Initialize all modal functions
    initializeModals();
    loadEmployees();
    loadDashboardData();
    initExitModalPickers();
    setInterval(loadPayrollApprovalNotifications, 60000); // refresh payroll approval notifications every minute
});

function setInterviewModalMode(viewOnly = false) {
    interviewModalViewOnlyMode = viewOnly;
    const $formFields = $('#interviewForm').find('input:not([type=hidden]), textarea, select');
    $formFields.prop('disabled', viewOnly);

    if (viewOnly) {
        $('#interviewSubmitBtn').hide();
        $('#saveHrAssessmentBtn').hide();
        $('#editInterviewBtn').show();
        $('#interviewForm').find('.form-control').addClass('disabled');
        $('#interviewCaseSelect').hide();
        $('#interviewCaseDisplay').show();
    } else {
        $('#interviewSubmitBtn').show();
        $('#editInterviewBtn').hide();
        $('#interviewForm').find('.form-control').removeClass('disabled');
        $('#interviewCaseSelect').show();
        $('#interviewCaseDisplay').hide();

        if ($('#interviewId').val()) {
            $('#interviewSubmitBtn').text('Save Changes');
        } else {
            $('#interviewSubmitBtn').text('Schedule Interview');
        }

        updateInterviewSubmitState();
    }
}

function setTransferModalMode(viewOnly = false) {
    transferModalViewOnlyMode = viewOnly;
    const $formFields = $('#transferForm').find('input:not([type=hidden]), textarea, select');
    $formFields.prop('disabled', viewOnly);

    if (viewOnly) {
        $('#transferSubmitBtn').hide();
        $('#editTransferBtn').show();
        $('#addTransferItem, .remove-item').hide();
        $('#transferForm').find('.form-control').addClass('disabled');
    } else {
        $('#transferSubmitBtn').show();
        $('#editTransferBtn').hide();
        $('#addTransferItem, .remove-item').show();
        $('#transferForm').find('.form-control').removeClass('disabled');

        if ($('#transferPlanId').val()) {
            $('#transferSubmitBtn').text('Save Changes');
        } else {
            $('#transferSubmitBtn').text('Create Transfer Plan');
        }
    }
}

function normalizeInterviewTime(value) {
    if (!value) {
        return '';
    }

    const [hours = '', minutes = ''] = String(value).split(':');
    const hourValue = parseInt(hours, 10);
    const minuteValue = parseInt(minutes, 10);

    if (Number.isNaN(hourValue) || Number.isNaN(minuteValue)) {
        return '';
    }

    return `${String(hourValue).padStart(2, '0')}:${String(minuteValue).padStart(2, '0')}`;
}

function normalizeDateInputValue(value) {
    if (!value) {
        return '';
    }

    if (value instanceof Date) {
        if (Number.isNaN(value.getTime())) {
            return '';
        }
        return value.toISOString().slice(0, 10);
    }

    const dateString = String(value).trim();
    if (!dateString) {
        return '';
    }

    const ymdMatch = dateString.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (ymdMatch) {
        return `${ymdMatch[1]}-${ymdMatch[2]}-${ymdMatch[3]}`;
    }

    const dateTimeMatch = dateString.match(/^(\d{4}-\d{2}-\d{2})[T\s].*$/);
    if (dateTimeMatch) {
        return dateTimeMatch[1];
    }

    const parsedDate = new Date(dateString.replace(/\s+/g, 'T'));
    if (!Number.isNaN(parsedDate.getTime())) {
        return parsedDate.toISOString().slice(0, 10);
    }

    const parts = dateString.split(/[-\/\. ]+/);
    if (parts.length >= 3) {
        const [first, second, third] = parts;
        if (first.length === 4) {
            return `${first.padStart(4, '0')}-${second.padStart(2, '0')}-${third.padStart(2, '0')}`;
        }
        if (third.length === 4) {
            return `${third.padStart(4, '0')}-${first.padStart(2, '0')}-${second.padStart(2, '0')}`;
        }
    }

    return '';
}

function setDateInputValue(selector, value) {
    const normalized = normalizeDateInputValue(value);
    const element = document.querySelector(selector);
    if (!element) {
        return;
    }

    if (element._flatpickr && typeof element._flatpickr.setDate === 'function') {
        if (normalized) {
            element._flatpickr.setDate(normalized, true);
        } else {
            element._flatpickr.clear();
        }
        return;
    }

    element.value = normalized;
}

function buildInterviewTimeValue() {
    const hour = String($('#interviewHour').val() || '').trim();
    const minute = String($('#interviewMinute').val() || '').trim();
    const meridiem = $('#interviewMeridiem').val() || 'AM';

    if (!hour || !minute) {
        return '';
    }

    let hourValue = parseInt(hour, 10);
    if (Number.isNaN(hourValue)) {
        return '';
    }

    if (meridiem === 'PM' && hourValue < 12) {
        hourValue += 12;
    } else if (meridiem === 'AM' && hourValue === 12) {
        hourValue = 0;
    }

    const minuteValue = String(parseInt(minute, 10)).padStart(2, '0');
    return `${String(hourValue).padStart(2, '0')}:${minuteValue}`;
}

function populateInterviewTimeFields(value) {
    const normalized = normalizeInterviewTime(value);
    if (!normalized) {
        $('#interviewHour').val('');
        $('#interviewMinute').val('');
        $('#interviewMeridiem').val('AM');
        return;
    }

    const [hourPart, minutePart] = normalized.split(':');
    let hourValue = parseInt(hourPart, 10);
    const minuteValue = parseInt(minutePart, 10);

    if (Number.isNaN(hourValue) || Number.isNaN(minuteValue)) {
        $('#interviewHour').val('');
        $('#interviewMinute').val('');
        $('#interviewMeridiem').val('AM');
        return;
    }

    let meridiem = 'AM';
    if (hourValue >= 12) {
        meridiem = 'PM';
    }

    if (hourValue > 12) {
        hourValue -= 12;
    } else if (hourValue === 0) {
        hourValue = 12;
    }

    $('#interviewHour').val(String(hourValue));
    $('#interviewMinute').val(String(minuteValue).padStart(2, '0'));
    $('#interviewMeridiem').val(meridiem);
}

function formatInterviewTimeDisplay(value) {
    if (!value) {
        return '';
    }

    const normalized = normalizeInterviewTime(value);
    if (!normalized) {
        return '';
    }

    const [hourPart, minutePart] = normalized.split(':');
    const hour = parseInt(hourPart, 10);
    const minute = parseInt(minutePart, 10);
    const suffix = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;

    return `${displayHour}:${String(minute).padStart(2, '0')} ${suffix}`;
}

function canManageHrAssessment() {
    const role = String(window.exitManagementUserRole || '').toLowerCase();
    return ['admin', 'superadmin', 'administrator', 'hr_admin'].includes(role);
}

function initExitModalPickers() {
    if (typeof flatpickr !== 'function') {
        return;
    }

    flatpickr('.exit-modal input[type="date"]', {
        altInput: true,
        altFormat: 'F j, Y',
        dateFormat: 'Y-m-d',
        allowInput: true,
        clickOpens: true,
        monthSelectorType: 'dropdown',
        yearSelectorType: 'dropdown',
        locale: 'default',
        position: 'auto center',
        wrap: false,
        onReady: function(selectedDates, dateStr, instance) {
            addFlatpickrFooter(instance);
            enableFlatpickrYearDropdown(instance);
        },
        onOpen: function(selectedDates, dateStr, instance) {
            addFlatpickrFooter(instance);
            enableFlatpickrYearDropdown(instance);
        }
    });

    flatpickr('.exit-modal input[type="time"]', {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: false,
        allowInput: true,
        clickOpens: true,
        position: 'auto center'
    });
}

function enableFlatpickrYearDropdown(instance) {
    if (!instance || !instance.calendarContainer) {
        return;
    }

    const yearWrapper = instance.calendarContainer.querySelector('.flatpickr-current-month .numInputWrapper');
    const yearInput = yearWrapper ? yearWrapper.querySelector('input') : null;
    if (!yearWrapper || !yearInput || yearWrapper.dataset.yearDropdownAttached) {
        return;
    }

    yearWrapper.dataset.yearDropdownAttached = 'true';
    yearWrapper.style.cursor = 'pointer';
    yearInput.style.cursor = 'pointer';

    yearWrapper.addEventListener('click', function(event) {
        event.stopPropagation();
        yearInput.focus();
        yearInput.click();
    });
}

function addFlatpickrFooter(instance) {
    if (!instance || !instance.calendarContainer) {
        return;
    }

    if (instance.calendarContainer.querySelector('.flatpickr-footer')) {
        return;
    }

    const footer = document.createElement('div');
    footer.className = 'flatpickr-footer';

    const todayBtn = document.createElement('button');
    todayBtn.type = 'button';
    todayBtn.className = 'flatpickr-footer-btn flatpickr-today-btn';
    todayBtn.textContent = 'Today';
    todayBtn.addEventListener('click', function() {
        instance.setDate(new Date(), true);
        instance.close();
    });

    const clearBtn = document.createElement('button');
    clearBtn.type = 'button';
    clearBtn.className = 'flatpickr-footer-btn flatpickr-clear-btn';
    clearBtn.textContent = 'Clear';
    clearBtn.addEventListener('click', function() {
        instance.clear();
        instance.close();
    });

    footer.appendChild(todayBtn);
    footer.appendChild(clearBtn);
    instance.calendarContainer.appendChild(footer);
}

// Render pagination controls
function renderPagination(containerId, total, currentPage, limit, onPageChange) {
    const container = $(`#${containerId}`);
    container.empty();

    if (!total || total <= limit) {
        return;
    }

    const totalPages = Math.ceil(total / limit);
    const startRecord = (currentPage - 1) * limit + 1;
    const endRecord = Math.min(currentPage * limit, total);

    let paginationHtml = `
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Showing ${startRecord} to ${endRecord} of ${total} entries
            </div>
            <nav aria-label="Table pagination">
                <ul class="pagination pagination-sm mb-0">
    `;

    // Previous button
    if (currentPage > 1) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${onPageChange(currentPage - 1)}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
    } else {
        paginationHtml += `
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
    }

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${onPageChange(1)}">1</a>
            </li>
        `;
        if (startPage > 2) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="event.preventDefault(); ${onPageChange(i)}">${i}</a>
                </li>
            `;
        }
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${onPageChange(totalPages)}">${totalPages}</a>
            </li>
        `;
    }

    // Next button
    if (currentPage < totalPages) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${onPageChange(currentPage + 1)}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    } else {
        paginationHtml += `
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    }

    paginationHtml += `
                </ul>
            </nav>
        </div>
    `;

    container.html(paginationHtml);
}

// Legacy function for resignations table
function renderResignationsPagination(containerId, response, status, currentPage, searchTerm = '') {
    if (!response || !response.total) {
        $(`#${containerId}`).empty();
        return;
    }

    const onPageChange = (page) => `loadResignationsTable('${status}', ${page}, '${escapeJsString(searchTerm)}')`;
    renderPagination(containerId, response.total, response.page || currentPage, response.limit || 10, onPageChange);
}

function renderTerminationsPagination(containerId, response, status, currentPage, searchTerm = '') {
    if (!response || !response.total) {
        $(`#${containerId}`).empty();
        return;
    }

    const onPageChange = (page) => `loadTerminationsTable('${status}', ${page}, '${escapeJsString(searchTerm)}')`;
    renderPagination(containerId, response.total, response.page || currentPage, response.limit || 10, onPageChange);
}

// Legacy function for interviews table
function renderInterviewsPagination(containerId, response, status, currentPage, searchTerm = '') {
    if (!response || !response.total) {
        $(`#${containerId}`).empty();
        return;
    }

    const onPageChange = (page) => `loadInterviewsTable('${status}', ${page}, '${escapeJsString(searchTerm)}')`;
    renderPagination(containerId, response.total, response.page || currentPage, response.limit || 10, onPageChange);
}

// Initialize modal event handlers
function initializeModals() {
    // Resignation Modal
    $('#resignationForm').on('submit', function(e) {
        e.preventDefault();
        submitResignationForm();
    });

    $('#terminationForm').on('submit', function(e) {
        e.preventDefault();
        submitTerminationForm();
    });

    $('#terminationEmployeeSelect').on('change', function() {
        const employeeId = $(this).val();
        if (employeeId) {
            checkTerminationEligibility(employeeId);
        } else {
            $('#terminationEligibilityMessage').hide();
            $('#terminationSubmitBtn').prop('disabled', false);
        }
    });

    // Interview Modal
    $('#interviewForm').on('submit', function(e) {
        e.preventDefault();
        submitInterviewForm();
    });

    $('#interviewCaseSelect, #interviewerSelect, #interviewDate, #interviewHour, #interviewMinute, #interviewMeridiem').on('change input', function() {
        updateInterviewSubmitState();
    });

    // Save HR Assessment button
    $('#saveHrAssessmentBtn').on('click', function() {
        if (!canManageHrAssessment()) {
            showToast('error', 'HR assessment saving is restricted to administrators.');
            return;
        }

        const interviewId = $('#interviewId').val();
        if (!interviewId) {
            showToast('error', 'Cannot save HR assessment: interview not loaded');
            return;
        }
        saveHrAssessment(interviewId);
    });

    $('#editInterviewBtn').on('click', function() {
        setInterviewModalMode(false);
        $('#interviewModalTitle').text('Edit Exit Interview');
        if (canManageHrAssessment()) {
            $('#saveHrAssessmentBtn').show();
        } else {
            $('#saveHrAssessmentBtn').hide();
        }
        $('#interviewSubmitBtn').text($('#interviewId').val() ? 'Save Changes' : 'Schedule Interview');
    });

    $('#editTransferBtn').on('click', function() {
        setTransferModalMode(false);
        $('#transferModalTitle').text('Edit Knowledge Transfer Plan');
        $('#transferSubmitBtn').text($('#transferPlanId').val() ? 'Save Changes' : 'Create Transfer Plan');
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

    // Confirmation modal action
    $('#confirmActionBtn').on('click', function() {
        if (typeof confirmationCallback === 'function') {
            const callback = confirmationCallback;
            confirmationCallback = null;
            $('#confirmationModal').modal('hide');
            callback();
        } else {
            $('#confirmationModal').modal('hide');
        }
    });

    $('#confirmationModal').on('show.bs.modal', function() {
        const currentMaxZ = Math.max(1050, ...$('.modal:visible').map(function() {
            return Number($(this).css('z-index')) || 1050;
        }).get());
        const newZ = currentMaxZ + 20;
        $(this).css('z-index', newZ);
        setTimeout(function() {
            $('.modal-backdrop').not('.stacked').css('z-index', newZ - 10).addClass('stacked');
        }, 0);
    });

    $('#confirmationModal').on('hidden.bs.modal', function() {
        confirmationCallback = null;
        $(this).css('z-index', '');
        $('.modal-backdrop.stacked').css('z-index', '').removeClass('stacked');
    });

    // Employee eligibility check on selection change
    $('#employeeSelect').on('change', function() {
        const employeeId = $(this).val();
        if (employeeId) {
            checkEmployeeEligibility(employeeId);
            loadEmployeeLastAttendanceDate(employeeId);
        } else {
            $('#eligibilityMessage').hide();
            $('#lastWorkingDate').val('');
        }
    });
}

// Load employee's last attendance date and auto-fill last working date
function loadEmployeeLastAttendanceDate(employeeId) {
    $.post('exit_management.php', {
        ajax_action: 'get_employee_last_attendance_date',
        controller: 'resignation',
        employee_id: employeeId
    }, function(response) {
        if (response.success && response.last_attendance_date) {
            $('#lastWorkingDate').val(response.last_attendance_date);
        } else {
            // If no attendance date found, clear the field
            $('#lastWorkingDate').val('');
        }
    }, 'json').fail(function(err) {
        console.error('Error loading last attendance date:', err);
        $('#lastWorkingDate').val('');
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

function checkTerminationEligibility(employeeId) {
    $.post('exit_management.php', {
        ajax_action: 'check_termination_eligibility',
        controller: 'termination',
        employee_id: employeeId
    }, function(response) {
        const messageDiv = $('#terminationEligibilityMessage');
        messageDiv.removeClass('alert alert-success alert-danger').empty();

        if (response.success) {
            messageDiv.addClass('alert alert-success').html('<i class="fas fa-check-circle"></i> ' + response.message);
            $('#terminationSubmitBtn').prop('disabled', false);
        } else {
            messageDiv.addClass('alert alert-danger').html('<i class="fas fa-exclamation-triangle"></i> ' + response.message);
            $('#terminationSubmitBtn').prop('disabled', true);
        }
        messageDiv.show();
    }, 'json').fail(function(err) {
        console.error('Error checking termination eligibility:', err);
        $('#terminationEligibilityMessage').removeClass('alert alert-success alert-danger')
            .addClass('alert alert-warning')
            .html('<i class="fas fa-exclamation-triangle"></i> Unable to check eligibility. Please try again.')
            .show();
        $('#terminationSubmitBtn').prop('disabled', false);
    });
}

// Load employees for dropdowns
function loadEmployees(callback) {
    console.log('[LOAD EMPLOYEES] Starting employee load...');
    $.post('exit_management.php', {
        ajax_action: 'get_eligible_employees'
    }, function(response) {
        console.log('[LOAD EMPLOYEES] Response received:', response);
        console.log('[LOAD EMPLOYEES] Response type:', typeof response);
        console.log('[LOAD EMPLOYEES] Is array:', Array.isArray(response));
        
        if (response && Array.isArray(response) && response.length > 0) {
            console.log('[LOAD EMPLOYEES] Employee count:', response.length);
            const employeeOptions = '<option value="">Select Employee</option>' +
                response.map(emp => {
                    console.log('[LOAD EMPLOYEES] Processing employee:', emp);
                    return `<option value="${emp.id}">${emp.full_name} (${emp.username})</option>`;
                }).join('');

            console.log('[LOAD EMPLOYEES] Generated options HTML:', employeeOptions.substring(0, 200));
            $('#employeeSelect, #terminationEmployeeSelect, #interviewEmployeeSelect, #documentEmployeeSelect').html(employeeOptions);
            console.log('[LOAD EMPLOYEES] Dropdown populated successfully');
        } else {
            console.warn('[LOAD EMPLOYEES] No employees returned or not an array:', response);
            // Set empty state message
            $('#employeeSelect, #terminationEmployeeSelect').html('<option value="">No employees available</option>');
        }

        if (typeof callback === 'function') {
            callback();
        }
    }, 'json').fail(function(err) {
        console.error('[LOAD EMPLOYEES] AJAX request failed:', err);
        console.error('[LOAD EMPLOYEES] Status:', err.status);
        console.error('[LOAD EMPLOYEES] Status Text:', err.statusText);
        console.error('[LOAD EMPLOYEES] Response Text:', err.responseText);
        $('#employeeSelect').html('<option value="">Error loading employees</option>');
        if (typeof callback === 'function') {
            callback();
        }
    });
}

// Load employees with resignations for exit interview modal
function updateInterviewSubmitState() {
    const selectedCase = $('#interviewCaseSelect').val();
    const selectedInterviewer = $('#interviewerSelect').val();
    const scheduledDate = $('#interviewDate').val();
    const interviewTime = buildInterviewTimeValue();
    const hasDateTime = scheduledDate && interviewTime;
    const canSubmit = !!selectedCase && !!selectedInterviewer && !!hasDateTime;

    if (!selectedCase) {
        $('#interviewCaseHelpText').text('Please select an approved exit case before scheduling the interview.').show();
    } else {
        $('#interviewCaseHelpText').hide();
    }

    $('#interviewSubmitBtn').prop('disabled', !canSubmit);
}

function loadApprovedExitCases(callback) {
    $.post('exit_management.php', {
        ajax_action: 'get_approved_exit_cases',
        controller: 'exit_management'
    }, function(response) {
        console.debug('Approved cases response raw:', response);
        const cases = Array.isArray(response)
            ? response
            : (response && Array.isArray(response.data)
                ? response.data
                : (response && Array.isArray(response.cases)
                    ? response.cases
                    : (response && typeof response === 'object'
                        ? Object.values(response).find(value => Array.isArray(value)) || []
                        : [])));

        if (response && response.success === false) {
            console.error('Approved cases fetch failed:', response.message || response);
        }

        if (cases && cases.length > 0) {
            const caseOptions = '<option value="">Select Approved Exit Case</option>' +
                cases.map(emp => {
                    const exitDate = emp.exit_date || emp.last_working_date || emp.effective_date || '';
                    const exitType = emp.exit_case_type ? emp.exit_case_type.charAt(0).toUpperCase() + emp.exit_case_type.slice(1) : '';
                    return `
                        <option value="${emp.exit_case_type}:${emp.exit_case_id}"
                                data-employee-id="${emp.employee_id}">
                            ${emp.full_name} (${emp.username}) - ${exitType} - ${emp.exit_reason || ''} (${exitDate})
                        </option>
                    `;
                }).join('');

            $('#interviewCaseSelect').html(caseOptions);
        } else {
            console.warn('No approved exit cases returned', response);
            $('#interviewCaseSelect').html('<option value="">No approved exit cases found</option>');
        }

        $('#interviewCaseSelect').off('change').on('change', function() {
            const selected = $(this).val();
            if (selected) {
                const [caseType, caseId] = selected.split(':');
                const employeeId = $(this).find('option:selected').data('employee-id');
                $('#interviewExitCaseType').val(caseType);
                $('#interviewExitCaseId').val(caseId);
                $('#interviewEmployeeId').val(employeeId);
            } else {
                $('#interviewExitCaseType').val('');
                $('#interviewExitCaseId').val('');
                $('#interviewEmployeeId').val('');
            }
            updateInterviewSubmitState();
        });

        updateInterviewSubmitState();
        if (typeof callback === 'function') callback();
    }, 'json').fail(function(err) {
        console.error('Error loading approved exit cases:', err);
        $('#interviewCaseSelect').html('<option value="">Error loading exit cases</option>');
        updateInterviewSubmitState();
        if (typeof callback === 'function') callback();
    });
}

// Load interviewers for interview modal
function loadInterviewers(callback) {
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
        if (typeof callback === 'function') callback();
    }, 'json').fail(function(err) {
        console.error('Error loading interviewers:', err);
        $('#interviewerSelect').html('<option value="">Error loading interviewers</option>');
        if (typeof callback === 'function') callback();
    });
}

// Load employees with resignations for transfer modal (employee leaving)
function loadEmployeesWithResignationsForTransfers(callback) {
    $.post('exit_management.php', {
        ajax_action: 'get_employees_with_resignations'
    }, function(response) {
        if (response && response.length > 0) {
            const employeeOptions = '<option value="">Select Employee Leaving</option>' +
                response.map(emp => {
                    const resignationLabel = emp.resignation_type ? ` (${emp.resignation_type})` : '';
                    const workingDateLabel = emp.last_working_date ? ` - LW: ${emp.last_working_date}` : '';
                    return `<option value="${emp.id}">${emp.full_name} (${emp.username})${resignationLabel}${workingDateLabel}</option>`;
                }).join('');

            $('#transferEmployeeSelect').html(employeeOptions);
        } else {
            $('#transferEmployeeSelect').html('<option value="">No employees with resignations found</option>');
        }
        if (typeof callback === 'function') callback();
    }).fail(function(err) {
        console.error('Error loading resigning employees for transfers:', err);
        $('#transferEmployeeSelect').html('<option value="">Error loading employees</option>');
        if (typeof callback === 'function') callback();
    });
}

// Load successors for transfer modal
function loadSuccessors(callback) {
    $.post('exit_management.php', {
        ajax_action: 'get_eligible_employees'
    }, function(response) {
        if (response && response.length > 0) {
            const successorOptions = '<option value="">Select Successor</option>' +
                response.map(emp => {
                    const positionLabel = emp.position ? ` (${emp.position})` : ' (No position assigned)';
                    return `<option value="${emp.id}">${emp.full_name} (${emp.username})${positionLabel}</option>`;
                }).join('');

            $('#successorSelect').html(successorOptions);
        } else {
            $('#successorSelect').html('<option value="">No employees available</option>');
        }
        if (typeof callback === 'function') callback();
    }, 'json').fail(function(err) {
        console.error('Error loading employees for successors:', err);
        $('#successorSelect').html('<option value="">Error loading employees</option>');
        if (typeof callback === 'function') callback();
    });
}

// Load approved exit cases for settlement modal
function loadApprovedExitCasesForSettlements(callback) {
    $.post('exit_management.php', {
        ajax_action: 'get_approved_exit_cases',
        controller: 'exit_management'
    }, function(response) {
        const cases = Array.isArray(response) ? response : (response && Array.isArray(response.data) ? response.data : []);

        if (cases && cases.length > 0) {
            const caseOptions = '<option value="">Select Approved Exit Case</option>' +
                cases.map(emp => {
                    const exitType = emp.exit_case_type ? emp.exit_case_type.charAt(0).toUpperCase() + emp.exit_case_type.slice(1) : '';
                    const exitDate = emp.exit_date || emp.last_working_date || '';
                    return `<option value="${emp.exit_case_type}:${emp.exit_case_id}" data-employee-id="${emp.employee_id}" data-exit-case-type="${emp.exit_case_type}" data-exit-case-id="${emp.exit_case_id}" data-resignation-id="${emp.exit_case_type === 'resignation' ? emp.exit_case_id : ''}">${emp.full_name} (${emp.username}) - ${exitType}${exitDate ? ' - ' + exitDate : ''}</option>`;
                }).join('');

            $('#settlementCaseSelect').html(caseOptions);
        } else {
            $('#settlementCaseSelect').html('<option value="">No approved exit cases found</option>');
        }

        if (typeof callback === 'function') callback();
    }, 'json').fail(function(err) {
        console.error('Error loading approved exit cases for settlements:', err);
        $('#settlementCaseSelect').html('<option value="">Error loading exit cases</option>');
        if (typeof callback === 'function') callback();
    });
}

// Modal display functions
function showResignationModal(resignationId = null) {
    if (!resignationId) {
        showToast('warning', 'Resignation creation is managed through the Employee Portal.');
        return;
    }

    console.log('Opening resignation modal for ID:', resignationId);
    $('#resignationModalTitle').text('Review Resignation');
    $('#resignationForm')[0].reset();
    $('#resignationId').val(resignationId);
    $('#approvalSection').show();
    $('#eligibilityMessage').hide();
    $('#resignationForm').find('input, textarea, select').prop('disabled', true);
    $('#resignationId').prop('disabled', false);
    $('#approvalSection').find('select, textarea').prop('disabled', false);
    $('#resignationSubmitBtn').show().prop('disabled', false).text('Save Decision');
    $('#resignationModal').modal('show');

    loadEmployees(function() {
        loadResignationData(resignationId);
    });
}

function showInterviewModal(interviewId = null, viewOnly = false) {
    if (interviewId) {
        $('#interviewModalTitle').text(viewOnly ? 'View Exit Interview' : 'Edit Exit Interview');
        $('#interviewForm')[0].reset();
        $('#interviewId').val('');
        $('#interviewExitCaseType').val('');
        $('#interviewExitCaseId').val('');
        $('#interviewEmployeeId').val('');
        setInterviewModalMode(viewOnly);
        loadApprovedExitCases(function() {
            loadInterviewers(function() {
                loadInterviewData(interviewId, viewOnly);
                $('#interviewModal').modal('show');
            });
        });
    } else {
        $('#interviewModalTitle').text('Schedule Exit Interview');
        $('#interviewForm')[0].reset();
        $('#interviewId').val('');
        $('#feedbackSection').hide();
        $('#interviewExitCaseType').val('');
        $('#interviewExitCaseId').val('');
        $('#interviewEmployeeId').val('');
        // hide read-only and HR sections for new schedules
        $('#employeeInfoSection').hide();
        $('#exitCaseInfoSection').hide();
        $('#engagementSection').hide();
        $('#hrAssessmentSection').hide();
        $('#saveHrAssessmentBtn').hide();
        $('#interviewSubmitBtn').prop('disabled', true).text('Schedule Interview');
        setInterviewModalMode(false);
        loadApprovedExitCases();
        loadInterviewers();
        $('#interviewModal').modal('show');
    }
}

function showTransferModal(planId = null) {
    console.log('showTransferModal called with planId:', planId);
    let transferPlanId = planId === null || planId === undefined || planId === '' ? null : Number(planId);
    if (Number.isNaN(transferPlanId)) {
        transferPlanId = null;
    }

    if (transferPlanId !== null) {
        $('#transferModalTitle').text('View Knowledge Transfer Plan');
        $('#transferSubmitBtn').hide();
        $('#editTransferBtn').show();
        $('#transferForm')[0].reset();
        $('#transferPlanId').val('');
        $('#transferItemsContainer').html(getTransferItemTemplate(0));
        setTransferModalMode(true);

        loadEmployeesWithResignationsForTransfers(function() {
            loadSuccessors(function() {
                loadTransferData(transferPlanId, true, function() {
                    console.log('Showing transfer modal after load for planId:', transferPlanId);
                    $('#transferModal').modal('show');
                });
            });
        });
    } else {
        $('#transferModalTitle').text('Create Knowledge Transfer Plan');
        $('#transferSubmitBtn').show().text('Create Transfer Plan');
        $('#editTransferBtn').hide();
        $('#transferForm')[0].reset();
        $('#transferPlanId').val('');
        $('#transferItemsContainer').html(getTransferItemTemplate(0));
        setTransferModalMode(false);
        loadEmployeesWithResignationsForTransfers();
        loadSuccessors();
        $('#transferModal').modal('show');
    }
}

function setSettlementModalMode(viewOnly = false) {
    const $formFields = $('#settlementForm').find('input:not([type=hidden]), textarea, select');
    $formFields.prop('disabled', viewOnly);

    $('#calculateNetPayable').hide();

    if (viewOnly) {
        $('#settlementSubmitBtn').hide();
        $('#settlementEditBtn').show();
        $('#settlementForm').find('.form-control').addClass('disabled');
    } else {
        $('#settlementSubmitBtn').show();
        $('#settlementEditBtn').hide();
        $('#settlementForm').find('.form-control').removeClass('disabled');

        if ($('#settlementId').val()) {
            $('#settlementSubmitBtn').text('Save Changes');
        } else {
            $('#settlementSubmitBtn').text('Request Settlement');
        }
    }
}

function showSettlementModal(settlementId = null, viewOnly = false) {
    $('#settlementCaseSelect').off('change').on('change', function() {
        const selected = $(this).val();
        if (selected) {
            const [caseType, caseId] = selected.split(':');
            const employeeId = $(this).find('option:selected').data('employee-id');
            const resignationId = $(this).find('option:selected').data('resignation-id') || '';
            $('#settlementEmployeeId').val(employeeId);
            $('#settlementExitCaseType').val(caseType);
            $('#settlementExitCaseId').val(caseId);
            $('#settlementResignationId').val(resignationId);

            if (employeeId) {
                loadEmployeeSalaryComponents(employeeId);
            } else {
                clearSalaryFields();
            }
        } else {
            $('#settlementEmployeeId').val('');
            $('#settlementExitCaseType').val('');
            $('#settlementExitCaseId').val('');
            $('#settlementResignationId').val('');
            clearSalaryFields();
        }
    });

    $('#settlementForm')[0].reset();
    $('#settlementId').val('');
    $('#settlementModalTitle').text(viewOnly ? 'View Settlement' : (settlementId ? 'Edit Settlement' : 'Request Final Settlement'));
    setSettlementModalMode(viewOnly);

    const loadSequence = function(callback) {
        loadApprovedExitCasesForSettlements(callback);
    };

    if (settlementId) {
        loadSequence(function() {
            loadSettlementData(settlementId, viewOnly, function() {
                $('#settlementModal').appendTo('body').modal('show');
            });
        });
    } else {
        loadSequence(function() {
            $('#settlementModal').appendTo('body').modal('show');
        });
    }
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

    if (!resignationId) {
        showToast('error', 'Resignation creation is managed through the Employee Portal.');
        return;
    }

    // Add action and controller parameters
    formData.append('ajax_action', 'update_resignation');
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
    
    const interviewTime = buildInterviewTimeValue();
    if (interviewTime) {
        formData.set('scheduled_time', interviewTime);
    }
    
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

// Save HR assessment via AJAX
function saveHrAssessment(interviewId) {
    const assessment = {
        summary: $('#hrSummary').val(),
        key_findings: $('#hrKeyFindings').val(),
        hr_recommendations: $('#hrRecommendations').val(),
        follow_up_actions: $('#hrFollowUpActions').val(),
        rehire_eligibility: $('#hrRehireEligibility').val(),
        knowledge_transfer_required: $('#hrKnowledgeTransfer').is(':checked') ? 1 : 0,
        clearance_recommendation: $('#hrClearanceRecommendation').val()
    };

    $('#saveHrAssessmentBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.post('exit_management.php', {
        ajax_action: 'save_hr_assessment',
        controller: 'interview',
        interview_id: interviewId,
        assessment: assessment
    }, function(response) {
        if (response && response.success) {
            showToast('success', response.message || 'HR assessment saved');
            // reload interview data
            loadInterviewData(interviewId);
        } else {
            showToast('error', response ? response.message : 'Failed to save HR assessment');
        }
    }, 'json').fail(function(err) {
        console.error('Error saving HR assessment:', err);
        showToast('error', 'An error occurred while saving HR assessment');
    }).always(function() {
        $('#saveHrAssessmentBtn').prop('disabled', false).html('Save HR Assessment');
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
            const transferPlanId = $('#transferPlanId').val();
            const buttonText = transferPlanId ? 'Save Changes' : 'Create Transfer Plan';
            $('#transferSubmitBtn').prop('disabled', false).html(buttonText);
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
            const buttonText = settlementId ? 'Save Changes' : 'Request Settlement';
            $('#settlementSubmitBtn').prop('disabled', false).html(buttonText);
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
function getTransferItemTemplate(index, item = {}) {
    const selectedType = item.type || item.item_type || '';
    const titleValue = item.title || '';
    const priorityValue = item.priority || 'medium';
    const descriptionValue = item.description || '';
    const notesValue = item.notes || '';
    const itemIdField = item.id ? `<input type="hidden" name="items[${index}][id]" value="${item.id}">` : '';

    return `
        <div class="transfer-item mb-3 p-3 border rounded">
            ${itemIdField}
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" name="items[${index}][type]" required>
                        <option value="">Select Type</option>
                        <option value="process" ${selectedType === 'process' ? 'selected' : ''}>Process</option>
                        <option value="system" ${selectedType === 'system' ? 'selected' : ''}>System</option>
                        <option value="contact" ${selectedType === 'contact' ? 'selected' : ''}>Contact</option>
                        <option value="document" ${selectedType === 'document' ? 'selected' : ''}>Document</option>
                        <option value="other" ${selectedType === 'other' ? 'selected' : ''}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="items[${index}][title]" placeholder="Title" value="${titleValue.replace(/"/g, '&quot;')}" required>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="items[${index}][priority]">
                        <option value="medium" ${priorityValue === 'medium' ? 'selected' : ''}>Medium</option>
                        <option value="low" ${priorityValue === 'low' ? 'selected' : ''}>Low</option>
                        <option value="high" ${priorityValue === 'high' ? 'selected' : ''}>High</option>
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
                    <textarea class="form-control" name="items[${index}][description]" rows="2" placeholder="Description">${descriptionValue.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</textarea>
                </div>
                <div class="col-12">
                    <textarea class="form-control" name="items[${index}][notes]" rows="2" placeholder="Notes">${notesValue.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</textarea>
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
function loadResignationData(id, callback) {
    // Load resignation data for editing
    $.post('exit_management.php', {
        ajax_action: 'get_resignation',
        controller: 'resignation',
        resignation_id: id
    }, function(response) {
        if (response) {
            Object.keys(response).forEach(key => {
                if (key === 'employee_id') {
                    $('#employeeSelect').val(response[key]);
                } else {
                    const $field = $(`#${key}`);
                    if ($field.length) {
                        $field.val(response[key]);
                    } else {
                        const $namedField = $(`[name="${key}"]`);
                        if ($namedField.length) {
                            $namedField.val(response[key]);
                        }
                    }
                }
            });

            if (response.hr_approval_comments) {
                $('#approvalComments').val(response.hr_approval_comments);
            }
            if (response.legal_approval_comments && !response.hr_approval_comments) {
                $('#approvalComments').val(response.legal_approval_comments);
            }

            renderResignationApprovalOptions(response.status, response);
        }

        if (typeof callback === 'function') {
            callback();
        }
    }, 'json');
}

function renderResignationApprovalOptions(status, response = {}) {
    const $approvalSection = $('#approvalSection');
    const $approvalStatus = $('#approvalStatus');
    const $approvalComments = $('#approvalComments');
    const $submitBtn = $('#resignationSubmitBtn');

    $approvalStatus.empty();
    $approvalComments.prop('disabled', false);
    $approvalStatus.prop('disabled', false);
    $submitBtn.show().prop('disabled', false);

    if (status === 'pending_review') {
        $('#resignationModalTitle').text('Review Resignation');
        $approvalStatus.append(`
            <option value="pending_legal_review">Approve HR review (send to Legal)</option>
            <option value="rejected">Reject</option>
        `);
        $approvalSection.show();
        $submitBtn.text('Save Decision');
    } else if (status === 'pending_legal_review') {
        $('#resignationModalTitle').text('Legal Review');
        $approvalStatus.append(`
            <option value="approved">Approve Final</option>
            <option value="rejected_by_legal">Reject by Legal</option>
        `);
        $approvalSection.show();
        $submitBtn.text('Save Decision');
    } else {
        $('#resignationModalTitle').text('View Resignation');
        $approvalStatus.append(`<option value="${status}">${status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</option>`);
        $approvalStatus.prop('disabled', true);
        $approvalComments.prop('disabled', true);
        $submitBtn.hide();
        $approvalSection.show();
    }

    // Keep all resignation fields read-only
    $('#resignationForm').find('input, textarea, select').prop('disabled', true);
    $('#resignationId').prop('disabled', false);
    $approvalStatus.prop('disabled', status !== 'pending_review' && status !== 'pending_legal_review');
    $approvalComments.prop('disabled', status !== 'pending_review' && status !== 'pending_legal_review');
}

function loadInterviewData(id, viewOnly = false) {
    // Load interview data for viewing or editing
    console.log('Loading interview data for ID:', id, 'viewOnly=', viewOnly);
    $.post('exit_management.php', {
        ajax_action: 'get_interview',
        controller: 'interview',
        interview_id: id
    }, function(response) {
        console.log('Interview response:', response);
        if (response && response.data && !response.error) {
            response = response.data;
        }
        if (response && !response.error) {
            response.id = response.id || id;
            $('#interviewId').val(response.id);
            const caseValue = response.exit_case_type && response.exit_case_id ? `${response.exit_case_type}:${response.exit_case_id}` : '';
            if (caseValue) {
                $('#interviewCaseSelect').val(caseValue).trigger('change');
                $('#interviewCaseDisplay').text($('#interviewCaseSelect option:selected').text());
            } else {
                const caseText = response.exit_case_type && response.exit_case_id
                    ? `${response.exit_case_type}:${response.exit_case_id}`
                    : 'Not available';
                $('#interviewCaseDisplay').text(caseText);
            }

            Object.keys(response).forEach(key => {
                if (key === 'exit_case_type' || key === 'exit_case_id' || key === 'employee_id') {
                    return;
                } else if (key === 'interviewer_id') {
                    $('#interviewerSelect').val(response[key]);
                } else if (key === 'scheduled_date') {
                    $('#interviewDate').val(response[key]);
                } else if (key === 'scheduled_time') {
                    if (response[key]) {
                        populateInterviewTimeFields(response[key]);
                    }
                } else {
                    const $field = $(`#${key}`);
                    if ($field.length) {
                        $field.val(response[key]);
                    } else {
                        const $namedField = $(`[name="${key}"]`);
                        if ($namedField.length) {
                            $namedField.val(response[key]);
                        }
                    }
                }
            });

            // Populate read-only Employee Info if present
            if (response.employee_full_name) {
                $('#employeeFullName').text(response.employee_full_name);
                $('#employeeDepartment').text(response.employee_department || '');
                $('#employeePosition').text(response.employee_position || '');
                $('#employeeDateHired').text(response.employee_date_hired || '');
                // calculate years of service
                if (response.employee_date_hired) {
                    const hired = new Date(response.employee_date_hired);
                    const now = new Date();
                    const years = now.getFullYear() - hired.getFullYear();
                    $('#employeeYearsOfService').text(years + ' years');
                } else {
                    $('#employeeYearsOfService').text('');
                }
                $('#employeeManager').text(response.manager_name || '');
                $('#employeeInfoSection').show();
            }

            // Populate Exit Case Info from enriched exit_case_details if available
            const caseDetails = response.exit_case_details || null;
            if (caseDetails) {
                $('#exitCaseReason').text(caseDetails.exit_reason || '');
                $('#exitCaseNoticeDate').text(caseDetails.notice_date || '');
                $('#exitCaseDate').text(caseDetails.last_working_date || caseDetails.effective_date || '');
                const approvedBy = caseDetails.approved_by_name ? `${caseDetails.approved_by_name} @ ${caseDetails.approved_at || ''}` : '';
                $('#exitCaseApproved').text(approvedBy);
                $('#exitCaseInfoSection').show();
            } else if (response.exit_reason || response.exit_date || response.notice_date) {
                $('#exitCaseReason').text(response.exit_reason || '');
                $('#exitCaseNoticeDate').text(response.notice_date || '');
                $('#exitCaseDate').text(response.exit_date || response.termination_effective_date || '');
                const approvedBy = response.case_approved_by ? (response.case_approved_by + ' @ ' + (response.case_approved_at || '')) : '';
                $('#exitCaseApproved').text(approvedBy);
                $('#exitCaseInfoSection').show();
            }

            // Populate Engagement panel with available records
            const engagementRecords = response.engagement_records || {};
            let engagementHtml = '<p class="text-muted">No engagement or survey records available.</p>';

            if (engagementRecords.exit_surveys && engagementRecords.exit_surveys.length) {
                engagementHtml = '<div class="list-group">';
                engagementRecords.exit_surveys.slice(0, 3).forEach(survey => {
                    engagementHtml += `<div class="list-group-item py-2 d-flex justify-content-between align-items-start">
                        <div>
                            <div class="font-weight-bold">${survey.survey_title || survey.title || 'Exit Survey'}</div>
                            <div class="text-muted small">Submitted ${survey.submitted_at || ''}</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewSurveyResponseDetails(${survey.response_id})">View Responses</button>
                    </div>`;
                });
                if (engagementRecords.exit_surveys.length > 3) {
                    engagementHtml += `<div class="list-group-item text-center text-muted py-2">And ${engagementRecords.exit_surveys.length - 3} more response(s)</div>`;
                }
                engagementHtml += '</div>';
            } else if (engagementRecords.grievances && engagementRecords.grievances.length) {
                engagementHtml = '<div class="list-group">';
                engagementRecords.grievances.slice(0, 3).forEach(grievance => {
                    engagementHtml += `<div class="list-group-item py-2">
                        <div class="font-weight-bold">${grievance.subject || 'Grievance'}</div>
                        <div class="text-muted small">Status: ${grievance.status || 'N/A'} · Updated ${grievance.updated_at || grievance.created_at || ''}</div>
                    </div>`;
                });
                if (engagementRecords.grievances.length > 3) {
                    engagementHtml += `<div class="list-group-item text-center text-muted py-2">And ${engagementRecords.grievances.length - 3} more grievance(s)</div>`;
                }
                engagementHtml += '</div>';
            } else if (engagementRecords.feedback_history && engagementRecords.feedback_history.length) {
                engagementHtml = '<div class="list-group">';
                engagementRecords.feedback_history.slice(0, 3).forEach(feedback => {
                    engagementHtml += `<div class="list-group-item py-2">
                        <div class="font-weight-bold">Interview ${feedback.interview_id} (${feedback.status || ''})</div>
                        <div class="text-muted small">Rating: ${feedback.overall_satisfaction || 'N/A'} · ${feedback.submitted_at || ''}</div>
                    </div>`;
                });
                if (engagementRecords.feedback_history.length > 3) {
                    engagementHtml += `<div class="list-group-item text-center text-muted py-2">And ${engagementRecords.feedback_history.length - 3} more feedback record(s)</div>`;
                }
                engagementHtml += '</div>';
            }

            $('#engagementPlaceholder').html(engagementHtml);
            $('#engagementSection').show();

            // Populate HR assessment if present
            if (response.hr_assessment) {
                const hr = response.hr_assessment;
                $('#hrSummary').val(hr.summary || '');
                $('#hrKeyFindings').val(hr.key_findings || '');
                $('#hrRecommendations').val(hr.hr_recommendations || '');
                $('#hrFollowUpActions').val(hr.follow_up_actions || '');
                $('#hrRehireEligibility').val(hr.rehire_eligibility || '');
                $('#hrKnowledgeTransfer').prop('checked', hr.knowledge_transfer_required == 1 || hr.knowledge_transfer_required === '1');
                $('#hrClearanceRecommendation').val(hr.clearance_recommendation || 'pending');
                $('#hrAssessmentSection').show();
            } else {
                // show empty HR section when editing
                $('#hrAssessmentSection').show();
            }

            if (canManageHrAssessment()) {
                $('#saveHrAssessmentBtn').show();
            } else {
                $('#saveHrAssessmentBtn').hide();
            }

            setInterviewModalMode(viewOnly);
            if (!viewOnly && $('#interviewId').val()) {
                $('#interviewSubmitBtn').text('Save Changes');
            }
        } else {
            console.error('Error loading interview:', response);
        }
    }, 'json').fail(function(err) {
        console.error('AJAX error loading interview:', err);
    });
}

function loadTransferData(id, viewOnly = false, callback = null) {
    // Load transfer plan data for editing or viewing
    console.log('Loading transfer plan data for ID:', id, 'viewOnly=', viewOnly);
    $.post('exit_management.php', {
        ajax_action: 'get_transfer_plan',
        controller: 'transfer',
        plan_id: id
    }, function(response) {
        console.log('Transfer response:', response);
        const plan = response && response.success && response.data ? response.data : response;

        if (plan && !plan.error) {
            $('#transferPlanId').val(plan.id || plan.plan_id || plan.planId || id);
            $('#transferEmployeeSelect').val(plan.employee_id || '');
            $('#successorSelect').val(plan.successor_id || '');
            setDateInputValue('#transferStartDate', plan.start_date);
            setDateInputValue('#transferEndDate', plan.end_date);

            if (Array.isArray(plan.items) && plan.items.length > 0) {
                let itemsHtml = '';
                plan.items.forEach((item, idx) => {
                    itemsHtml += getTransferItemTemplate(idx);
                });
                $('#transferItemsContainer').html(itemsHtml);
                plan.items.forEach((item, idx) => {
                    const $itemBlock = $('#transferItemsContainer .transfer-item').eq(idx);
                    if (!$itemBlock.length) return;
                    $itemBlock.find(`select[name="items[${idx}][type]"]`).val(item.item_type || item.type || '');
                    $itemBlock.find(`input[name="items[${idx}][title]"]`).val(item.title || '');
                    $itemBlock.find(`select[name="items[${idx}][priority]"]`).val(item.priority || 'medium');
                    $itemBlock.find(`textarea[name="items[${idx}][description]"]`).val(item.description || '');
                    $itemBlock.find(`textarea[name="items[${idx}][notes]"]`).val(item.notes || '');
                });
            } else {
                $('#transferItemsContainer').html(getTransferItemTemplate(0));
            }

            if (viewOnly) {
                $('#transferModalTitle').text('View Knowledge Transfer Plan');
                $('#transferSubmitBtn').hide();
                $('#editTransferBtn').show();
            } else {
                $('#transferModalTitle').text('Edit Knowledge Transfer Plan');
                $('#transferSubmitBtn').show().text('Save Changes');
                $('#editTransferBtn').hide();
            }

            setTransferModalMode(viewOnly);
            if (typeof callback === 'function') {
                callback();
            }
        } else {
            console.error('Error loading transfer:', response);
            if (typeof callback === 'function') {
                callback();
            }
        }
    }, 'json').fail(function(err) {
        console.error('AJAX error loading transfer:', err);
        if (typeof callback === 'function') {
            callback();
        }
    });
}

function loadSettlementData(id, viewOnly = false, callback = null) {
    console.log('Loading settlement data for ID:', id);
    $.post('exit_management.php', {
        ajax_action: 'get_settlement',
        controller: 'settlement',
        settlement_id: id
    }, function(response) {
        console.log('Settlement response:', response);
        if (response && !response.error) {
            $('#settlementId').val(response.id || id);
            let selectedCaseValue = '';

            Object.keys(response).forEach(key => {
                if (key === 'employee_id') {
                    $('#settlementEmployeeId').val(response[key]);
                    loadEmployeeSalaryComponents(response[key]);
                } else if (key === 'exit_case_type' || key === 'exit_case_id') {
                    const fieldId = key === 'exit_case_type' ? '#settlementExitCaseType' : '#settlementExitCaseId';
                    $(fieldId).val(response[key]);
                    if (response.exit_case_type && response.exit_case_id) {
                        selectedCaseValue = `${response.exit_case_type}:${response.exit_case_id}`;
                    }
                } else if (key === 'resignation_id') {
                    $('#settlementResignationId').val(response[key] || '');
                } else {
                    const $field = $(`#${key}`);
                    if ($field.length) {
                        $field.val(response[key]);
                    } else {
                        const $namedField = $(`[name="${key}"]`);
                        if ($namedField.length) {
                            $namedField.val(response[key]);
                        }
                    }
                }
            });

            if (selectedCaseValue) {
                $('#settlementCaseSelect').val(selectedCaseValue);
            }

            populatePayrollSettlementSummary(response, viewOnly);
        } else {
            console.error('Error loading settlement:', response);
        }

        setSettlementModalMode(viewOnly);
        if (typeof callback === 'function') {
            callback();
        }
    }, 'json').fail(function(err) {
        console.error('AJAX error loading settlement:', err);
        setSettlementModalMode(viewOnly);
        if (typeof callback === 'function') {
            callback();
        }
    });
}

function loadDocumentData(id) {
    // Load document data for editing
    $.post('exit_management.php', {
        ajax_action: 'get_document',
        controller: 'document',
        document_id: id
    }, function(response) {
        if (response) {
            Object.keys(response).forEach(key => {
                const $field = $(`#${key}`);
                if ($field.length) {
                    $field.val(response[key]);
                } else {
                    const $namedField = $(`[name="${key}"]`);
                    if ($namedField.length) {
                        $namedField.val(response[key]);
                    }
                }
            });
        }
    }, 'json');
}

function loadSurveyData(id) {
    // Load survey data for editing
    $.post('exit_management.php', {
        ajax_action: 'get_survey',
        controller: 'survey',
        survey_id: id
    }, function(response) {
        if (response) {
            Object.keys(response).forEach(key => {
                const $field = $(`#${key}`);
                if ($field.length) {
                    $field.val(response[key]);
                } else {
                    const $namedField = $(`[name="${key}"]`);
                    if ($namedField.length) {
                        $namedField.val(response[key]);
                    }
                }
            });
        }
    }, 'json');
}

function createTableLoaderRow(colspan, message = 'Loading...') {
    return `
        <tr class="table-loading-row">
            <td colspan="${colspan}" class="text-center py-4">
                <div class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></div>
                ${message}
            </td>
        </tr>
    `;
}

function showTableLoading(tbody, colspan, message = 'Loading...') {
    tbody.html(createTableLoaderRow(colspan, message));
}

function escapeJsString(value) {
    return String(value)
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'")
        .replace(/\n/g, '\\n')
        .replace(/\r/g, '\\r');
}

// Table loading functions
let archivedResignationsData = [];
let archivedResignationPage = 1;
const archivedResignationPageSize = 10;

function loadResignationsTable(status = 'active', page = 1, searchTerm = '') {
    console.log('[loadResignationsTable] status:', status, 'page:', page, 'search:', searchTerm);
    let apiStatus = status;
    if (status === 'active' || status === null) {
        apiStatus = null;
    } else if (status === 'all') {
        apiStatus = 'all';
    }

    const payload = {
        ajax_action: 'get_resignations',
        controller: 'resignation',
        status: apiStatus,
        page: page,
        limit: 10,
        search: searchTerm
    };
    console.log('[loadResignationsTable] apiStatus:', apiStatus, 'payload:', JSON.stringify(payload));

    if (status === 'archived') {
        // keep archived in separate table via dedicated function
        toggleArchivedResignations(true);
        return;
    } else {
        $('#archived-resignations-container').hide();
        $('#toggle-archived-resignations').text('Show Archived');
    }

    const tbody = $('#resignations-tbody');
    showTableLoading(tbody, 11);

    $.post('exit_management.php', payload, function(response) {
        console.log('[loadResignationsTable] Response:', response);
        const tbody = $('#resignations-tbody');
        tbody.empty();

        if (response && response.data && response.data.length > 0) {
            console.log('[loadResignationsTable] Display', response.data.length, 'records');
            response.data.forEach(function(resignation) {
                const statusBadge = getStatusBadge(resignation.status);
                const fs = resignation.archive_reason ? ` (${resignation.archive_reason})` : (resignation.archived_from_status ? ` (from ${resignation.archived_from_status})` : '');
                const tooltip = resignation.archive_reason ? `Archive reason: ${resignation.archive_reason}` : (resignation.archived_from_status ? `Archived from status: ${resignation.archived_from_status}` : '');

                const actions = `
                    <div class="table-actions">
                        <button type="button" class="btn btn-sm btn-info action-button" onclick="showResignationModal(${resignation.id})" title="Review Resignation" aria-label="Review Resignation" data-title="Review Resignation">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${resignation.status === 'archived' ? `
                        <button type="button" class="btn btn-sm btn-success action-button" onclick="unarchiveResignation(${resignation.id})" title="Unarchive Resignation" aria-label="Unarchive Resignation" data-title="Unarchive Resignation">
                            <i class="fas fa-undo"></i>
                        </button>
                        ` : `
                        <button type="button" class="btn btn-sm btn-secondary action-button" onclick="archiveResignation(${resignation.id})" title="Archive Resignation" aria-label="Archive Resignation" data-title="Archive Resignation">
                            <i class="fas fa-archive"></i>
                        </button>
                        `}
                    </div>
                `;

                tbody.append(`
                    <tr title="${tooltip}">
                        <td>${resignation.employee_name || '<em class="text-danger">Missing Employee</em>'}</td>
                        <td>${resignation.department || '-'}</td>
                        <td>${resignation.email || '-'}</td>
                        <td>${resignation.position || '-'}</td>
                        <td>${resignation.reason || '-'}</td>
                        <td>${resignation.notice_date || '-'}</td>
                        <td>${resignation.last_working_date || '-'}</td>
                        <td>${resignation.comments ? resignation.comments.substring(0, 50) + '...' : '-'}</td>
                        <td class="status-cell">${statusBadge}</td>
                        <td class="actions-cell">${actions}</td>
                    </tr>
                `);
            });

            // Add pagination controls
            renderResignationsPagination('resignations-pagination', response, status, page, searchTerm);
        } else {
            console.log('[loadResignationsTable] No records found');
            tbody.append('<tr><td colspan="10" class="text-center">No resignations found</td></tr>');
            $('#resignations-pagination').empty();
        }
    }).fail(function(xhr, jqStatus, error) {
        console.error('[loadResignationsTable] AJAX error. jqStatus:', jqStatus, 'error:', error, 'response:', xhr.responseText);
        $('#resignations-tbody').html('<tr><td colspan="10" class="text-center text-danger">Error loading resignations. Check console for details.</td></tr>');
        $('#resignations-pagination').empty();
    });
}

function loadTerminationsTable(status = 'active', page = 1, searchTerm = '') {
    console.log('[loadTerminationsTable] status:', status, 'page:', page, 'search:', searchTerm);
    let apiStatus = status;
    if (status === 'active' || status === null) {
        apiStatus = null;
    } else if (status === 'all') {
        apiStatus = 'all';
    }

    const payload = {
        ajax_action: 'get_terminations',
        controller: 'termination',
        status: apiStatus,
        page: page,
        limit: 10,
        search: searchTerm
    };

    const tbody = $('#terminations-tbody');
    showTableLoading(tbody, 9);

    $.post('exit_management.php', payload, function(response) {
        tbody.empty();

        if (response && response.data && response.data.length > 0) {
            response.data.forEach(function(termination) {
                const statusBadge = getStatusBadge(termination.status);
                const actions = `
                    <div class="table-actions">
                        <button type="button" class="btn btn-sm btn-info action-button" onclick="showTerminationModal(${termination.id})" title="Review Termination" aria-label="Review Termination">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${termination.status === 'archived' ? `
                        <button type="button" class="btn btn-sm btn-success action-button" onclick="unarchiveTermination(${termination.id})" title="Unarchive Termination" aria-label="Unarchive Termination">
                            <i class="fas fa-undo"></i>
                        </button>
                        ` : `
                        <button type="button" class="btn btn-sm btn-secondary action-button" onclick="archiveTermination(${termination.id})" title="Archive Termination" aria-label="Archive Termination">
                            <i class="fas fa-archive"></i>
                        </button>
                        `}
                    </div>
                `;

                tbody.append(`
                    <tr>
                        <td>${termination.employee_name || '<em class="text-danger">Missing Employee</em>'}</td>
                        <td>${termination.department || '-'}</td>
                        <td>${termination.email || '-'}</td>
                        <td>${termination.position || '-'}</td>
                        <td>${termination.termination_reason || '-'}</td>
                        <td>${termination.effective_date || '-'}</td>
                        <td>${termination.comments ? termination.comments.substring(0, 50) + '...' : '-'}</td>
                        <td class="status-cell">${statusBadge}</td>
                        <td class="actions-cell">${actions}</td>
                    </tr>
                `);
            });

            renderTerminationsPagination('terminations-pagination', response, status, page, searchTerm);
        } else {
            tbody.append('<tr><td colspan="9" class="text-center">No terminations found</td></tr>');
            $('#terminations-pagination').empty();
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('[loadTerminationsTable] AJAX error:', status, error, xhr.responseText);
        tbody.html('<tr><td colspan="9" class="text-center text-danger">Error loading terminations. Check console for details.</td></tr>');
        $('#terminations-pagination').empty();
    });
}

function showTerminationModal(terminationId = null) {
    $('#terminationForm')[0].reset();
    $('#terminationId').val('');
    $('#terminationApprovalSection').hide();
    $('#terminationEligibilityMessage').hide();
    $('#terminationSubmitBtn').prop('disabled', false).text('Submit Termination');

    if (terminationId) {
        $('#terminationModalTitle').text('Review Termination');
        $('#terminationApprovalSection').show();
        $('#terminationForm input, #terminationForm textarea, #terminationForm select').prop('disabled', false);
        $('#terminationEmployeeSelect').prop('disabled', true);

        $.post('exit_management.php', {
            ajax_action: 'get_termination_details',
            controller: 'termination',
            termination_id: terminationId
        }, function(response) {
            if (response.success) {
                $('#terminationId').val(response.data.id);
                $('#terminationEmployeeSelect').val(response.data.employee_id);
                $('#terminationEffectiveDate').val(response.data.effective_date);
                $('#terminationReason').val(response.data.termination_reason);
                $('#terminationComments').val(response.data.comments || '');
                $('#terminationApprovalStatus').val(response.data.status || 'pending_review');
                $('#terminationApprovalComments').val('');
                $('#terminationModal').modal('show');
            } else {
                showToast('error', response.message || 'Unable to load termination details.');
            }
        }, 'json').fail(function(xhr, status, error) {
            console.error('Error loading termination details:', status, error, xhr.responseText);
            showToast('error', 'Error loading termination details.');
        });
    } else {
        $('#terminationModalTitle').text('Initiate Termination');
        $('#terminationModal').modal('show');
    }
}

function submitTerminationForm() {
    const terminationId = $('#terminationId').val();
    const employeeId = $('#terminationEmployeeSelect').val();
    const terminationReason = $('#terminationReason').val();
    const effectiveDate = $('#terminationEffectiveDate').val();
    const comments = $('#terminationComments').val();

    if (!employeeId || !terminationReason || !effectiveDate) {
        showToast('warning', 'Please complete all required termination fields.');
        return;
    }

    const payload = {
        ajax_action: terminationId ? 'process_termination' : 'submit_termination',
        controller: 'termination',
        employee_id: employeeId,
        termination_reason: terminationReason,
        effective_date: effectiveDate,
        comments: comments
    };

    if (terminationId) {
        payload.termination_id = terminationId;
        payload.status = $('#terminationApprovalStatus').val();
        payload.approval_comments = $('#terminationApprovalComments').val();
    }

    $.post('exit_management.php', payload, function(response) {
        if (response.success) {
            showToast('success', response.message || 'Termination saved successfully.');
            $('#terminationModal').modal('hide');
            loadTerminationsTable();
        } else {
            showToast('error', response.message || 'Failed to save termination.');
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('Error submitting termination:', status, error, xhr.responseText);
        showToast('error', 'Error submitting termination. See console for details.');
    });
}

function onTerminationSearchChange() {
    const searchTerm = $('#termination-search').val().toLowerCase();
    const status = $('#termination-status-filter').val();
    loadTerminationsTable(status, 1, searchTerm);
}

function onTerminationStatusFilterChange() {
    const selectedStatus = $('#termination-status-filter').val();
    $('#termination-search').val('');
    loadTerminationsTable(selectedStatus, 1);
}

function archiveTermination(id) {
    // fetch termination details and open archive modal
    $.post('exit_management.php', {
        ajax_action: 'get_termination_details',
        controller: 'termination',
        termination_id: id
    }, function(response) {
        if (response.success) {
            $('#archiveTerminationId').val(id);
            $('#archiveTerminationEmployeeId').val(response.data.employee_id);
            $('#archiveTerminationEmployeeName').val(response.data.employee_name);
            $('#archiveTerminationReason').val(getAutomatedArchiveReason());
            $('#archiveTerminationModal').appendTo('body').modal('show');
        } else {
            showToast('error', 'Failed to load termination details');
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('Error fetching termination details:', status, error, xhr.responseText);
        showToast('error', 'Error fetching termination details.');
    });
}

function unarchiveTermination(id) {
    $.post('exit_management.php', {
        ajax_action: 'unarchive_termination',
        controller: 'termination',
        termination_id: id
    }, function(response) {
        if (response.success) {
            showToast('success', response.message || 'Termination unarchived successfully.');
            loadTerminationsTable();
            loadArchivedTerminationsTable(1, $('#archivedTerminationsModal').is(':visible'));
        } else {
            showToast('error', response.message || 'Failed to unarchive termination.');
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('Error unarchiving termination:', status, error, xhr.responseText);
        showToast('error', 'Error unarchiving termination.');
    });
}

function loadArchivedResignationsTable(page = 1, inModal = false) {
    const tbody = inModal ? $('#modal-archived-resignations-tbody') : $('#archived-resignations-tbody');
    const paginationId = inModal ? 'modal-archived-resignations-pagination' : 'archived-resignations-pagination';
    const noDataCols = inModal ? 7 : 10;
    showTableLoading(tbody, noDataCols);

    $.post('exit_management.php', {
        ajax_action: 'get_archived_resignations',
        controller: 'resignation',
        page: page,
        limit: 10
    }, function(response) {
        tbody.empty();

        if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
            response.data.forEach(function(resignation) {
                const statusBadge = getStatusBadge(resignation.status);
                const tooltip = resignation.archive_reason ? `Archive reason: ${resignation.archive_reason}` : '';
                const actions = `
                    <div class="table-actions">
                        <button class="btn btn-sm btn-success action-button" onclick="unarchiveResignation(${resignation.id})" title="Unarchive Resignation" aria-label="Unarchive Resignation">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                `;

                if (inModal) {
                    tbody.append(`
                        <tr title="${tooltip}">
                            <td>${resignation.employee_name || '<em class="text-danger">Missing Employee</em>'}</td>
                            <td>${resignation.department || '-'}</td>
                            <td>${resignation.email || '-'}</td>
                            <td>${resignation.position || '-'}</td>
                            <td>${resignation.resignation_type || '-'}</td>
                            <td>${resignation.reason || '-'}</td>
                            <td>${actions}</td>
                        </tr>
                    `);
                } else {
                    tbody.append(`
                        <tr title="${tooltip}">
                            <td>${resignation.employee_name || '<em class="text-danger">Missing Employee</em>'}</td>
                            <td>${resignation.department || '-'}</td>
                            <td>${resignation.email || '-'}</td>
                            <td>${resignation.position || '-'}</td>
                            <td>${resignation.resignation_type || '-'}</td>
                            <td>${resignation.reason || '-'}</td>
                            <td>${resignation.notice_date || '-'}</td>
                            <td>${resignation.last_working_date || '-'}</td>
                            <td>${resignation.comments ? resignation.comments.substring(0, 50) + '...' : '-'}</td>
                            <td class="status-cell">${statusBadge}</td>
                            <td class="actions-cell">${actions}</td>
                        </tr>
                    `);
                }
            });

            renderPagination(paginationId, response.total, page, response.limit || 10, (newPage) => loadArchivedResignationsTable(newPage, inModal));
        } else {
            tbody.append(`<tr><td colspan="${noDataCols}" class="text-center">No archived resignations found</td></tr>`);
            $(`#${paginationId}`).empty();
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('Error loading archived resignations:', status, error, xhr.responseText);
        tbody.html(`<tr><td colspan="${noDataCols}" class="text-center text-danger">Error loading archived resignations</td></tr>`);
        $(`#${paginationId}`).empty();
    });
}

function renderArchivedResignationsPage() {
    const tbody = $('#archived-resignations-tbody');
    tbody.empty();

    if (!archivedResignationsData.length) {
        tbody.append('<tr><td colspan="10" class="text-center">No archived resignations found</td></tr>');
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
            <div class="table-actions">
                <button class="btn btn-sm btn-success action-button" onclick="unarchiveResignation(${resignation.id})" title="Unarchive Resignation" aria-label="Unarchive Resignation" data-title="Unarchive Resignation">
                    <i class="fas fa-undo"></i>
                </button>
            </div>
        `;

        tbody.append(`
            <tr>
                <td>${resignation.employee_name || '<em class="text-danger">Missing Employee</em>'}</td>
                <td>${resignation.department || '-'}</td>
                <td>${resignation.email || '-'}</td>
                <td>${resignation.position || '-'}</td>
                <td>${resignation.resignation_type || '-'}</td>
                <td>${resignation.reason || '-'}</td>
                <td>${resignation.notice_date || '-'}</td>
                <td>${resignation.last_working_date || '-'}</td>
                <td>${resignation.comments ? resignation.comments.substring(0, 50) + '...' : '-'}</td>
                <td class="status-cell">${statusBadge}</td>
                <td class="actions-cell">${actions}</td>
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

function isInterviewCompletable(scheduledDate) {
    if (!scheduledDate) {
        return true;
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const interviewDate = new Date(scheduledDate);
    interviewDate.setHours(0, 0, 0, 0);

    return interviewDate <= today;
}

function loadInterviewsTable(status = 'all', page = 1, searchTerm = '') {
    const tbody = $('#interviews-tbody');
    showTableLoading(tbody, 5);

    $.post('exit_management.php', {
        ajax_action: 'get_interviews',
        controller: 'interview',
        status: status,
        page: page,
        limit: 10,
        search: searchTerm
    }, function(response) {
        tbody.empty();

        if (response && response.data && response.data.length > 0) {
            response.data.forEach(function(interview) {
                const statusBadge = getStatusBadge(interview.status);
                const canComplete = isInterviewCompletable(interview.scheduled_date);
                const completeButton = canComplete
                    ? `<button class="btn btn-sm btn-success" onclick="completeInterview(${interview.id})" title="Complete Interview">
                        <i class="fas fa-check"></i>
                    </button>`
                    : `<button class="btn btn-sm btn-secondary" disabled title="Cannot complete past interview">
                        <i class="fas fa-check"></i>
                    </button>`;
                const actions = `
                    <button class="btn btn-sm btn-info" onclick="showInterviewModal(${interview.id}, true)" title="View Interview">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${completeButton}
                    <button class="btn btn-sm btn-warning" onclick="archiveInterview(${interview.id})" title="Archive Interview">
                        <i class="fas fa-archive"></i>
                    </button>
                `;

                tbody.append(`
                    <tr data-id="${interview.id}" data-status="${interview.status || ''}">
                        <td>${interview.employee_name}</td>
                        <td>${interview.interviewer_name}</td>
                        <td>${interview.scheduled_date}</td>
                        <td>${statusBadge}</td>
                        <td>${actions}</td>
                    </tr>
                `);
            });

            // Add pagination controls
            renderInterviewsPagination('interviews-pagination', response, status, page, searchTerm);
        } else {
            tbody.append('<tr><td colspan="5" class="text-center">No interviews found</td></tr>');
            $('#interviews-pagination').empty();
        }

        loadArchivedInterviewSummary();
    }).fail(function(xhr, status, error) {
        console.error('Error loading interviews:', status, error, xhr.responseText);
        tbody.html('<tr><td colspan="5" class="text-center text-danger">Error loading interviews</td></tr>');
        $('#interviews-pagination').empty();
        $('#archive-notif-count').hide();
    });
}

let archivedInterviewsData = [];
let archivedInterviewPage = 1;
let archivedInterviewTotal = 0;
const archivedInterviewPageSize = 10;

function loadArchivedInterviewSummary() {
    $.post('exit_management.php', {
        ajax_action: 'get_archived_interviews',
        controller: 'interview',
        page: 1,
        limit: 1
    }, function(response) {
        const badge = $('#archive-notif-count');
        const archiveButton = $('#viewArchivedInterviewsButton');
        if (!response || typeof response.new_count === 'undefined' || typeof response.total === 'undefined') {
            badge.hide();
            archiveButton.attr('title', 'Archived interviews');
            return;
        }

        if (response.new_count > 0) {
            badge.text(response.new_count).show();
            archiveButton.attr('title', `${response.new_count} new archived interviews`);
        } else {
            badge.hide();
            archiveButton.attr('title', response.total > 0 ? `${response.total} archived interviews` : 'No archived interviews yet');
        }
    }, 'json').fail(function() {
        $('#archive-notif-count').hide();
    });
}

function loadArchivedInterviewsTable(page = 1) {
    const tbody = $('#archived-interviews-tbody');
    tbody.html('<tr><td colspan="7" class="text-center text-muted">Loading...</td></tr>');

    $.post('exit_management.php', {
        ajax_action: 'get_archived_interviews',
        controller: 'interview',
        page: page,
        limit: archivedInterviewPageSize
    }, function(response) {
        if (response && response.data && response.data.length > 0) {
            archivedInterviewsData = response.data;
            archivedInterviewPage = page;
            archivedInterviewTotal = response.total || response.data.length;
            renderArchivedInterviewsPage();
            return;
        }

        archivedInterviewsData = [];
        archivedInterviewTotal = 0;
        tbody.html('<tr><td colspan="7" class="text-center text-muted">No archived interviews yet</td></tr>');
        $('#archived-interviews-pagination').empty();
    }, 'json').fail(function() {
        archivedInterviewsData = [];
        archivedInterviewTotal = 0;
        tbody.html('<tr><td colspan="7" class="text-center text-danger">Unable to load archived interviews</td></tr>');
        $('#archived-interviews-pagination').empty();
    });
}

function renderArchivedInterviewsPage() {
    const tbody = $('#archived-interviews-tbody');
    tbody.empty();

    if (!archivedInterviewsData.length) {
        tbody.html('<tr><td colspan="7" class="text-center text-muted">No archived interviews yet</td></tr>');
        $('#archived-interviews-pagination').empty();
        return;
    }

    archivedInterviewsData.forEach(function(interview) {
        tbody.append(`
            <tr>
                <td>${interview.employee_name || 'Unknown'}</td>
                <td>${interview.interviewer_name || 'Unknown'}</td>
                <td>${interview.scheduled_date || ''}</td>
                <td>${interview.status || ''}</td>
                <td>${interview.archived_at || ''}</td>
                <td>${interview.archive_reason || ''}</td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="unarchiveInterview(${interview.original_id || interview.archive_id || interview.id})" title="Unarchive Interview">
                        <i class="fas fa-undo"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    const pagination = $('#archived-interviews-pagination');
    pagination.empty();

    const totalPages = Math.ceil(archivedInterviewTotal / archivedInterviewPageSize);
    if (totalPages > 1) {
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === archivedInterviewPage ? 'btn-primary' : 'btn-outline-secondary';
            pagination.append(`<button class="btn btn-sm ${activeClass} mr-1" onclick="goToArchivedInterviewPage(${i})">${i}</button>`);
        }
    }
}

function goToArchivedInterviewPage(page) {
    loadArchivedInterviewsTable(page);
}

function openArchiveModal() {
    $('#archivedInterviewsModal').modal('show');
    loadArchivedInterviewsTable(1);
}

function loadTransfersTable(status = 'all', page = 1, limit = 10, searchTerm = '') {
    const tbody = $('#transfers-tbody');
    showTableLoading(tbody, 6);

    $.post('exit_management.php', {
        ajax_action: 'get_transfer_plans',
        controller: 'transfer',
        status: status,
        page: page,
        limit: limit,
        search: searchTerm
    }, function(response) {
        console.log('Transfer plans response:', response);
        tbody.empty();

        if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
            response.data.forEach(function(plan) {
                const statusBadge = getStatusBadge(plan.status);
                const planId = plan.id ?? plan.plan_id ?? plan.planId ?? null;
                if (planId === null || planId === undefined || planId === '') {
                    console.warn('Transfer plan missing id, row data:', plan);
                }
                const actions = `
                    <button class="btn btn-sm btn-info" onclick="showTransferModal(${JSON.stringify(planId)})" title="View Transfer Plan">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="viewTransferItems(${planId})" title="View Transfer Items">
                        <i class="fas fa-list"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="archiveTransferPlan(${planId})" title="Archive Transfer Plan">
                        <i class="fas fa-archive"></i>
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

        // Render pagination
        renderPagination('transfers-pagination', response.total, page, limit, (newPage) => loadTransfersTable(status, newPage, limit, searchTerm));
        loadArchivedTransferSummary();
    }, 'json').fail(function(err) {
        console.error('Error loading transfers:', err);
        tbody.html('<tr><td colspan="6" class="text-center text-danger">Error loading transfer plans</td></tr>');
    });
}

let archivedTransferPage = 1;

function archiveTransfers() {
    $('#archivedTransfersModal').modal('show');
    loadArchivedTransfersTable(1);
}

function loadArchivedTransferSummary() {
    const badge = $('#transfer-archive-notif-count');

    $.post('exit_management.php', {
        ajax_action: 'get_archived_transfer_plans',
        controller: 'transfer',
        page: 1,
        limit: 1
    }, function(response) {
        if (!response || typeof response.new_count === 'undefined') {
            badge.hide();
            return;
        }

        if (response.new_count > 0) {
            badge.text(response.new_count).show();
        } else {
            badge.hide();
        }
    }, 'json').fail(function() {
        badge.hide();
    });
}

function loadArchivedTransfersTable(page = 1) {
    const tbody = $('#archived-transfers-tbody');
    tbody.html('<tr><td colspan="7" class="text-center text-muted">Loading archived transfer plans...</td></tr>');

    $.post('exit_management.php', {
        ajax_action: 'get_archived_transfer_plans',
        controller: 'transfer',
        page: page,
        limit: 10
    }, function(response) {
        archivedTransferPage = page;
        tbody.empty();

        if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
            response.data.forEach(function(plan) {
                const statusBadge = getStatusBadge(plan.status);
                const unarchiveButton = `
                    <button class="btn btn-sm btn-success" onclick="unarchiveTransferPlan(${plan.plan_id})" title="Unarchive Transfer Plan">
                        <i class="fas fa-undo"></i>
                    </button>
                `;

                tbody.append(`
                    <tr>
                        <td>${plan.employee_name || 'Unknown'}</td>
                        <td>${plan.start_date || '-'}</td>
                        <td>${plan.end_date || '-'}</td>
                        <td>${statusBadge}</td>
                        <td>${plan.archived_at || '-'}</td>
                        <td>${plan.archive_reason || '-'}</td>
                        <td>${unarchiveButton}</td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="7" class="text-center">No archived transfer plans found</td></tr>');
        }

        renderPagination('archived-transfers-pagination', response.total, page, response.limit || 10, (newPage) => `loadArchivedTransfersTable(${newPage})`);
    }, 'json').fail(function(err) {
        console.error('Error loading archived transfer plans:', err);
        tbody.html('<tr><td colspan="7" class="text-center text-danger">Error loading archived transfer plans</td></tr>');
    });
}

function loadSettlementsTable(status = 'all', page = 1, limit = 10, searchTerm = '') {
    const tbody = $('#settlements-tbody');
    showTableLoading(tbody, 5);

    $.ajax({
        url: 'exit_management.php',
        method: 'POST',
        dataType: 'json',
        timeout: 30000,
        data: {
            ajax_action: 'get_settlements',
            controller: 'settlement',
            status: status,
            page: page,
            limit: limit,
            search: searchTerm
        },
        success: function(response) {
            tbody.empty();

            if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
                response.data.forEach(function(settlement) {
                    const statusBadge = getStatusBadge(settlement.status);
                    const actions = `
                        <button class="btn btn-sm btn-info" onclick="showSettlementModal(${settlement.id}, true)" title="View Settlement">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-success" onclick="printSettlement(${settlement.id})" title="Print Settlement">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="archiveSettlement(${settlement.id}, this)" data-employee-id="${settlement.employee_id || ''}" data-employee-name="${(settlement.employee_name || '').replace(/"/g, '&quot;')}" title="Archive Settlement">
                            <i class="fas fa-archive"></i>
                        </button>
                    `;

                    tbody.append(`
                        <tr data-settlement-id="${settlement.id}">
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

            // Render pagination
            renderPagination('settlements-pagination', response.total, page, limit, (newPage) => `loadSettlementsTable('${status}', ${newPage}, ${limit})`);
        },
        error: function(xhr, status, error) {
            console.error('Error loading settlements:', status, error, xhr.responseText);
            tbody.html('<tr><td colspan="5" class="text-center text-danger">Error loading settlements</td></tr>');
        }
    });
}

function loadDocumentsTable(status = 'all', page = 1, limit = 10, searchTerm = '') {
    console.log('=== LOADING DOCUMENTS TABLE ===');
    const tbody = $('#documents-tbody');
    showTableLoading(tbody, 5);

    $.post('exit_management.php', {
        ajax_action: 'get_documents',
        controller: 'documentation',
        status: status,
        page: page,
        limit: limit,
        search: searchTerm
    }, function(response) {
        console.log('=== DOCUMENTS TABLE RESPONSE ===');
        console.log('Full response:', response);
        console.log('Response type:', typeof response);
        console.log('Is array:', Array.isArray(response));
        console.log('Response length:', response ? response.length : 'null/undefined');

        const tbody = $('#documents-tbody');
        tbody.empty();

        if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
            console.log('Found ' + response.data.length + ' documents');
            response.data.forEach(function(doc, index) {
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
                    <button class="btn btn-sm btn-warning" onclick="archiveDocument(${doc.id})" title="Archive Document">
                        <i class="fas fa-archive"></i>
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

        // Render pagination
        renderPagination('documents-pagination', response.total, page, limit, (newPage) => `loadDocumentsTable('${status}', ${newPage}, ${limit})`);
    }, 'json').fail(function(xhr, status, error) {
        console.error('Error loading documents:', status, error, xhr.responseText);
        const tbody = $('#documents-tbody');
        tbody.empty();
        tbody.append('<tr><td colspan="5" class="text-center text-danger">Error loading documents: ' + error + '</td></tr>');
    });
}

function loadSurveysTable(status = 'all', page = 1, limit = 10, searchTerm = '') {
    const tbody = $('#surveys-tbody');
    showTableLoading(tbody, 5);

    $.post('exit_management.php', {
        ajax_action: 'get_surveys',
        controller: 'survey',
        status: status,
        page: page,
        limit: limit,
        search: searchTerm
    }, function(response) {
        tbody.empty();

        if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
            response.data.forEach(function(survey) {
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
                    <button class="btn btn-sm btn-secondary" onclick="archiveSurvey(${survey.id})" title="Archive Survey">
                        <i class="fas fa-archive"></i>
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

        // Render pagination
        renderPagination('surveys-pagination', response.total, page, limit, (newPage) => `loadSurveysTable('${status}', ${newPage}, ${limit})`);
    }).fail(function(xhr, status, error) {
        console.error('Error loading surveys:', status, error, xhr.responseText);
        tbody.html('<tr><td colspan="5" class="text-center text-danger">Error loading surveys</td></tr>');
    });
}

// Helper functions
function getStatusBadge(status) {
    const normalized = (status || '').toLowerCase();
    const statusLabels = {
        'pending': 'Pending',
        'pending_review': 'Pending Review',
        'pending_legal_review': 'Pending Legal Review',
        'approved': 'Approved',
        'rejected': 'Rejected',
        'rejected_by_legal': 'Rejected by Legal',
        'withdrawn': 'Withdrawn',
        'completed': 'Completed',
        'active': 'Active',
        'inactive': 'Inactive',
        'scheduled': 'Scheduled',
        'draft': 'Draft',
        'archived': 'Archived'
    };

    const statusClasses = {
        'pending': 'badge badge-warning',
        'pending_review': 'badge badge-warning',
        'pending_legal_review': 'badge badge-info',
        'approved': 'badge badge-success',
        'rejected': 'badge badge-danger',
        'rejected_by_legal': 'badge badge-danger',
        'withdrawn': 'badge badge-secondary',
        'completed': 'badge badge-success',
        'active': 'badge badge-primary',
        'inactive': 'badge badge-secondary',
        'scheduled': 'badge badge-info',
        'draft': 'badge badge-light',
        'archived': 'badge badge-dark'
    };

    const label = statusLabels[normalized] || status || 'Unknown';
    const cssClass = statusClasses[normalized] || 'badge badge-secondary';
    return `<span class="${cssClass}">${label}</span>`;
}

// Toast notification function
function showToast(type, message, title) {
    const normalizedType = String(type || 'info').toLowerCase();
    const titles = {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Information'
    };
    const toastTitle = title || titles[normalizedType] || 'Notice';

    if (window.$ && typeof window.$(document).Toasts === 'function') {
        const toastClass = normalizedType === 'success'
            ? 'bg-success'
            : normalizedType === 'error'
                ? 'bg-danger'
                : normalizedType === 'warning'
                    ? 'bg-warning'
                    : 'bg-info';

        $(document).Toasts('create', {
            class: toastClass,
            title: toastTitle,
            subtitle: '',
            body: message,
            autohide: true,
            delay: 4000,
            close: true
        });
    } else if (window.toastr && typeof window.toastr[normalizedType] === 'function') {
        toastr[normalizedType](message, toastTitle, {
            closeButton: true,
            progressBar: true,
            timeOut: 4000,
            positionClass: 'toast-top-right'
        });
    } else if (typeof showToastMessage === 'function') {
        showToastMessage(normalizedType, message);
    } else {
        createCustomToast(normalizedType, toastTitle, message);
    }
}

function ensureCustomToastContainer() {
    if ($('#customToastContainer').length) {
        return;
    }

    $('body').append(
        '<div id="customToastContainer" style="position: fixed; top: 1rem; right: 1rem; z-index: 11000; display: flex; flex-direction: column; gap: .75rem;"></div>'
    );
}

function createCustomToast(type, title, message) {
    ensureCustomToastContainer();

    const toastId = `customToast_${Date.now()}`;
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    const bgColor = colors[type] || '#17a2b8';
    const textColor = type === 'warning' ? '#212529' : '#fff';

    const toastHtml = `
        <div id="${toastId}" style="min-width: 280px; max-width: 360px; border-radius: .375rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15); background: ${bgColor}; color: ${textColor}; padding: 12px 14px; position: relative; overflow: hidden;">
            <strong style="display: block; margin-bottom: 4px;">${title}</strong>
            <div style="font-size: .95rem; line-height: 1.3;">${message}</div>
            <button type="button" onclick="document.getElementById('${toastId}')?.remove()" style="position: absolute; top: 8px; right: 8px; border: none; background: transparent; color: ${textColor}; font-size: 1rem; cursor: pointer;">&times;</button>
        </div>
    `;

    $('#customToastContainer').append(toastHtml);
    setTimeout(() => { $(`#${toastId}`).fadeOut(300, function() { $(this).remove(); }); }, 4000);
}

let confirmationCallback = null;

function showConfirmation(message, callback, options = {}) {
    confirmationCallback = typeof callback === 'function' ? callback : null;
    $('#confirmationMessage').text(message || 'Are you sure you want to proceed?');

    const buttonText = options.confirmButtonText || 'Confirm';
    const buttonClass = options.confirmButtonClass || 'btn-warning';

    $('#confirmActionBtn')
        .text(buttonText)
        .removeClass('btn-success btn-danger btn-warning btn-primary btn-info')
        .addClass(buttonClass);

    $('#confirmationModal').modal('show');
}

let lastPayrollApprovalCount = 0;

// Load dashboard data
function loadDashboardData() {
    $.ajax({
        url: 'exit_management.php',
        method: 'POST',
        dataType: 'json',
        timeout: 30000,
        data: {
            ajax_action: 'get_dashboard_stats'
        },
        success: function(response) {
            if (response && typeof response === 'object') {
                $('#pending-resignations').text(response.pending_resignations || 0);
                $('#scheduled-interviews').text(response.scheduled_interviews || 0);
                $('#active-transfers').text(response.active_transfers || 0);
                $('#pending-settlements').text(response.pending_settlements || 0);
            } else {
                console.error('Invalid dashboard stats response:', response);
            }
        },
        error: function(xhr, status, errorThrown) {
            console.error('Error loading dashboard stats:', status, errorThrown, xhr.status, xhr.statusText, xhr.responseText);
        }
    });

    loadPayrollApprovalNotifications();

    // Load charts and recent resignations
    loadResignationTrendChart();
    loadResignationReasonsChart();
    loadExitStatusChart();
    // resignation type chart removed
    loadTerminationTrendChart();
    loadTerminationStatusChart();
    loadDashboardMetrics();
}

function loadPayrollApprovalNotifications() {
    $.post('exit_management.php', {
        ajax_action: 'get_payroll_clearance_notifications'
    }, function(response) {
        const count = response.count || 0;
        $('#approved-preclearances').text(count);

        const notifications = response.notifications || [];
        const tbody = $('#payroll-approval-notification-body');
        tbody.empty();

        if (count > 0) {
            $('#payroll-approval-notification-row').show();
            notifications.forEach(function(notification, index) {
                const approvedAt = notification.approved_at ? new Date(notification.approved_at).toLocaleString() : 'N/A';
                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${notification.full_name || 'Unknown'}</td>
                        <td>${notification.settlement_date || 'N/A'}</td>
                        <td>₱${parseFloat(notification.net_payable || 0).toFixed(2)}</td>
                        <td>${approvedAt}</td>
                    </tr>
                `);
            });

            if (count > lastPayrollApprovalCount) {
                const newApprovals = count - lastPayrollApprovalCount;
                if (newApprovals > 0 && lastPayrollApprovalCount !== 0) {
                    showToast('info', `${newApprovals} new payroll pre-clearance approval${newApprovals > 1 ? 's' : ''} received.`);
                }
            }
        } else {
            $('#payroll-approval-notification-row').hide();
        }

        lastPayrollApprovalCount = count;
    }).fail(function(xhr, status, errorThrown) {
        console.error('Error loading payroll clearance notifications:', status, errorThrown, xhr.status, xhr.statusText, xhr.responseText);
    });
}

// Load section data based on section name
function onResignationStatusFilterChange() {
    const selectedStatus = $('#resignation-status-filter').val();
    console.log('[Filter] Resignation status changed to:', selectedStatus);

    // Clear search when switching filters
    $('#resignation-search').val('');

    if (selectedStatus === 'archived') {
        console.log('[Filter] Loading archived resignations');
        $('#archived-resignations-container').show();
        $('#toggle-archived-resignations').text('Hide Archived');
        loadArchivedResignationsTable(1);
    } else if (selectedStatus === 'all') {
        console.log('[Filter] Loading all resignations');
        $('#archived-resignations-container').show();
        $('#toggle-archived-resignations').text('Hide Archived');
        loadResignationsTable('all', 1);
        loadArchivedResignationsTable(1);
    } else {
        console.log('[Filter] Loading status:', selectedStatus);
        $('#archived-resignations-container').hide();
        $('#toggle-archived-resignations').text('Show Archived');
        loadResignationsTable(selectedStatus, 1);
    }
}

function onInterviewStatusFilterChange() {
    const selectedStatus = $('#interview-status-filter').val();
    console.log('[Filter] Interview status:', selectedStatus);
    // Clear search when switching filters
    $('#interview-search').val('');
    loadInterviewsTable(selectedStatus === 'active' ? 'all' : selectedStatus, 1);
}

function onTransferStatusFilterChange() {
    const selectedStatus = $('#transfer-status-filter').val();
    console.log('[Filter] Transfer status:', selectedStatus);
    // Clear search when switching filters
    $('#transfer-search').val('');
    loadTransfersTable(selectedStatus === 'active' ? 'all' : selectedStatus, 1);
}

function onSettlementStatusFilterChange() {
    const selectedStatus = $('#settlement-status-filter').val();
    console.log('[Filter] Settlement status:', selectedStatus);
    // Clear search when switching filters
    $('#settlement-search').val('');
    loadSettlementsTable(selectedStatus === 'active' ? 'all' : selectedStatus, 1);
}

function onDocumentStatusFilterChange() {
    const selectedStatus = $('#document-status-filter').val();
    console.log('[Filter] Document status:', selectedStatus);
    // Clear search when switching filters
    $('#document-search').val('');
    loadDocumentsTable(selectedStatus === 'active' ? 'all' : selectedStatus, 1);
}

function onSurveyStatusFilterChange() {
    const selectedStatus = $('#survey-status-filter').val();
    console.log('[Filter] Survey status:', selectedStatus);
    // Clear search when switching filters
    $('#survey-search').val('');
    loadSurveysTable(selectedStatus === 'active' ? 'all' : selectedStatus, 1);
}

function getAutomatedArchiveReason() {
    return 'Process completed; archived.';
}

// Action functions
function archiveResignation(id) {
    // Get resignation data first
    $.post('exit_management.php', {
        ajax_action: 'get_resignation_details',
        controller: 'resignation',
        resignation_id: id
    }, function(response) {
        if (response.success) {
            // Populate modal with resignation data
            $('#archiveResignationId').val(id);
            $('#archiveEmployeeId').val(response.data.employee_id);
            $('#archiveEmployeeName').val(response.data.employee_name);
            $('#archiveReason').val(getAutomatedArchiveReason());
            $('#archiveNotes').val('');

            // Show modal
            $('#archiveResignationModal').modal('show');
        } else {
            showToast('error', 'Failed to load resignation details');
        }
    }, 'json');
}

// Open archived resignations in a modal (used by Archive header button)
function openArchivedResignationsModal(page = 1) {
    console.log('[Archive] Opening archived resignations modal, page=', page);
    // ensure inline archived container is hidden when opening modal
    $('#archived-resignations-container').hide();

    $('#modal-archived-resignations-tbody').html('<tr><td colspan="7" class="text-center text-muted">Loading archived resignations...</td></tr>');
    $('#modal-archived-resignations-pagination').empty();
    // ensure modal is appended to body so Bootstrap places it above other containers
    $('#archivedResignationsModal').appendTo('body').modal('show');
    loadArchivedResignationsTable(page, true);
}

function unarchiveResignation(id) {
    showConfirmation('Are you sure you want to unarchive this resignation?', function() {
        $.post('exit_management.php', {
            ajax_action: 'unarchive_resignation',
            controller: 'resignation',
            resignation_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadResignationsTable();
                loadArchivedResignationsTable(1, $('#archivedResignationsModal').is(':visible'));
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        }, 'json');
    }, {
        confirmButtonText: 'Unarchive',
        confirmButtonClass: 'btn-success'
    });
}

// Archive resignation form handler
$(document).ready(function() {
    $('#archiveResignationForm').on('submit', function(e) {
        e.preventDefault();

        const resignationId = $('#archiveResignationId').val();
        const archiveReason = getAutomatedArchiveReason();

        $.post('exit_management.php', {
            ajax_action: 'archive_resignation',
            controller: 'resignation',
            resignation_id: resignationId,
            archive_reason: archiveReason
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                $('#archiveResignationModal').modal('hide');
                loadResignationsTable();
                loadArchivedResignationsTable(1, $('#archivedResignationsModal').is(':visible'));
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        }, 'json').fail(function() {
            showToast('error', 'Failed to archive resignation');
        });
    });
});

// Ensure header archive button label is correct and modal is attached to body
$(document).ready(function() {
    const headerArchiveBtn = $('#open-archived-resignations');
    if (headerArchiveBtn.length) {
        headerArchiveBtn.html('<i class="fas fa-archive"></i> Archive');
    }
});

// Archive settlement form handler
$(document).ready(function() {
    $('#archiveSettlementForm').on('submit', function(e) {
        e.preventDefault();

        const settlementId = $('#archiveSettlementId').val();
        const archiveReason = getAutomatedArchiveReason();

        $.post('exit_management.php', {
            ajax_action: 'archive_settlement',
            controller: 'settlement',
            settlement_id: settlementId,
            archive_reason: archiveReason
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                $('#archiveSettlementModal').modal('hide');
                loadSettlementsTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        }, 'json').fail(function() {
            showToast('error', 'Failed to archive settlement');
        });
    });

    $('#settlementEditBtn').off('click').on('click', function() {
        $('#settlementModalTitle').text('Edit Settlement');
        setSettlementModalMode(false);
    });
});

// Archive interview form handler
$(document).ready(function() {
    $('#archiveInterviewForm').on('submit', function(e) {
        e.preventDefault();

        const interviewId = $('#archiveInterviewId').val();
        const archiveReason = getAutomatedArchiveReason();

        $.post('exit_management.php', {
            ajax_action: 'archive_interview',
            controller: 'interview',
            interview_id: interviewId,
            archive_reason: archiveReason
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                $('#archiveInterviewModal').modal('hide');
                loadInterviewsTable();
                loadDashboardData();
                loadArchivedInterviewsTable(1);
                loadArchivedInterviewSummary();
            } else {
                showToast('error', response.message);
            }
        }, 'json').fail(function() {
            showToast('error', 'Failed to archive interview');
        });
    });
});

// Archive document form handler
$(document).ready(function() {
    $('#archiveDocumentForm').on('submit', function(e) {
        e.preventDefault();

        const documentId = $('#archiveDocumentId').val();
        const archiveReason = getAutomatedArchiveReason();

        $.post('exit_management.php', {
            ajax_action: 'archive_document',
            controller: 'documentation',
            document_id: documentId,
            archive_reason: archiveReason
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                $('#archiveDocumentModal').modal('hide');
                loadDocumentsTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        }, 'json').fail(function() {
            showToast('error', 'Failed to archive document');
        });
    });
});

// Archive survey form handler
$(document).ready(function() {
    $('#archiveSurveyForm').on('submit', function(e) {
        e.preventDefault();

        const surveyId = $('#archiveSurveyId').val();
        const archiveReason = getAutomatedArchiveReason();

        $.post('exit_management.php', {
            ajax_action: 'archive_survey',
            controller: 'survey',
            survey_id: surveyId,
            archive_reason: archiveReason
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                $('#archiveSurveyModal').modal('hide');
                loadSurveysTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        }, 'json').fail(function() {
            showToast('error', 'Failed to archive survey');
        });
    });
});

// Archive transfer plan form handler
$(document).ready(function() {
    $('#archiveTransferPlanForm').on('submit', function(e) {
        e.preventDefault();

        const planId = $('#archiveTransferPlanId').val();
        const archiveReason = getAutomatedArchiveReason();

        $.post('exit_management.php', {
            ajax_action: 'archive_transfer_plan',
            controller: 'transfer',
            plan_id: planId,
            reason: archiveReason,
            archive_reason: archiveReason
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                $('#archiveTransferPlanModal').modal('hide');
                loadTransfersTable();
                loadDashboardData();
                loadArchivedTransferSummary();
            } else {
                showToast('error', response.message);
            }
        }, 'json').fail(function() {
            showToast('error', 'Failed to archive transfer plan');
        });
    });
});

// Archive transfer item form handler
$(document).ready(function() {
    $('#archiveTransferItemForm').on('submit', function(e) {
        e.preventDefault();

        const itemId = $('#archiveTransferItemId').val();
        const archiveReason = getAutomatedArchiveReason();

        $.post('exit_management.php', {
            ajax_action: 'archive_transfer_item',
            controller: 'transfer',
            item_id: itemId,
            reason: archiveReason,
            archive_reason: archiveReason
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                $('#archiveTransferItemModal').modal('hide');
                loadTransfersTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        }, 'json').fail(function() {
            showToast('error', 'Failed to archive transfer item');
        });
    });
});

function completeInterview(id) {
    showConfirmation('Mark this interview as completed?', function() {
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
    }, {
        confirmButtonText: 'Complete',
        confirmButtonClass: 'btn-success'
    });
}

function deleteTransferPlan(id) {
    showConfirmation('Are you sure you want to delete this knowledge transfer plan? This will also delete all associated transfer items.', function() {
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
    }, {
        confirmButtonText: 'Delete',
        confirmButtonClass: 'btn-danger'
    });
}

function viewTransferItems(id) {
    $.post('exit_management.php', {
        ajax_action: 'get_transfer_items',
        controller: 'transfer',
        plan_id: id
    }, function(response) {
        if (response && response.length > 0) {
            let itemsHtml = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Type</th><th>Title</th><th>Priority</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
            response.forEach(item => {
                const statusBadge = getStatusBadge(item.status);
                const actions = `
                    <button class="btn btn-sm btn-warning" onclick="archiveTransferItem(${item.id})" title="Archive Item">
                        <i class="fas fa-archive"></i>
                    </button>
                `;
                itemsHtml += `<tr><td>${item.item_type || item.type}</td><td>${item.title}</td><td>${item.priority}</td><td>${statusBadge}</td><td>${actions}</td></tr>`;
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
    showConfirmation('Are you sure you want to delete this document?', function() {
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
    }, {
        confirmButtonText: 'Delete',
        confirmButtonClass: 'btn-danger'
    });
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

function viewSurveyResponseDetails(responseId) {
    $.post('exit_management.php', {
        ajax_action: 'get_response_details',
        controller: 'survey',
        response_id: responseId
    }, function(response) {
        if (response && response.response) {
            const responseData = response.response;
            const answers = response.answers || [];
            let answersHtml = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Question</th><th>Answer</th></tr></thead><tbody>';

            answers.forEach(answer => {
                let answerValue = answer.answer_value || answer.answer_text || '';
                if (answer.question_type === 'checkbox' && answer.answer_array) {
                    answerValue = Array.isArray(answer.answer_array) ? answer.answer_array.join(', ') : answer.answer_array;
                }

                answersHtml += `<tr><td>${answer.question_text || ''}</td><td>${answerValue}</td></tr>`;
            });

            answersHtml += '</tbody></table></div>';
            const modal = `
                <div class="modal fade" id="surveyResponseDetailsModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Survey Response Details</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Survey:</strong> ${responseData.survey_title || 'Unknown'}</p>
                                <p><strong>Respondent:</strong> ${responseData.full_name || responseData.respondent_name || 'Unknown'}</p>
                                <p><strong>Submitted At:</strong> ${responseData.submitted_at || ''}</p>
                                ${answersHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modal);
            $('#surveyResponseDetailsModal').modal('show');
            $('#surveyResponseDetailsModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        } else {
            showToast('info', 'Survey response details could not be found.');
        }
    }, 'json').fail(function(err) {
        console.error('AJAX error loading survey response details:', err);
        showToast('error', 'Failed to load survey response details.');
    });
}

function duplicateSurvey(id) {
    showConfirmation('Create a copy of this survey?', function() {
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
    }, {
        confirmButtonText: 'Duplicate',
        confirmButtonClass: 'btn-primary'
    });
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
            maintainAspectRatio: false,
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
// Resignation type chart removed per UI request

// Load recent resignations table
function loadRecentResignations() {
    const recentTbody = $('#recent-resignations-tbody');
    showTableLoading(recentTbody, 8);

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
        const statusBadge = getStatusBadge(res.status);
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
                    <td>${statusBadge}</td>
                    <td>${daysLeft}</td>
                </tr>`;
    });

    tbody.html(html);
}

// Get status badge CSS class
function getStatusBadgeClass(status) {
    switch((status || '').toLowerCase()) {
        case 'pending':
        case 'pending_review':
            return 'badge-warning';
        case 'pending_legal_review':
            return 'badge-info';
        case 'approved':
            return 'badge-success';
        case 'rejected':
        case 'rejected_by_legal':
            return 'badge-danger';
        case 'withdrawn':
            return 'badge-secondary';
        case 'completed':
            return 'badge-info';
        default:
            return 'badge-secondary';
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

function formatCurrency(value) {
    const number = Number(value);
    if (Number.isNaN(number)) {
        return '0.00';
    }
    return number.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function populatePayrollSettlementSummary(data, viewOnly = false) {
    const keys = [
        'net_payable', 'basic_salary', 'remaining_salary', 'unused_leave_conversion', 'overtime_pay',
        'holiday_pay', 'bonuses', 'commission', 'hra', 'conveyance', 'lta', 'medical_allowance',
        'other_allowances', 'separation_pay', 'tax', 'sss', 'philhealth', 'pagibig', 'cash_advance',
        'company_loan', 'equipment_damage', 'missing_assets', 'late_deductions', 'absence_deductions',
        'provident_fund', 'gratuity', 'notice_pay', 'outstanding_loans', 'other_deductions'
    ];

    let hasData = false;
    keys.forEach(key => {
        const value = data[key] !== undefined && data[key] !== null ? data[key] : 0;
        const formatted = formatCurrency(value);
        $(`#payrollSettlementSummary_${key}`).text(formatted);
        if (Number(value) !== 0) {
            hasData = true;
        }
    });

    if (viewOnly) {
        $('#payrollSettlementSummaryCard').removeClass('d-none');
    } else {
        $('#payrollSettlementSummaryCard').toggleClass('d-none', !hasData);
    }
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
            $('#remainingSalary').val(response.remaining_salary || 0);
            $('#unusedLeaveConversion').val(response.unused_leave_conversion || 0);
            $('#overtimePay').val(response.overtime_pay || 0);
            $('#holidayPay').val(response.holiday_pay || 0);
            $('#bonuses').val(response.bonuses || 0);
            $('#commission').val(response.commission || 0);
            $('#hra').val(response.hra || 0);
            $('#conveyance').val(response.conveyance || 0);
            $('#lta').val(response.lta || 0);
            $('#medicalAllowance').val(response.medical_allowance || 0);
            $('#otherAllowances').val(response.other_allowances || 0);
            $('#separationPay').val(response.separation_pay || 0);
            $('#tax').val(response.tax || 0);
            $('#sss').val(response.sss || 0);
            $('#philhealth').val(response.philhealth || 0);
            $('#pagibig').val(response.pagibig || 0);
            $('#providentFund').val(response.provident_fund || 0);
            $('#cashAdvance').val(response.cash_advance || 0);
            $('#companyLoan').val(response.company_loan || 0);
            $('#equipmentDamage').val(response.equipment_damage || 0);
            $('#missingAssets').val(response.missing_assets || 0);
            $('#lateDeductions').val(response.late_deductions || 0);
            $('#absenceDeductions').val(response.absence_deductions || 0);
            $('#outstandingLoans').val(response.outstanding_loans || 0);
            $('#otherDeductions').val(response.other_deductions || 0);
            $('#gratuity').val(response.gratuity || 0);
            $('#noticePay').val(response.notice_pay || 0);

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
    $('#remainingSalary').val('');
    $('#unusedLeaveConversion').val('');
    $('#overtimePay').val('');
    $('#holidayPay').val('');
    $('#bonuses').val('');
    $('#commission').val('');
    $('#hra').val('');
    $('#conveyance').val('');
    $('#lta').val('');
    $('#medicalAllowance').val('');
    $('#otherAllowances').val('');
    $('#separationPay').val('');
    $('#tax').val('');
    $('#sss').val('');
    $('#philhealth').val('');
    $('#pagibig').val('');
    $('#providentFund').val('');
    $('#cashAdvance').val('');
    $('#companyLoan').val('');
    $('#equipmentDamage').val('');
    $('#missingAssets').val('');
    $('#lateDeductions').val('');
    $('#absenceDeductions').val('');
    $('#outstandingLoans').val('');
    $('#otherDeductions').val('');
    $('#gratuity').val('');
    $('#noticePay').val('');
    $('#netPayable').val('');
}

// Load dashboard metrics
function loadDashboardMetrics() {
    $.ajax({
        url: 'exit_management.php',
        method: 'POST',
        dataType: 'json',
        timeout: 30000,
        data: {
            ajax_action: 'get_dashboard_metrics'
        },
        success: function(response) {
            if (response && typeof response === 'object') {
                console.log('loadDashboardMetrics response:', response);
                $('#total-exited').text(response.total_exited || 0);
                $('#avg-notice').text(response.avg_notice || 0);
                $('#top-reason').text((response.top_reason || 'N/A').substring(0, 20));
                $('#avg-interviews').text((response.interview_rate || 0) + '%');
            } else {
                console.error('Invalid dashboard metrics response:', response);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('loadDashboardMetrics AJAX error:', textStatus, errorThrown, jqXHR.status, jqXHR.statusText, jqXHR.responseText);
        }
    });
}

// Section data loading function
function loadSectionData(sectionName) {
    switch (sectionName) {
        case 'dashboard':
            loadDashboardData();
            break;
        case 'resignations':
            loadResignationsTable();
            break;
        case 'terminations':
            loadTerminationsTable();
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
            console.log('Unknown section:', sectionName);
    }
}

// Search and Filter Functions
function onResignationSearchChange() {
    const searchTerm = $('#resignation-search').val().toLowerCase();
    const status = $('#resignation-status-filter').val();
    loadResignationsTable(status, 1, searchTerm);
}

function onInterviewSearchChange() {
    const searchTerm = $('#interview-search').val().toLowerCase();
    const status = $('#interview-status-filter').val();
    loadInterviewsTable(status, 1, searchTerm);
}

function onTransferSearchChange() {
    const searchTerm = $('#transfer-search').val().toLowerCase();
    const status = $('#transfer-status-filter').val();
    loadTransfersTable(status, 1, 10, searchTerm);
}

function onSettlementSearchChange() {
    const searchTerm = $('#settlement-search').val().toLowerCase();
    const status = $('#settlement-status-filter').val();
    loadSettlementsTable(status, 1, 10, searchTerm);
}

function openArchivedSettlementsModal(page = 1) {
    $('#modal-archived-settlements-tbody').html('<tr><td colspan="6" class="text-center text-muted">Loading archived settlements...</td></tr>');
    $('#modal-archived-settlements-pagination').empty();
    $('#archivedSettlementsModal').appendTo('body').modal('show');
    loadArchivedSettlementsTable(page);
}

function loadArchivedSettlementsTable(page = 1) {
    const tbody = $('#modal-archived-settlements-tbody');
    const paginationId = 'modal-archived-settlements-pagination';
    showTableLoading(tbody, 6);

    $.post('exit_management.php', {
        ajax_action: 'get_archived_settlements',
        controller: 'settlement',
        page: page,
        limit: 10
    }, function(response) {
        tbody.empty();
        if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
            response.data.forEach(function(settlement) {
                const actions = `
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick='showArchivedSettlementDetails(${JSON.stringify(settlement)})' title="View Archived Settlement">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-success" onclick="unarchiveSettlement(${settlement.original_id || settlement.archive_id || settlement.id})" title="Restore Settlement">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                `;

                tbody.append(`
                    <tr>
                        <td>${settlement.employee_name || 'Unknown'}</td>
                        <td>${settlement.settlement_date || '-'}</td>
                        <td>${settlement.net_payable ? '$' + parseFloat(settlement.net_payable).toFixed(2) : '-'}</td>
                        <td>${settlement.status || '-'}</td>
                        <td>${settlement.archived_at || '-'}</td>
                        <td>${actions}</td>
                    </tr>
                `);
            });

            renderPagination(paginationId, response.total, page, response.limit || 10, (newPage) => `loadArchivedSettlementsTable(${newPage})`);
        } else {
            tbody.append('<tr><td colspan="6" class="text-center">No archived settlements found</td></tr>');
            $('#' + paginationId).empty();
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('Error loading archived settlements:', status, error, xhr.responseText);
        tbody.html('<tr><td colspan="6" class="text-center text-danger">Error loading archived settlements</td></tr>');
        $('#' + paginationId).empty();
    });
}

function showArchivedSettlementDetails(settlement) {
    const body = $('#viewArchivedSettlementBody');
    const netPayable = settlement.net_payable ? '$' + parseFloat(settlement.net_payable).toFixed(2) : '-';

    body.html(`
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Employee</label>
                    <input type="text" class="form-control" value="${(settlement.employee_name || 'Unknown').replace(/"/g, '&quot;')}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Settlement Date</label>
                    <input type="text" class="form-control" value="${(settlement.settlement_date || '-').replace(/"/g, '&quot;')}" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <input type="text" class="form-control" value="${(settlement.status || '-').replace(/"/g, '&quot;')}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Net Payable</label>
                    <input type="text" class="form-control" value="${netPayable.replace(/"/g, '&quot;')}" readonly>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Archive Reason</label>
            <textarea class="form-control" rows="3" readonly>${(settlement.archive_reason || 'No reason provided').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</textarea>
        </div>
        <div class="form-group">
            <label>Archived At</label>
            <input type="text" class="form-control" value="${(settlement.archived_at || '-').replace(/"/g, '&quot;')}" readonly>
        </div>
    `);

    $('#viewArchivedSettlementModal').appendTo('body').modal('show');
}

function onDocumentSearchChange() {
    const searchTerm = $('#document-search').val().toLowerCase();
    const status = $('#document-status-filter').val();
    loadDocumentsTable(status, 1, searchTerm);
}

function onSurveySearchChange() {
    const searchTerm = $('#survey-search').val().toLowerCase();
    const status = $('#survey-status-filter').val();
    loadSurveysTable(status, 1, searchTerm);
}

function archiveSettlement(id, triggerElement = null) {
    if (!id || Number(id) <= 0) {
        showToast('error', 'Invalid settlement ID, cannot archive this record.');
        return;
    }

    const employeeName = triggerElement ? (triggerElement.getAttribute('data-employee-name') || '') : '';

    $.post('exit_management.php', {
        ajax_action: 'check_settlement_archive_eligibility',
        controller: 'settlement',
        settlement_id: id
    }, function(response) {
        if (response && response.success) {
            $('#archiveSettlementForm')[0].reset();
            $('#archiveSettlementId').val(id);
            $('#archiveSettlementEmployeeName').val(employeeName);
            $('#archiveSettlementReason').val(getAutomatedArchiveReason());
            $('#archiveSettlementModal').appendTo('body').modal('show');
        } else {
            showToast('error', response.message || 'This settlement cannot be archived yet.');
        }
    }, 'json').fail(function() {
        showToast('error', 'Failed to verify archive eligibility for this settlement.');
    });
}

function unarchiveSettlement(id) {
    showConfirmation('Are you sure you want to unarchive this settlement?', function() {
        $.post('exit_management.php', {
            ajax_action: 'unarchive_settlement',
            controller: 'settlement',
            settlement_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadSettlementsTable();
                loadDashboardData();
                if (typeof loadArchivedSettlementsTable === 'function') {
                    loadArchivedSettlementsTable(1);
                }
            } else {
                showToast('error', response.message);
            }
        }, 'json');
    }, {
        confirmButtonText: 'Unarchive',
        confirmButtonClass: 'btn-success'
    });
}

function archiveInterview(id) {
    if (!id || Number(id) <= 0) {
        showToast('error', 'Invalid interview ID, cannot archive this record.');
        return;
    }

    // Get interview data first
    $.post('exit_management.php', {
        ajax_action: 'get_interview_details',
        controller: 'interview',
        interview_id: id
    }, function(response) {
        if (response && response.success) {
            // Populate modal with interview data
            $('#archiveInterviewId').val(id);
            $('#archiveInterviewEmployeeId').val(response.data.employee_id);
            $('#archiveInterviewEmployeeName').val(response.data.employee_name);
            $('#archiveInterviewReason').val(getAutomatedArchiveReason());
            $('#archiveInterviewNotes').val('');

            // Show modal
            $('#archiveInterviewModal').modal('show');
        } else {
            showToast('error', response?.message || 'Failed to load interview details');
        }
    }, 'json').fail(function(xhr, status, error) {
        showToast('error', 'Failed to load interview details: ' + (xhr.responseText || status || error));
    });
}

function unarchiveInterview(id) {
    showConfirmation('Are you sure you want to unarchive this interview?', function() {
        $.post('exit_management.php', {
            ajax_action: 'unarchive_interview',
            controller: 'interview',
            interview_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadInterviewsTable();
                loadDashboardData();
                loadArchivedInterviewsTable(archivedInterviewPage);
                loadArchivedInterviewSummary();
            } else {
                showToast('error', response.message);
            }
        }, 'json');
    }, {
        confirmButtonText: 'Unarchive',
        confirmButtonClass: 'btn-success'
    });
}

function archiveDocument(id) {
    // Get document data first
    $.post('exit_management.php', {
        ajax_action: 'get_document_details',
        controller: 'documentation',
        document_id: id
    }, function(response) {
        if (response.success) {
            // Populate modal with document data
            $('#archiveDocumentId').val(id);
            $('#archiveDocumentEmployeeId').val(response.data.employee_id);
            $('#archiveDocumentEmployeeName').val(response.data.employee_name);
            $('#archiveDocumentReason').val(getAutomatedArchiveReason());
            $('#archiveDocumentNotes').val('');

            // Show modal
            $('#archiveDocumentModal').modal('show');
        } else {
            showToast('error', 'Failed to load document details');
        }
    }, 'json');
}

function unarchiveDocument(id) {
    showConfirmation('Are you sure you want to unarchive this document?', function() {
        $.post('exit_management.php', {
            ajax_action: 'unarchive_document',
            controller: 'documentation',
            document_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadDocumentsTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        }, 'json');
    }, {
        confirmButtonText: 'Unarchive',
        confirmButtonClass: 'btn-success'
    });
}

function archiveSurvey(id) {
    // Get survey data first
    $.post('exit_management.php', {
        ajax_action: 'get_survey_details',
        controller: 'survey',
        survey_id: id
    }, function(response) {
        if (response.success) {
            // Populate modal with survey data
            $('#archiveSurveyId').val(id);
            $('#archiveSurveyEmployeeId').val(response.data.employee_id);
            $('#archiveSurveyEmployeeName').val(response.data.employee_name);
            $('#archiveSurveyReason').val(getAutomatedArchiveReason());
            $('#archiveSurveyNotes').val('');

            // Show modal
            $('#archiveSurveyModal').modal('show');
        } else {
            showToast('error', 'Failed to load survey details');
        }
    }, 'json');
}

function unarchiveSurvey(id) {
    showConfirmation('Are you sure you want to unarchive this survey?', function() {
        $.post('exit_management.php', {
            ajax_action: 'unarchive_survey',
            controller: 'survey',
            survey_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadSurveysTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        }, 'json');
    }, {
        confirmButtonText: 'Unarchive',
        confirmButtonClass: 'btn-success'
    });
}

function archiveTransferPlan(id) {
    // Get transfer plan data first
    $.post('exit_management.php', {
        ajax_action: 'get_transfer_plan',
        controller: 'transfer',
        plan_id: id
    }, function(response) {
        const payload = response && response.success && response.data ? response.data : response;
        if (payload && payload.employee_id) {
            // Populate modal with transfer plan data
            $('#archiveTransferPlanId').val(id);
            $('#archiveTransferPlanEmployeeId').val(payload.employee_id);
            $('#archiveTransferPlanEmployeeName').val(payload.employee_name || '');
            $('#archiveTransferPlanReason').val(getAutomatedArchiveReason());
            $('#archiveTransferPlanNotes').val('');

            // Show modal
            $('#archiveTransferPlanModal').modal('show');
        } else {
            showToast('error', 'Failed to load transfer plan details');
        }
    }, 'json');
}

function unarchiveTransferPlan(id) {
    showConfirmation('Are you sure you want to unarchive this transfer plan?', function() {
        $.post('exit_management.php', {
            ajax_action: 'unarchive_transfer_plan',
            controller: 'transfer',
            plan_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadTransfersTable();
                loadDashboardData();
                loadArchivedTransfersTable(archivedTransferPage);
                loadArchivedTransferSummary();
            } else {
                showToast('error', response.message);
            }
        }, 'json');
    }, {
        confirmButtonText: 'Unarchive',
        confirmButtonClass: 'btn-success'
    });
}

function archiveTransferItem(id) {
    // Get transfer item data first
    $.post('exit_management.php', {
        ajax_action: 'get_transfer_item_details',
        controller: 'transfer',
        item_id: id
    }, function(response) {
        if (response.success) {
            // Populate modal with transfer item data
            $('#archiveTransferItemId').val(id);
            $('#archiveTransferItemEmployeeId').val(response.data.employee_id);
            $('#archiveTransferItemEmployeeName').val(response.data.employee_name);
            $('#archiveTransferItemReason').val(getAutomatedArchiveReason());
            $('#archiveTransferItemNotes').val('');

            // Show modal
            $('#archiveTransferItemModal').modal('show');
        } else {
            showToast('error', 'Failed to load transfer item details');
        }
    }, 'json');
}

function unarchiveTransferItem(id) {
    showConfirmation('Are you sure you want to unarchive this transfer item?', function() {
        $.post('exit_management.php', {
            ajax_action: 'unarchive_transfer_item',
            controller: 'transfer',
            item_id: id
        }, function(response) {
            if (response.success) {
                showToast('success', response.message);
                loadTransfersTable();
                loadDashboardData();
            } else {
                showToast('error', response.message);
            }
        }, 'json');
    }, {
        confirmButtonText: 'Unarchive',
        confirmButtonClass: 'btn-success'
    });
}

// Load termination trend chart
function loadTerminationTrendChart() {
    $.ajax({
        url: 'exit_management.php',
        method: 'POST',
        dataType: 'json',
        timeout: 30000,
        data: {
            ajax_action: 'get_termination_trend'
        },
        success: function(response) {
            if (response && response.labels && response.data) {
                renderTerminationTrendChart(response.labels, response.data);
            }
        },
        error: function(xhr, status, errorThrown) {
            console.error('Error loading termination trend:', status, errorThrown, xhr.status, xhr.statusText, xhr.responseText);
        }
    });
}

function renderTerminationTrendChart(labels, data) {
    const ctx = document.getElementById('terminationTrendChart');
    if (!ctx) return;

    if (charts.terminationTrend) charts.terminationTrend.destroy();

    charts.terminationTrend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Terminations',
                data: data,
                borderColor: '#c0392b',
                backgroundColor: 'rgba(192, 57, 43, 0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
}

// Load termination status distribution
function loadTerminationStatusChart() {
    $.ajax({
        url: 'exit_management.php',
        method: 'POST',
        dataType: 'json',
        timeout: 30000,
        data: {
            ajax_action: 'get_termination_status'
        },
        success: function(response) {
            if (response && response.labels && response.data) {
                renderTerminationStatusChart(response.labels, response.data);
            }
        },
        error: function(xhr, status, errorThrown) {
            console.error('Error loading termination status:', status, errorThrown, xhr.status, xhr.statusText, xhr.responseText);
        }
    });
}

function renderTerminationStatusChart(labels, data) {
    const ctx = document.getElementById('terminationStatusChart');
    if (!ctx) return;

    if (charts.terminationStatus) charts.terminationStatus.destroy();

    const colors = ['#2ecc71', '#f39c12', '#e74c3c', '#9b59b6', '#3498db'];

    charts.terminationStatus = new Chart(ctx, {
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
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
}

// Archive termination form handler
$(document).ready(function() {
    $('#archiveTerminationForm').on('submit', function(e) {
        e.preventDefault();
        console.log('[ArchiveTermination] submit handler fired');

        const terminationId = $('#archiveTerminationId').val();
        const archiveReason = getAutomatedArchiveReason();

        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).append(' <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

        $.post('exit_management.php', {
            ajax_action: 'archive_termination',
            controller: 'termination',
            termination_id: terminationId,
            archive_reason: archiveReason
        }, function(response) {
            if (response && response.success) {
                showToast('success', response.message || 'Termination archived successfully.');
                $('#archiveTerminationModal').modal('hide');
                loadTerminationsTable();
                loadArchivedTerminationsTable(1, $('#archivedTerminationsModal').is(':visible'));
            } else {
                showToast('error', (response && response.message) || 'Failed to archive termination.');
            }
        }, 'json').fail(function(xhr, status, error) {
            console.error('Error archiving termination:', status, error, xhr.responseText);
            showToast('error', 'Error archiving termination.');
        }).always(function() {
            $submitBtn.prop('disabled', false).find('.spinner-border').remove();
        });
    });
});

// Open archived terminations modal
function openArchivedTerminationsModal(page = 1) {
    $('#modal-archived-terminations-tbody').html('<tr><td colspan="7" class="text-center text-muted">Loading archived terminations...</td></tr>');
    $('#modal-archived-terminations-pagination').empty();
    $('#archivedTerminationsModal').appendTo('body').modal('show');
    loadArchivedTerminationsTable(page, true);
}

// Load archived terminations into inline or modal table
function loadArchivedTerminationsTable(page = 1, inModal = false) {
    const tbody = inModal ? $('#modal-archived-terminations-tbody') : $('#archived-terminations-tbody');
    const paginationId = inModal ? 'modal-archived-terminations-pagination' : 'archived-terminations-pagination';
    const noDataCols = 7;
    showTableLoading(tbody, noDataCols);

    $.post('exit_management.php', {
        ajax_action: 'get_archived_terminations',
        controller: 'termination',
        page: page,
        limit: 10
    }, function(response) {
        tbody.empty();
        if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
            response.data.forEach(function(termination) {
                const actions = `
                    <div class="table-actions">
                        <button class="btn btn-sm btn-success action-button" onclick="unarchiveTermination(${termination.id})" title="Unarchive Termination">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                `;

                if (inModal) {
                    tbody.append(`
                        <tr>
                            <td>${termination.employee_name || '<em class="text-danger">Missing Employee</em>'}</td>
                            <td>${termination.department || '-'}</td>
                            <td>${termination.email || '-'}</td>
                            <td>${termination.position || '-'}</td>
                            <td>${termination.reason || '-'}</td>
                            <td>${termination.effective_date || '-'}</td>
                            <td>${actions}</td>
                        </tr>
                    `);
                } else {
                    tbody.append(`
                        <tr>
                            <td>${termination.employee_name || '<em class="text-danger">Missing Employee</em>'}</td>
                            <td>${termination.department || '-'}</td>
                            <td>${termination.email || '-'}</td>
                            <td>${termination.position || '-'}</td>
                            <td>${termination.reason || '-'}</td>
                            <td>${termination.effective_date || '-'}</td>
                            <td class="actions-cell">${actions}</td>
                        </tr>
                    `);
                }
            });

            renderPagination(paginationId, response.total, page, response.limit || 10, (newPage) => loadArchivedTerminationsTable(newPage, inModal));
        } else {
            tbody.append(`<tr><td colspan="${noDataCols}" class="text-center">No archived terminations found</td></tr>`);
            $(`#${paginationId}`).empty();
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('Error loading archived terminations:', status, error, xhr.responseText);
        tbody.html(`<tr><td colspan="${noDataCols}" class="text-center text-danger">Error loading archived terminations</td></tr>`);
        $(`#${paginationId}`).empty();
    });
}