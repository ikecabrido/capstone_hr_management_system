<div class="modal fade" id="leaveRequestModal" tabindex="-1">
    <style>
        /* Mobile-responsive modal styling */
        @media (max-width: 576px) {
            #leaveRequestModal .modal-dialog {
                margin: 0.5rem !important;
            }
            
            #leaveRequestModal .modal-content {
                padding: 0 !important;
            }
            
            #leaveRequestModal .modal-header {
                padding: 0.6rem !important;
            }
            
            #leaveRequestModal .modal-title {
                font-size: 0.95rem !important;
                font-weight: 600;
            }
            
            /* Close button styling */
            #leaveRequestModal .btn-close {
                width: 1.5rem !important;
                height: 1.5rem !important;
                opacity: 0.8 !important;
            }
            
            #leaveRequestModal .btn-close:hover,
            #leaveRequestModal .btn-close:focus {
                opacity: 1 !important;
            }
            
            #leaveRequestModal .modal-body {
                padding: 0.6rem !important;
            }
            
            #leaveRequestModal .modal-footer {
                padding: 0.6rem !important;
                gap: 0.4rem;
                flex-wrap: nowrap;
            }
            
            /* Form labels */
            #leaveRequestModal .form-label {
                font-size: 0.75rem !important;
                margin-bottom: 0.2rem !important;
                font-weight: 600;
            }
            
            /* Form controls */
            #leaveRequestModal .form-control,
            #leaveRequestModal .form-select {
                font-size: 0.8rem !important;
                padding: 0.35rem 0.5rem !important;
                height: auto !important;
                min-height: 1.8rem !important;
            }
            
            /* Input field height for touch targets */
            #leaveRequestModal input[type="text"],
            #leaveRequestModal input[type="date"],
            #leaveRequestModal select,
            #leaveRequestModal textarea {
                font-size: 0.8rem !important;
                padding: 0.4rem !important;
                line-height: 1.2 !important;
            }
            
            /* Textarea */
            #leaveRequestModal textarea {
                min-height: 50px !important;
                resize: vertical;
            }
            
            /* Helper text */
            #leaveRequestModal .text-muted {
                font-size: 0.7rem !important;
            }
            
            /* Alert messages */
            #leaveRequestModal .alert {
                padding: 0.4rem 0.6rem !important;
                margin-bottom: 0.6rem !important;
                font-size: 0.75rem !important;
            }
            
            #leaveRequestModal .alert h3 {
                font-size: 0.8rem !important;
                margin-bottom: 0.2rem !important;
            }
            
            /* Buttons - ENSURE TOUCHABLE */
            #leaveRequestModal .btn {
                font-size: 0.8rem !important;
                padding: 0.35rem 0.6rem !important;
                border-radius: 0.25rem !important;
                white-space: nowrap;
                min-height: 32px !important;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                touch-action: manipulation;
                -webkit-user-select: none;
                user-select: none;
            }
            
            #leaveRequestModal .btn-lg {
                font-size: 0.8rem !important;
                padding: 0.35rem 0.6rem !important;
            }
            
            /* Row spacing */
            #leaveRequestModal .row {
                gap: 0.75rem !important;
            }
            
            #leaveRequestModal .col-md-6 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
            
            /* Margins and spacing */
            #leaveRequestModal .mb-3,
            #leaveRequestModal .mb-4 {
                margin-bottom: 0.75rem !important;
            }
            
            #leaveRequestModal .mt-3 {
                margin-top: 0.75rem !important;
            }
        }
        
        /* Tablet sizing (576px - 768px) */
        @media (min-width: 577px) and (max-width: 768px) {
            #leaveRequestModal .modal-title {
                font-size: 1.3rem !important;
            }
            
            #leaveRequestModal .form-label {
                font-size: 0.9rem !important;
            }
            
            #leaveRequestModal .form-control,
            #leaveRequestModal .form-select {
                font-size: 0.9rem !important;
                padding: 0.5rem 0.75rem !important;
            }
            
            #leaveRequestModal .btn {
                font-size: 0.9rem !important;
                padding: 0.5rem 1rem !important;
            }
        }
        
        /* Desktop - no changes needed */
        @media (min-width: 769px) {
            #leaveRequestModal .modal-lg {
                max-width: 700px !important;
            }
        }
    </style>
    
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-3xl">Request Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="leaveRequestForm" method="POST" action="index.php?url=leave-request-store" enctype="multipart/form-data">
                <div class="modal-body px-4 py-3">
                    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?? '' ?>">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Leave Type</label>
                        <select name="leave_type_id" id="leaveTypeSelect" class="form-select shadow-sm" required>
                            <option value="" disabled selected>Choose leave type...</option>
                            <?php foreach ($allLeaveTypes as $type): ?>
                                <option value="<?= $type['leave_type_id'] ?>"><?= htmlspecialchars($type['leave_type_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control shadow-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control shadow-sm" required>
                        </div>
                    </div>

                    <!-- 2-week advance warning for vacation leave -->
                    <div id="vacationWarning" class="alert alert-warning d-none mt-3" role="alert">
                        <strong>⚠️ Vacation Leave Notice:</strong> Vacation leave must be filed at least 2 weeks in advance.
                    </div>

                    <div class="mb-4 mt-3">
                        <label class="form-label fw-semibold">Reason for Leave</label>
                        <textarea
                            name="reason"
                            class="form-control shadow-sm"
                            rows="3"
                            placeholder="Provide a brief explanation..."
                            required></textarea>
                        <small class="text-muted">Be clear and concise.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Additional Details</label>
                        <textarea
                            name="details"
                            class="form-control shadow-sm"
                            rows="2"
                            placeholder="Any additional information..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Supporting Documents</label>
                        <small class="text-muted d-block mb-2">Upload medical certificate, death certificate, or other required documents</small>
                        <input type="file" name="documents[]" id="documentUpload" class="form-control shadow-sm" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">Max 5MB per file. Accepted: PDF, DOC, DOCX, JPG, PNG</small>
                    </div>

                </div>

                <div class="modal-footer px-4 py-3 border-top-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" id="submitLeaveBtn" class="btn btn-success px-4 shadow-sm">
                        Submit Request
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const leaveTypeSelect = document.getElementById('leaveTypeSelect');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const vacationWarning = document.getElementById('vacationWarning');
    const leaveForm = document.getElementById('leaveRequestForm');

    // Function to format date as YYYY-MM-DD
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Function to set minimum date based on leave type
    function setMinimumDate() {
        const today = new Date();
        const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
        const leaveTypeName = selectedOption.text.toLowerCase();

        if (leaveTypeName.includes('vacation')) {
            // For vacation leave, minimum date is 14 days from today
            const minimumDate = new Date(today);
            minimumDate.setDate(minimumDate.getDate() + 14);
            startDateInput.min = formatDate(minimumDate);
            vacationWarning.classList.remove('d-none');
        } else {
            // For other leave types, allow from today onwards
            startDateInput.min = formatDate(today);
            vacationWarning.classList.add('d-none');
        }
    }

    // Check 2-week advance requirement for vacation leave and update min date
    function checkVacationAdvance() {
        const leaveTypeId = leaveTypeSelect.value;
        const startDate = new Date(startDateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Get leave type name
        const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
        const leaveTypeName = selectedOption.text.toLowerCase();
        
        if (leaveTypeName.includes('vacation')) {
            const daysAdvance = Math.floor((startDate - today) / (1000 * 60 * 60 * 24));
            
            if (daysAdvance < 14) {
                vacationWarning.classList.remove('d-none');
            } else {
                vacationWarning.classList.add('d-none');
            }
        } else {
            vacationWarning.classList.add('d-none');
        }
    }

    // Initialize minimum date on page load
    leaveTypeSelect.addEventListener('change', function() {
        setMinimumDate();
        checkVacationAdvance();
    });

    startDateInput.addEventListener('change', checkVacationAdvance);
    endDateInput.addEventListener('change', function() {
        // Calculate duration
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(this.value);
        if (startDate && endDate && endDate >= startDate) {
            const days = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
            console.log('Leave duration: ' + days + ' days');
        }
    });

    // Validate form before submit
    leaveForm.addEventListener('submit', function(e) {
        const leaveTypeId = leaveTypeSelect.value;
        const startDate = new Date(startDateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
        const leaveTypeName = selectedOption.text.toLowerCase();

        if (leaveTypeName.includes('vacation')) {
            const daysAdvance = Math.floor((startDate - today) / (1000 * 60 * 60 * 24));
            
            if (daysAdvance < 14) {
                e.preventDefault();
                alert('❌ Vacation Leave must be filed at least 2 weeks in advance. Please select a date that is 14 days or more from today.');
                return false;
            }
        }
    });

    // Set initial minimum date on page load
    const today = new Date();
    startDateInput.min = formatDate(today);
});

// Ensure modal trigger works - explicit initialization
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('leaveRequestModal');
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        // Initialize modal
        new bootstrap.Modal(modalEl, {
            backdrop: 'static',
            keyboard: true
        });
        
        // Log for debugging
        console.log('✅ Leave Request Modal initialized');
    }
    
    // Also ensure button click works
    const modalButtons = document.querySelectorAll('[data-bs-target="#leaveRequestModal"]');
    modalButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            console.log('Leave request button clicked');
            // The data-bs-toggle should handle it, but this helps with debugging
        });
    });
});
</script>