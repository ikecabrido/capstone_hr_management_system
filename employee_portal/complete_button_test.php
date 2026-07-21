<?php
/**
 * Complete Button Functionality Test
 * Tests all modal buttons after JavaScript fixes
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Button Functionality Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana; }
        .test-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 25px; font-weight: 700; }
        .test-button { margin: 10px 0; width: 100%; }
        .results-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 25px; border: 2px solid #667eea; }
        .result-item { padding: 10px 0; border-bottom: 1px solid #ddd; }
        .result-item:last-child { border-bottom: none; }
        .pass { color: #28a745; font-weight: bold; }
        .fail { color: #dc3545; font-weight: bold; }
        .info { color: #0066cc; font-size: 0.9rem; }
        .device-info { background: #e7f3ff; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #0066cc; }
        .button-group { margin: 20px 0; }
        .button-group h5 { color: #333; margin-bottom: 12px; font-weight: 600; }
        .btn-test { font-size: 0.9rem; padding: 10px 20px; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>✅ Complete Button Test</h1>
        
        <div class="device-info">
            <strong>📱 Device Info:</strong><br>
            Width: <span id="width"></span>px | Height: <span id="height"></span>px<br>
            <small id="deviceType" style="color: #666;"></small>
        </div>

        <div class="button-group">
            <h5>🧪 Test Buttons</h5>
            <button type="button" class="btn btn-primary test-button btn-test" data-bs-toggle="modal" data-bs-target="#testModal">
                📋 Open Modal (Modal Trigger)
            </button>
            <small class="info d-block mt-2">↑ This button has data-bs-toggle="modal" - should open the modal below</small>
        </div>

        <div class="results-box">
            <h5 style="margin-bottom: 15px;">📊 Test Results:</h5>
            <div id="testResults"></div>
        </div>
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
                #testModal .modal-footer { padding: 0.6rem !important; gap: 0.4rem; flex-wrap: wrap; }
                #testModal .btn { font-size: 0.8rem !important; padding: 0.35rem 0.6rem !important; min-height: 32px !important; display: flex; align-items: center; justify-content: center; }
            }
        </style>
        
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Modal Window</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" title="Close Modal"></button>
                </div>
                
                <div class="modal-body">
                    <p>🎯 Modal opened successfully! Now test the buttons below:</p>
                    <div class="alert alert-info mb-3">
                        <strong>Instructions:</strong>
                        <ul style="margin-bottom: 0; margin-top: 8px;">
                            <li>Click the <strong>X button</strong> (top right) to close</li>
                            <li>Click the <strong>Cancel button</strong> to close</li>
                            <li>Click the <strong>Confirm</strong> button to test submit</li>
                        </ul>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        ❌ Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="confirmBtn">
                        ✅ Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- IMPORTANT: NO mobile-responsive.js included here -->
    <!-- We're testing without the problematic script first -->
    
    <script>
        const results = [];

        function addResult(name, passed, details = '') {
            results.push({ name, passed, details });
            const resultsDiv = document.getElementById('testResults');
            const div = document.createElement('div');
            div.className = 'result-item';
            let html = `<span class="${passed ? 'pass' : 'fail'}">${passed ? '✅' : '❌'} ${name}</span>`;
            if (details) html += `<br><span class="info">${details}</span>`;
            div.innerHTML = html;
            resultsDiv.appendChild(div);
        }

        // Update device info
        function updateDeviceInfo() {
            document.getElementById('width').textContent = window.innerWidth;
            document.getElementById('height').textContent = window.innerHeight;
            
            const width = window.innerWidth;
            let type = '';
            if (width < 576) type = '📱 Mobile (<576px)';
            else if (width < 768) type = '📱 Mobile Large (576-768px)';
            else if (width < 1024) type = '📱 Tablet (768-1024px)';
            else type = '🖥️ Desktop (>1024px)';
            
            document.getElementById('deviceType').textContent = type;
        }

        updateDeviceInfo();
        window.addEventListener('resize', updateDeviceInfo);

        // Test 1: Bootstrap loaded
        const hasBootstrap = typeof bootstrap !== 'undefined' && bootstrap.Modal;
        addResult('Bootstrap 5 Module', hasBootstrap, hasBootstrap ? 'bootstrap.Modal available' : 'Not found');

        // Test 2: Modal element
        const modal = document.getElementById('testModal');
        addResult('Modal Element', !!modal, modal ? 'id="testModal" exists' : 'Not found');

        // Test 3: Close button
        const closeBtn = document.querySelector('#testModal .btn-close');
        addResult('Close Button (X)', !!closeBtn, closeBtn ? 'Has btn-close class' : 'Not found');

        // Test 4: Cancel button
        const cancelBtn = document.querySelector('[data-bs-dismiss="modal"]');
        addResult('Cancel Button', !!cancelBtn, cancelBtn ? 'Has data-bs-dismiss="modal"' : 'Not found');

        // Test 5: Confirm button
        const confirmBtn = document.getElementById('confirmBtn');
        addResult('Confirm Button', !!confirmBtn, confirmBtn ? 'id="confirmBtn" exists' : 'Not found');

        // Test 6: Modal trigger
        const triggerBtn = document.querySelector('[data-bs-target="#testModal"]');
        addResult('Modal Trigger Button', !!triggerBtn, triggerBtn ? 'Has data-bs-toggle="modal"' : 'Not found');

        // Test 7: Button click handlers
        let closeClicked = false, cancelClicked = false, confirmClicked = false;

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                closeClicked = true;
                console.log('✅ Close button clicked');
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                cancelClicked = true;
                console.log('✅ Cancel button clicked');
            });
        }

        if (confirmBtn) {
            confirmBtn.addEventListener('click', function(e) {
                confirmClicked = true;
                console.log('✅ Confirm button clicked');
            });
        }

        // Test 8: Modal events
        let modalShown = false, modalHidden = false;
        
        modal.addEventListener('shown.bs.modal', function() {
            modalShown = true;
            addResult('Modal Opens', true, 'shown.bs.modal event fired');
            console.log('✅ Modal opened');
        });

        modal.addEventListener('hidden.bs.modal', function() {
            modalHidden = true;
            console.log('✅ Modal closed');
        });

        // Test 9: Verify no mobile-responsive.js interference
        const scriptTags = Array.from(document.scripts).map(s => s.src);
        const hasMobileResponsive = scriptTags.some(src => src.includes('mobile-responsive.js'));
        addResult('mobile-responsive.js', !hasMobileResponsive, hasMobileResponsive ? '⚠️ Script is loaded - may cause issues' : '✅ Not loaded (clean test)');

        // Final message
        setTimeout(() => {
            const resultsDiv = document.getElementById('testResults');
            const allPassed = results.every(r => r.passed);
            const summary = document.createElement('div');
            summary.className = 'result-item' + (allPassed ? ' pass' : ' fail');
            summary.innerHTML = allPassed ? 
                '<strong>🎉 All checks passed! Buttons should work correctly.</strong>' :
                '<strong>⚠️ Some checks failed. Review the results above.</strong>';
            resultsDiv.appendChild(summary);
        }, 100);

        console.log('=== Button Test Suite Loaded ===');
        console.log('Click the "Open Modal" button above to test');
        console.log('Then test each button in the modal');
    </script>
</body>
</html>
