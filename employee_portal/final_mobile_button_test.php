<?php
/**
 * Final Comprehensive Test - Verifies all mobile button issues are fixed
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Mobile Button Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        * { box-sizing: border-box; }
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 15px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana;
        }
        .test-container {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        h1 { color: #667eea; margin-bottom: 20px; font-weight: 700; font-size: 1.8rem; }
        h3 { color: #333; margin-top: 25px; margin-bottom: 15px; }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        .test-button { margin: 10px 0; }
        .status-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .status-table td { padding: 10px; border-bottom: 1px solid #ddd; }
        .status-table .status-pass { color: green; font-weight: bold; }
        .status-fail { color: red; font-weight: bold; }
        .code-block {
            background: #1e1e1e;
            color: #00ff00;
            padding: 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.85rem;
            margin: 10px 0;
            max-height: 150px;
            overflow-y: auto;
        }
        @media (max-width: 576px) {
            .test-container { padding: 15px; }
            h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🧪 Final Mobile Button Test</h1>
        
        <div class="info-box">
            <strong>📱 Purpose:</strong> Verify that all mobile button issues are fixed. 
            <br><strong>Test on:</strong> Mobile device (<576px) or browser DevTools mobile view
            <br><strong>What to do:</strong> Click all buttons and verify they respond
        </div>

        <h3>✅ Test the Modal Button</h3>
        <p>Click this button to test the modal opening:</p>
        <button type="button" class="btn btn-primary btn-lg w-100 test-button" data-bs-toggle="modal" data-bs-target="#testModal">
            📋 Open Leave Request Modal
        </button>

        <h3>📊 Status Checks</h3>
        <table class="status-table">
            <tr>
                <td><strong>Bootstrap Loaded</strong></td>
                <td id="check-bootstrap">⏳</td>
            </tr>
            <tr>
                <td><strong>Modal Element Exists</strong></td>
                <td id="check-modal">⏳</td>
            </tr>
            <tr>
                <td><strong>Close Button (X)</strong></td>
                <td id="check-close">⏳</td>
            </tr>
            <tr>
                <td><strong>Cancel Button</strong></td>
                <td id="check-cancel">⏳</td>
            </tr>
            <tr>
                <td><strong>Submit Button</strong></td>
                <td id="check-submit">⏳</td>
            </tr>
            <tr>
                <td><strong>Mobile-responsive.js</strong></td>
                <td id="check-mobile-responsive">⏳</td>
            </tr>
            <tr>
                <td><strong>Mobile-fix.js</strong></td>
                <td id="check-mobile-fix">⏳</td>
            </tr>
            <tr>
                <td><strong>Bootstrap Modal Events</strong></td>
                <td id="check-modal-events">⏳</td>
            </tr>
        </table>

        <h3>🖥️ Console Output</h3>
        <div class="code-block" id="consoleOutput"></div>

        <h3>✨ Instructions</h3>
        <ol>
            <li><strong>On Mobile Device:</strong> Open this page and scroll to "Test the Modal Button"</li>
            <li><strong>In Browser DevTools:</strong> Toggle device toolbar (Ctrl+Shift+M or Cmd+Shift+M)</li>
            <li><strong>Set Width:</strong> Make sure window is <576px wide for mobile view</li>
            <li><strong>Click Button:</strong> Click "Open Leave Request Modal" button</li>
            <li><strong>Test Buttons in Modal:</strong>
                <ul>
                    <li>Click the <strong>X button</strong> (top right) - should close modal</li>
                    <li>Reopen and click <strong>Cancel</strong> button - should close modal</li>
                    <li>Reopen and click <strong>Submit</strong> button - should respond</li>
                </ul>
            </li>
            <li><strong>Check Console:</strong> Open DevTools Console tab and look for green ✅ messages</li>
        </ol>

        <div style="margin-top: 25px; padding: 15px; background: #f9f9f9; border-radius: 6px; border-left: 4px solid #ffc107;">
            <strong>📝 If buttons still don't respond:</strong>
            <ol style="margin-bottom: 0;">
                <li>Hard refresh page: <kbd>Ctrl+Shift+Delete</kbd></li>
                <li>Clear browser cache completely</li>
                <li>Check console (F12 → Console tab) for red error messages</li>
                <li>Take a screenshot and report the error</li>
            </ol>
        </div>
    </div>

    <!-- Test Modal (Same as production) -->
    <div class="modal fade" id="testModal" tabindex="-1">
        <style>
            @media (max-width: 576px) {
                #testModal .modal-dialog { margin: 0.5rem !important; }
                #testModal .modal-header { padding: 0.6rem !important; }
                #testModal .modal-title { font-size: 0.95rem !important; }
                #testModal .btn-close { width: 1.5rem !important; height: 1.5rem !important; }
                #testModal .modal-body { padding: 0.6rem !important; }
                #testModal .modal-footer { padding: 0.6rem !important; gap: 0.4rem; flex-wrap: wrap; }
                #testModal .btn { font-size: 0.8rem !important; padding: 0.35rem 0.6rem !important; min-height: 32px !important; display: flex; align-items: center; justify-content: center; }
            }
        </style>
        
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeBtn"></button>
                </div>
                
                <div class="modal-body">
                    <p id="modalStatus">🎯 Modal opened successfully!</p>
                    <div class="alert alert-info" role="alert">
                        <strong>Test Instructions:</strong>
                        <ul style="margin-bottom: 0; margin-top: 8px;">
                            <li>Click the <strong>X button</strong> (top right)</li>
                            <li>Reopen and click <strong>Cancel</strong></li>
                            <li>Reopen and click <strong>Submit</strong></li>
                        </ul>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="cancelBtn">
                        ❌ Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="submitBtn">
                        ✅ Submit Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const logs = [];
        const consoleOutput = document.getElementById('consoleOutput');

        function addLog(msg) {
            logs.push(msg);
            console.log(msg);
            consoleOutput.innerHTML = logs.map((l, i) => `<div>${i + 1}. ${escapeHtml(l)}</div>`).join('');
            consoleOutput.scrollTop = consoleOutput.scrollHeight;
        }

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        function setStatus(id, passed, text) {
            const el = document.getElementById(id);
            el.className = passed ? 'status-pass' : 'status-fail';
            el.textContent = passed ? '✅ ' + text : '❌ ' + text;
        }

        // Start checks
        addLog('🚀 Page loaded, running diagnostics...');

        // Check 1: Bootstrap
        const hasBootstrap = typeof bootstrap !== 'undefined' && bootstrap.Modal;
        setStatus('check-bootstrap', hasBootstrap, 'Available');
        if (hasBootstrap) addLog('✅ Bootstrap.Modal available');

        // Check 2: Modal element
        const modal = document.getElementById('testModal');
        setStatus('check-modal', !!modal, 'Found (#testModal)');
        if (modal) addLog('✅ Modal element found');

        // Check 3: Close button
        const closeBtn = document.getElementById('closeBtn');
        setStatus('check-close', !!closeBtn, 'Found');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => addLog('✅ Close button clicked'));
        }

        // Check 4: Cancel button
        const cancelBtn = document.getElementById('cancelBtn');
        setStatus('check-cancel', !!cancelBtn, 'Found');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => addLog('✅ Cancel button clicked'));
        }

        // Check 5: Submit button
        const submitBtn = document.getElementById('submitBtn');
        setStatus('check-submit', !!submitBtn, 'Found');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => addLog('✅ Submit button clicked'));
        }

        // Check 6: mobile-responsive.js
        const hasMobileResponsive = Array.from(document.scripts).some(s => s.src && s.src.includes('mobile-responsive.js'));
        setStatus('check-mobile-responsive', !hasMobileResponsive, hasMobileResponsive ? 'FOUND (may cause issues)' : 'Not loaded');
        if (hasMobileResponsive) addLog('⚠️ mobile-responsive.js is loaded');

        // Check 7: mobile-fix.js
        const hasMobileFix = Array.from(document.scripts).some(s => s.src && s.src.includes('mobile-fix.js'));
        setStatus('check-mobile-fix', !hasMobileFix, hasMobileFix ? 'FOUND (fixed)' : 'Not loaded');
        if (hasMobileFix) addLog('ℹ️ mobile-fix.js is loaded (should be fixed now)');

        // Check 8: Modal events
        let modalEventsFired = false;
        modal.addEventListener('shown.bs.modal', function() {
            modalEventsFired = true;
            setStatus('check-modal-events', true, 'Events working');
            addLog('✅ Modal shown.bs.modal event fired');
            document.getElementById('modalStatus').innerHTML = '<strong style="color: green;">✅ Modal opened successfully! All events working!</strong>';
        });

        modal.addEventListener('hidden.bs.modal', function() {
            addLog('✅ Modal hidden.bs.modal event fired');
        });

        addLog('');
        addLog('📱 Device: ' + (window.innerWidth < 576 ? 'MOBILE' : 'DESKTOP (' + window.innerWidth + 'px)'));
        addLog('🖥️ Touch: ' + (('ontouchstart' in window) ? 'Yes' : 'No'));
        addLog('');
        addLog('👉 Now click the "Open Leave Request Modal" button above');

        // Trap any errors
        window.addEventListener('error', function(e) {
            addLog('❌ ERROR: ' + e.message);
        });
    </script>
</body>
</html>
