<!DOCTYPE html>
<html>
<head>
    <title>Employee Loading Test</title>
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Exit Management - Employee Loading Test</h1>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5>Test: Load Employees via AJAX</h5>
        </div>
        <div class="card-body">
            <button class="btn btn-primary" id="testLoadBtn">Click to Load Employees</button>
            <div id="results" class="mt-3"></div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5>Result Details</h5>
        </div>
        <div class="card-body">
            <pre id="responseOutput" style="background: #f5f5f5; padding: 10px; border-radius: 5px;"></pre>
        </div>
    </div>
</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('#testLoadBtn').click(function() {
        $('#responseOutput').text('Loading...');
        
        $.post('exit_management.php', {
            ajax_action: 'get_eligible_employees'
        }, function(response) {
            console.log('Response:', response);
            
            if (response && Array.isArray(response)) {
                let html = `<div class="alert alert-success">✓ Loaded ${response.length} employees</div>`;
                html += '<table class="table table-sm"><thead><tr><th>ID</th><th>Full Name</th><th>Username</th><th>Email</th></tr></thead><tbody>';
                
                response.slice(0, 10).forEach(emp => {
                    html += `<tr><td>${emp.id}</td><td>${emp.full_name}</td><td>${emp.username}</td><td>${emp.email}</td></tr>`;
                });
                
                html += '</tbody></table>';
                html += response.length > 10 ? `<p>... and ${response.length - 10} more employees</p>` : '';
                
                $('#results').html(html);
                $('#responseOutput').text(JSON.stringify(response.slice(0, 3), null, 2));
            } else {
                $('#results').html('<div class="alert alert-danger">✗ Invalid response format</div>');
                $('#responseOutput').text(JSON.stringify(response, null, 2));
            }
        }, 'json').fail(function(err) {
            $('#results').html(`<div class="alert alert-danger">✗ AJAX Error: ${err.status} ${err.statusText}</div>`);
            $('#responseOutput').text(`Status: ${err.status}\nStatus Text: ${err.statusText}\nResponse: ${err.responseText}`);
            console.error('AJAX Error:', err);
        });
    });
    
    // Auto-test on load
    $('#testLoadBtn').trigger('click');
});
</script>
</body>
</html>
