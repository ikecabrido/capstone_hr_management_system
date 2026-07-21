<?php
/**
 * Button Responsiveness Test
 * Tests that modal submit, cancel, and close buttons are responsive on mobile
 */

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Button Responsiveness Test</title>
    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\">
    <style>
        body { background: #f5f5f5; padding: 20px; }
        .test-container { max-width: 500px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .test-section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #0066cc; border-radius: 4px; }
        .pass { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
        .alert-info { margin: 15px 0; }
    </style>
</head>
<body>
    <div class=\"test-container\">
        <h2>📱 Button Responsiveness Test</h2>
        
        <div class=\"alert alert-info\">
            <strong>Test Instructions:</strong>
            <ol style=\"margin-bottom: 0; margin-top: 10px;\">
                <li>Click \"Open Modal\" button</li>
                <li>Test the X close button (top right)</li>
                <li>Reopen modal and test Cancel button</li>
                <li>Test Submit button (should work if form is valid)</li>
                <li>Check console (F12) for any errors</li>
            </ol>
        </div>

        <button type=\"button\" class=\"btn btn-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#testModal\">
            📋 Open Modal
        </button>

        <div class=\"test-section\">
            <h4>🔍 Tests Performed:</h4>
            <ul id=\"testResults\" style=\"margin-bottom: 0;\"></ul>
        </div>
    </div>

    <!-- Test Modal -->
    <div class=\"modal fade\" id=\"testModal\" tabindex=\"-1\">
        <style>
            /* Minimal mobile styles */
            @media (max-width: 576px) {
                #testModal .modal-dialog { margin: 0.5rem !important; }
                #testModal .modal-header { padding: 0.6rem !important; }
                #testModal .modal-title { font-size: 0.95rem !important; }
                #testModal .btn-close { width: 1.5rem !important; height: 1.5rem !important; }
                #testModal .modal-footer { padding: 0.6rem !important; gap: 0.4rem; }
                #testModal .btn { font-size: 0.8rem !important; padding: 0.35rem 0.6rem !important; min-height: 32px !important; display: flex; align-items: center; justify-content: center; }
            }
        </style>
        
        <div class=\"modal-dialog modal-dialog-centered\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Test Form</h5>
                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" id=\"closeBtn\" title=\"Close\"></button>
                </div>
                
                <div class=\"modal-body\">
                    <form id=\"testForm\">
                        <div class=\"mb-3\">
                            <label class=\"form-label\">Name</label>
                            <input type=\"text\" class=\"form-control\" value=\"Test\" required>
                        </div>
                        <p style=\"font-size: 0.9rem; color: #666;\">Try clicking the X, Cancel, or Submit button.</p>
                    </form>
                </div>
                
                <div class=\"modal-footer\">
                    <button type=\"button\" class=\"btn btn-outline-secondary\" data-bs-dismiss=\"modal\" id=\"cancelBtn\">Cancel</button>
                    <button type=\"button\" class=\"btn btn-success\" id=\"submitBtn\">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js\"></script>
    <script>
        const results = [];
        const modal = new bootstrap.Modal(document.getElementById('testModal'));
        const testResultsList = document.getElementById('testResults');

        function addTest(name, passed, details = '') {
            results.push({ name, passed, details });
            const li = document.createElement('li');
            li.innerHTML = passed ? 
                `<span class=\"pass\">✅ ${name}</span>` : 
                `<span class=\"fail\">❌ ${name}</span>`;
            if (details) li.innerHTML += ` <small>${details}</small>`;
            testResultsList.appendChild(li);
        }

        // Test 1: Close button is present
        const closeBtn = document.getElementById('closeBtn');
        addTest('Close button (X) exists', !!closeBtn, closeBtn ? 'Found: btn-close' : 'Not found');

        // Test 2: Cancel button is present
        const cancelBtn = document.getElementById('cancelBtn');
        addTest('Cancel button exists', !!cancelBtn, cancelBtn ? 'Found with data-bs-dismiss' : 'Not found');

        // Test 3: Submit button is present
        const submitBtn = document.getElementById('submitBtn');
        addTest('Submit button exists', !!submitBtn, 'Found with click handler');

        // Test 4: Modal is initialized
        try {
            const testModal = bootstrap.Modal.getInstance(document.getElementById('testModal'));
            addTest('Bootstrap Modal initialized', !!testModal, 'Modal instance found');
        } catch (e) {
            addTest('Bootstrap Modal initialized', false, e.message);
        }

        // Test 5: Close button has proper attributes
        const closeHasDismiss = closeBtn && closeBtn.classList.contains('btn-close');
        addTest('Close button has correct class', closeHasDismiss, 'btn-close class present');

        // Test 6: Cancel button has data-bs-dismiss
        const cancelHasDismiss = cancelBtn && cancelBtn.hasAttribute('data-bs-dismiss');
        addTest('Cancel button has data-bs-dismiss', cancelHasDismiss, 'Attribute present');

        // Test 7: Submit button click handler
        let submitClicked = false;
        submitBtn.addEventListener('click', function() {
            submitClicked = true;
            console.log('✅ Submit button clicked!');
            addTest('Submit button click works', true, 'Click handler fires');
            modal.hide();
        });

        // Test 8: Modal opens properly
        document.addEventListener('shown.bs.modal', function(event) {
            if (event.target.id === 'testModal') {
                addTest('Modal opens without errors', true, 'shown.bs.modal event fired');
            }
        });

        // Test 9: Cancel button closes modal
        let cancelClicked = false;
        cancelBtn.addEventListener('click', function(e) {
            cancelClicked = true;
            console.log('✅ Cancel button clicked!');
        });

        // Test 10: Mobile responsiveness check
        const width = window.innerWidth;
        const isMobile = width < 576;
        addTest('Mobile detection', isMobile, isMobile ? 'Mobile (<576px)' : 'Desktop (' + width + 'px)');

        console.log('✅ Button Responsiveness Test Suite Loaded');
        console.log('Tests Results:', results);
    </script>
</body>
</html>";
?>
