<?php
/**
 * Clean Test - NO mobile-fix.js or mobile-responsive.js
 * This proves Bootstrap 5 works without interference
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clean Bootstrap Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 20px; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .success { background: #d4edda; border: 1px solid #28a745; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Clean Bootstrap Test</h1>
        
        <div class="success">
            <strong>⚠️ This page has NO mobile-fix.js or mobile-responsive.js</strong><br>
            This is a pure Bootstrap 5 test to verify buttons work without interference.
        </div>

        <p>Click the button below to test the modal. All buttons should respond immediately.</p>

        <button type="button" class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#cleanModal">
            📋 Open Modal - Pure Bootstrap
        </button>

        <hr>

        <h5 style="margin-top: 30px;">📊 What This Tests:</h5>
        <ul>
            <li>✅ Modal opens on button click</li>
            <li>✅ X close button works</li>
            <li>✅ Dismiss button works</li>
            <li>✅ No JavaScript interference</li>
            <li>✅ Touch events work on mobile</li>
        </ul>

        <div style="margin-top: 25px; padding: 15px; background: #f0f0f0; border-radius: 6px;">
            <h6>If this modal WORKS:</h6>
            <p style="margin-bottom: 0;">Then the problem is in <strong>mobile-fix.js</strong> or <strong>mobile-responsive.js</strong><br>
            We need to disable or fix those files.</p>
        </div>
    </div>

    <!-- Pure Bootstrap Modal -->
    <div class="modal fade" id="cleanModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clean Bootstrap Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="color: green; font-weight: bold;">✅ If you see this, the modal opened successfully!</p>
                    <p>Try clicking the X button (top right), or the Close button below.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Action</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- NO OTHER SCRIPTS LOADED -->
    
    <script>
        console.log('✅ Page loaded with ONLY Bootstrap');
        console.log('No mobile-fix.js');
        console.log('No mobile-responsive.js');
        console.log('Testing pure Bootstrap 5 modal...');

        const modal = document.getElementById('cleanModal');
        modal.addEventListener('shown.bs.modal', function() {
            console.log('✅ Modal opened successfully');
        });

        modal.addEventListener('hidden.bs.modal', function() {
            console.log('✅ Modal closed');
        });
    </script>
</body>
</html>
