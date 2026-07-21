<?php
/**
 * Test to verify button click events work without JS interference
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Button Click Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .test-container { max-width: 500px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .test-log { background: #1e1e1e; color: #00ff00; padding: 15px; border-radius: 4px; margin-top: 20px; min-height: 200px; font-family: monospace; overflow-y: auto; }
        .log-entry { margin: 5px 0; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="test-container">
        <h2>🧪 Raw Button Click Test</h2>
        <p>Testing button clicks WITHOUT mobile-responsive.js interference</p>

        <!-- Test buttons WITHOUT any data attributes -->
        <button type="button" class="btn btn-success mb-2" id="test1">Test 1: Normal Button</button>

        <!-- Test buttons WITH data-bs-toggle -->
        <button type="button" class="btn btn-info mb-2" data-bs-toggle="modal" data-bs-target="#testModal" id="test2">Test 2: Modal Trigger</button>

        <!-- Test button WITH data-bs-dismiss -->
        <button type="button" class="btn btn-warning mb-2" data-bs-dismiss="modal" id="test3">Test 3: Dismiss Button</button>

        <!-- Test button WITH btn-close class -->
        <button type="button" class="btn-close btn-lg mb-2" id="test4" title="Close"></button>

        <h3 style="margin-top: 30px;">📋 Event Log:</h3>
        <div class="test-log" id="testLog"></div>
    </div>

    <!-- Simple test modal -->
    <div class="modal fade" id="testModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">This is a test modal. Click buttons to test.</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Action</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- IMPORTANT: NO mobile-responsive.js loaded here -->
    
    <script>
        const testLog = document.getElementById('testLog');

        function logEvent(message) {
            const entry = document.createElement('div');
            entry.className = 'log-entry';
            const time = new Date().toLocaleTimeString();
            entry.innerHTML = `<span style="color: #666;">[${time}]</span> ${message}`;
            testLog.appendChild(entry);
            testLog.scrollTop = testLog.scrollHeight;
        }

        // Test 1: Normal button
        document.getElementById('test1').addEventListener('click', function(e) {
            logEvent('✅ <span style="color: #ffff00;">Test 1 clicked</span> - Normal button works');
        });

        // Test 2: Modal trigger
        document.getElementById('test2').addEventListener('click', function(e) {
            logEvent('✅ <span style="color: #00ff00;">Test 2 clicked</span> - Modal trigger fires');
        });

        // Test 3: Dismiss button
        document.getElementById('test3').addEventListener('click', function(e) {
            logEvent('✅ <span style="color: #ff6b6b;">Test 3 clicked</span> - Dismiss button fires');
        });

        // Test 4: Close button
        document.getElementById('test4').addEventListener('click', function(e) {
            logEvent('✅ <span style="color: #87ceeb;">Test 4 clicked</span> - Close button fires');
        });

        // Monitor modal events
        document.getElementById('testModal').addEventListener('shown.bs.modal', function() {
            logEvent('📋 Modal opened successfully');
        });

        document.getElementById('testModal').addEventListener('hidden.bs.modal', function() {
            logEvent('📋 Modal closed');
        });

        logEvent('🚀 Test page loaded - click buttons above');
        logEvent('✅ Bootstrap 5 loaded');
        logEvent('📝 No mobile-responsive.js interference on this page');
    </script>
</body>
</html>
