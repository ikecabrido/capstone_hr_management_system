<?php
/**
 * PDF Report Generator for Workforce Analytics
 * Creates print-friendly HTML that can be printed as PDF using browser
 * No external dependencies required!
 */

header('Content-Type: text/html; charset=utf-8');
error_reporting(0);

$host = 'localhost';
$db = 'hr_management';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $type = $_GET['type'] ?? 'dashboard';
    
    // Fetch analytics data
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN employment_status='Active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN employment_status!='Active' THEN 1 ELSE 0 END) as inactive,
                ROUND(AVG(YEAR(CURDATE()) - YEAR(date_hired)), 1) as avg_tenure
            FROM employees";
    $result = $conn->query($sql);
    $metrics = $result->fetch_assoc();
    
    $total = $metrics['total'];
    $attritionRate = $total > 0 ? round(($metrics['inactive'] / $total) * 100, 2) : 0;
    
    // Get department breakdown
    $deptSql = "SELECT department, COUNT(*) as count FROM employees GROUP BY department";
    $deptResult = $conn->query($deptSql);
    $departments = [];
    while ($row = $deptResult->fetch_assoc()) {
        $departments[] = $row;
    }
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Workforce Analytics Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
        }
        
        .report-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            background: white;
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        
        .report-header h1 {
            font-size: 28px;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .report-header p {
            color: #666;
            font-size: 14px;
        }
        
        .report-section {
            margin-bottom: 35px;
        }
        
        .report-section h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .metric-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            text-align: center;
        }
        
        .metric-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        
        .metric-value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .metric-change {
            font-size: 13px;
            color: #999;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #999;
            font-size: 12px;
            margin-top: 40px;
        }
        
        .print-note {
            background: #fff3cd;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            color: #856404;
            font-size: 13px;
        }
        
        @media print {
            body {
                background: white;
            }
            .print-note {
                display: none;
            }
            .report-container {
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1>📊 WORKFORCE ANALYTICS REPORT</h1>
            <p>Generated: <?php echo date('F j, Y H:i:s'); ?></p>
        </div>
        
        <div class="print-note">
            <strong>💡 To save as PDF:</strong> Use your browser's Print function (Ctrl+P or Cmd+P) and select "Save as PDF" as the printer.
        </div>
        
        <div class="report-section">
            <h2>Executive Summary</h2>
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Employees</div>
                    <div class="metric-value"><?php echo $metrics['total']; ?></div>
                    <div class="metric-change">Current headcount</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-label">Active Employees</div>
                    <div class="metric-value"><?php echo $metrics['active']; ?></div>
                    <div class="metric-change">Currently working</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-label">Inactive Employees</div>
                    <div class="metric-value"><?php echo $metrics['inactive']; ?></div>
                    <div class="metric-change">Separated</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-label">Attrition Rate</div>
                    <div class="metric-value"><?php echo $attritionRate; ?>%</div>
                    <div class="metric-change">Overall turnover</div>
                </div>
            </div>
        </div>
        
        <div class="report-section">
            <h2>Key Metrics</h2>
            <table>
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Average Tenure (Years)</td>
                        <td><?php echo $metrics['avg_tenure']; ?></td>
                        <td>✅ Healthy</td>
                    </tr>
                    <tr>
                        <td>Attrition Rate (%)</td>
                        <td><?php echo $attritionRate; ?></td>
                        <td><?php echo $attritionRate > 15 ? '⚠️ High' : ($attritionRate > 10 ? '⚡ Caution' : '✅ Good'); ?></td>
                    </tr>
                    <tr>
                        <td>Active to Inactive Ratio</td>
                        <td><?php echo $metrics['total'] > 0 ? round(($metrics['active'] / $metrics['total']) * 100, 1) : 0; ?>% Active</td>
                        <td>✅ Monitored</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($departments)): ?>
        <div class="report-section">
            <h2>Department Breakdown</h2>
            <table>
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Employee Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $dept): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dept['department']); ?></td>
                        <td><?php echo $dept['count']; ?></td>
                        <td><?php echo round(($dept['count'] / $total) * 100, 1); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="report-section">
            <h2>Report Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Report Type</td>
                        <td><?php echo ucfirst($type); ?></td>
                    </tr>
                    <tr>
                        <td>Generated Date</td>
                        <td><?php echo date('F j, Y'); ?></td>
                    </tr>
                    <tr>
                        <td>Generated Time</td>
                        <td><?php echo date('H:i:s'); ?></td>
                    </tr>
                    <tr>
                        <td>System</td>
                        <td>Workforce Analytics</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <p>This is an automatically generated report from the Workforce Analytics System.</p>
            <p>© 2026 HR Management System. All rights reserved.</p>
        </div>
    </div>
    
    <script>
        // Auto-open print dialog when page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
<?php
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    die("Error: " . $e->getMessage());
}
?>
