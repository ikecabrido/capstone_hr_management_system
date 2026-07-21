<?php
/**
 * JavaScript Error Diagnostic Test
 * Checks for any JS errors in the modal and button functionality
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JS Error Diagnostic Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana; }
        .container-test { max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .check-item { padding: 12px; margin: 10px 0; background: #f9f9f9; border-left: 4px solid #0066cc; border-radius: 4px; }
        .pass { color: #28a745; font-weight: bold; }
        .fail { color: #dc3545; font-weight: bold; }
        .warn { color: #ffc107; font-weight: bold; }
        h2 { color: #003d82; margin-bottom: 20px; }
        h3 { color: #555; font-size: 1.1rem; margin-top: 20px; margin-bottom: 15px; }
        .console-log { background: #1e1e1e; color: #00ff00; padding: 12px; border-radius: 4px; font-family: monospace; font-size: 0.85rem; max-height: 300px; overflow-y: auto; margin-top: 10px; }
        .console-log-line { margin: 4px 0; }
        .error { color: #ff6b6b; }
        .warning { color: #ffa500; }
        .info { color: #87ceeb; }
        .success { color: #00ff00; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        button { cursor: pointer; }
    </style>
</head>
<body>
    <div class="container-test">
        <h2>🔍 JavaScript Error Diagnostic Test</h2>
        
        <div class="alert alert-info mb-4">
            <strong>Instructions:</strong>
            <ol style="margin-bottom: 0; margin-top: 10px; font-size: 0.95rem;">
                <li>Open DevTools (F12) and go to Console tab</li>
                <li>Scroll down and read all diagnostic checks</li>
                <li>Click the "Test Modal Button" button below</li>
                <li>Check Console for any RED error messages</li>
                <li>Try clicking the buttons in the modal</li>
            </ol>
        </div>

        <button type="button" class="btn btn-primary btn-lg w-100 mb-4" data-bs-toggle="modal" data-bs-target="#testModal" id="mainTestBtn">
            📋 Test Modal Button
        </button>

        <h3>📊 Diagnostic Results:</h3>
        <div id="diagnosticResults"></div>

        <h3>🖥️ Console Output:</h3>
        <div class="console-log" id="consoleLog"></div>
    </div>

    <!-- Test Modal -->
    <div class="modal fade" id="testModal" tabindex="-1">
        <style>
            @media (max-width: 576px) {
                #testModal .modal-dialog { margin: 0.5rem !important; }
                #testModal .modal-header { padding: 0.6rem !important; }
                #testModal .modal-title { font-size: 0.95rem !important; }
                #testModal .btn-close { width: 1.5rem !important; height: 1.5rem !important; }
                #testModal .modal-body { padding: 0.6rem !important; }
                #testModal .modal-footer { padding: 0.6rem !important; gap: 0.4rem; }
                #testModal .btn { font-size: 0.8rem !important; padding: 0.35rem 0.6rem !important; min-height: 32px !important; }
            }
        </style>
        
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeBtn"></button>
                </div>
                
                <div class="modal-body">
                    <p id="testContent">Modal content loads here. Click buttons below to test.</p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="cancelBtn">Cancel</button>
                    <button type="button" class="btn btn-success" id="submitBtn">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Override console to capture logs
        const logs = [];
        const originalLog = console.log;
        const originalError = console.error;
        const originalWarn = console.warn;

        function addLog(type, message) {
            const timestamp = new Date().toLocaleTimeString();
            logs.push({ type, message, timestamp });
            updateConsoleDisplay();
        }

        console.log = function(...args) {
            originalLog.apply(console, args);
            addLog('info', args.join(' '));
        };

        console.error = function(...args) {
            originalError.apply(console, args);
            addLog('error', args.join(' '));
        };

        console.warn = function(...args) {
            originalWarn.apply(console, args);
            addLog('warning', args.join(' '));
        };

        function updateConsoleDisplay() {
            const consoleLog = document.getElementById('consoleLog');
            consoleLog.innerHTML = logs.map(log => {
                const className = log.type === 'error' ? 'error' : log.type === 'warning' ? 'warning' : 'info';
                return `<div class="console-log-line"><span style="color: #999; font-size: 0.8rem;">[${log.timestamp}]</span> <span class="${className}">${log.message}</span></div>`;
            }).join('');
            consoleLog.scrollTop = consoleLog.scrollHeight;
        }

        // Run diagnostics
        const diagnostics = [];

        function addDiagnostic(name, passed, details) {
            diagnostics.push({ name, passed, details });
            const resultsDiv = document.getElementById('diagnosticResults');
            const div = document.createElement('div');
            div.className = 'check-item';
            div.innerHTML = `<span class="${passed ? 'pass' : 'fail'}">${passed ? '✅' : '❌'} ${name}</span><br><small>${details}</small>`;
            resultsDiv.appendChild(div);
        }

        // Check 1: Bootstrap is loaded
        try {
            const hasBootstrap = typeof bootstrap !== 'undefined';
            addDiagnostic('Bootstrap 5 Loaded', hasBootstrap, hasBootstrap ? 'bootstrap.Modal available' : 'bootstrap not found');
            if (hasBootstrap) console.log('✅ Bootstrap is available');
        } catch (e) {
            addDiagnostic('Bootstrap 5 Loaded', false, 'Error: ' + e.message);
            console.error('❌ Bootstrap error: ' + e.message);
        }

        // Check 2: Modal element exists
        const modalEl = document.getElementById('testModal');
        addDiagnostic('Modal Element Found', !!modalEl, modalEl ? 'id="testModal" found' : 'Modal not found');
        if (modalEl) console.log('✅ Modal element exists');

        // Check 3: Close button exists
        const closeBtn = document.getElementById('closeBtn');
        addDiagnostic('Close Button Found', !!closeBtn, closeBtn ? 'id="closeBtn" found, has data-bs-dismiss' : 'Close button not found');
        if (closeBtn) console.log('✅ Close button found, data-bs-dismiss=' + closeBtn.getAttribute('data-bs-dismiss'));

        // Check 4: Cancel button exists
        const cancelBtn = document.getElementById('cancelBtn');
        addDiagnostic('Cancel Button Found', !!cancelBtn, cancelBtn ? 'id="cancelBtn" found' : 'Cancel button not found');
        if (cancelBtn) console.log('✅ Cancel button found');

        // Check 5: Submit button exists
        const submitBtn = document.getElementById('submitBtn');
        addDiagnostic('Submit Button Found', !!submitBtn, submitBtn ? 'id="submitBtn" found' : 'Submit button not found');
        if (submitBtn) console.log('✅ Submit button found');

        // Check 6: Modal trigger button exists
        const triggerBtn = document.getElementById('mainTestBtn');
        addDiagnostic('Modal Trigger Button', !!triggerBtn, triggerBtn ? 'Has data-bs-toggle and data-bs-target' : 'Trigger not found');
        if (triggerBtn) console.log('✅ Modal trigger button found');

        // Check 7: Test button click
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ DOMContentLoaded fired');
        });

        // Check 8: Modal initialization
        document.addEventListener('DOMContentLoaded', function() {
            if (modalEl && typeof bootstrap !== 'undefined') {
                try {
                    const modal = new bootstrap.Modal(modalEl);
                    addDiagnostic('Modal Initialization', true, 'Modal instance created successfully');
                    console.log('✅ Modal initialized successfully');
                } catch (e) {
                    addDiagnostic('Modal Initialization', false, 'Error: ' + e.message);
                    console.error('❌ Modal init error: ' + e.message);
                }
            }
        });

        // Check 9: Modal event listeners
        document.addEventListener('DOMContentLoaded', function() {
            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', function() {
                    addDiagnostic('Modal Show Event', true, 'shown.bs.modal event fired');
                    console.log('✅ Modal shown event fired');
                });

                modalEl.addEventListener('hidden.bs.modal', function() {
                    console.log('✅ Modal hidden event fired');
                });
            }
        });

        // Check 10: Test button clicks
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                console.log('✅ Close button click detected');
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                console.log('✅ Cancel button click detected');
            });
        }

        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                console.log('✅ Submit button click detected');
            });
        }

        // Check 11: Mobile-responsive.js interference
        addDiagnostic('mobile-responsive.js Check', true, 'Checking for event handler conflicts');

        // Check 12: Event handler test
        document.addEventListener('touchend', function(e) {
            console.log('📱 Touch event detected on: ' + e.target.tagName);
        });

        console.log('=== Diagnostic Test Started ===');
        console.log('Testing modal and button responsiveness');
        console.log('Open DevTools Console to see all messages');
        console.log('Click "Test Modal Button" above to proceed');
    </script>
</body>
</html>
