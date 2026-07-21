<?php
/**
 * JavaScript Conflict Diagnostic - Identifies which script blocks modal buttons
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JS Conflict Diagnostic</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; padding: 20px; font-family: monospace; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { color: #333; }
        .test-section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #0066cc; border-radius: 4px; }
        .code { background: #1e1e1e; color: #00ff00; padding: 10px; border-radius: 4px; margin: 10px 0; overflow-x: auto; font-size: 0.85rem; }
        .error { color: #ff6b6b; }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        button { margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔍 JavaScript Conflict Diagnostic</h2>
        
        <div class="test-section">
            <h4>Step 1: Check Loaded Scripts</h4>
            <div id="scriptsLoaded"></div>
        </div>

        <div class="test-section">
            <h4>Step 2: Event Listener Analysis</h4>
            <div id="eventAnalysis"></div>
        </div>

        <div class="test-section">
            <h4>Step 3: Test Modal Button</h4>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testModal">
                Open Test Modal
            </button>
        </div>

        <div class="test-section">
            <h4>Step 4: Console Log</h4>
            <div class="code" id="consoleLog"></div>
        </div>
    </div>

    <!-- Test Modal -->
    <div class="modal fade" id="testModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>✅ Modal opened successfully!</p>
                    <p id="eventInfo"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Action</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const logs = [];

        function logToDiagnostic(msg) {
            logs.push(msg);
            console.log(msg);
            updateConsoleDisplay();
        }

        function updateConsoleDisplay() {
            const consoleDiv = document.getElementById('consoleLog');
            consoleDiv.innerHTML = logs.map(log => `<div>${escapeHtml(log)}</div>`).join('\n');
            consoleDiv.scrollTop = consoleDiv.scrollHeight;
        }

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Check loaded scripts
        const scripts = Array.from(document.scripts).map(s => s.src || '[inline]');
        const scriptsDiv = document.getElementById('scriptsLoaded');
        
        scriptsDiv.innerHTML = '<h5>Loaded Scripts:</h5>';
        scripts.forEach(src => {
            const div = document.createElement('div');
            div.className = 'code';
            const name = src.split('/').pop() || src;
            
            let color = '#00ff00';
            if (src.includes('mobile-responsive')) {
                color = '#ffaa00';
                div.innerHTML = `<span style="color: ${color};">⚠️ ${name}</span>`;
            } else if (src.includes('mobile-fix')) {
                color = '#ff6b6b';
                div.innerHTML = `<span style="color: ${color};">❌ ${name}</span>`;
            } else if (src.includes('bootstrap')) {
                color = '#00ff00';
                div.innerHTML = `<span style="color: ${color};">✅ ${name}</span>`;
            } else {
                div.innerHTML = `<span style="color: ${color};">${name}</span>`;
            }
            scriptsDiv.appendChild(div);
        });

        // Check event listeners on button
        const triggerBtn = document.querySelector('[data-bs-target="#testModal"]');
        const eventDiv = document.getElementById('eventAnalysis');
        
        eventDiv.innerHTML = `
            <p><strong>Button Element:</strong></p>
            <div class="code">
                &lt;button data-bs-toggle="modal" data-bs-target="#testModal"&gt;
            </div>
            <p><strong>Button Attributes:</strong></p>
        `;

        if (triggerBtn) {
            const attrs = triggerBtn.attributes;
            for (let i = 0; i < attrs.length; i++) {
                const div = document.createElement('div');
                div.className = 'code';
                div.innerHTML = `${attrs[i].name}="${attrs[i].value}"`;
                eventDiv.appendChild(div);
            }
            
            eventDiv.innerHTML += '<p style="margin-top: 15px;"><strong>Checking for event handler conflicts:</strong></p>';
            
            // Test click
            let clickFired = false;
            triggerBtn.addEventListener('click', function(e) {
                clickFired = true;
                logToDiagnostic('✅ Click event fired on trigger button');
            });
        }

        // Monitor touchend event globally
        const originalAddEventListener = Element.prototype.addEventListener;
        let touchendListeners = 0;

        Element.prototype.addEventListener = function(type, listener, options) {
            if (type === 'touchend' && listener.toString().includes('preventDefault')) {
                touchendListeners++;
                logToDiagnostic(`⚠️ Found touchend + preventDefault listener: ${listener.toString().substring(0, 100)}...`);
            }
            return originalAddEventListener.call(this, type, listener, options);
        };

        // Bootstrap modal events
        document.getElementById('testModal').addEventListener('shown.bs.modal', function() {
            logToDiagnostic('✅ Modal shown event fired');
            document.getElementById('eventInfo').innerHTML = '<strong style="color: green;">✅ All events working correctly!</strong>';
        });

        document.getElementById('testModal').addEventListener('hidden.bs.modal', function() {
            logToDiagnostic('✅ Modal hidden event fired');
        });

        // Initial diagnostic
        logToDiagnostic('=== JavaScript Conflict Diagnostic Started ===');
        logToDiagnostic(`📱 Device Width: ${window.innerWidth}px`);
        logToDiagnostic(`🖥️ Touch Device: ${('ontouchstart' in window) ? 'Yes' : 'No'}`);
        logToDiagnostic(`✅ Bootstrap Module: ${typeof bootstrap !== 'undefined' ? 'Available' : 'Not found'}`);
        logToDiagnostic('');
        logToDiagnostic('Instructions:');
        logToDiagnostic('1. Click the "Open Test Modal" button above');
        logToDiagnostic('2. Watch this log for any errors or preventDefault calls');
        logToDiagnostic('3. Try clicking the modal buttons (X, Close, Action)');
        logToDiagnostic('');
    </script>
</body>
</html>
