<?php
session_start();
require_once "../auth/auth_check.php";
require_once "../auth/database.php";
require_once "core/BaseModel.php";
require_once "services/ClinicReportService.php";
require_once "models/Patient.php";

$database = Database::getInstance();
$db = $database->getConnection();

if ($db === null) {
    $_SESSION['error'] = "Database connection failed.";
    header('Location: ../index.php');
    exit;
}

$clinic_report = new ClinicReportService($db);

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$message = '';
$error = '';

if ($action === 'download' && isset($_GET['report_id'])) {
    $report_id = $_GET['report_id'];
    $report = $clinic_report->getReport($report_id);
    
    if ($report && $report['file_format'] === 'Excel') {
        header("Location: generate_report_excel.php?report_id=$report_id");
    } elseif ($report && $report['file_format'] === 'HTML') {
        header("Location: generate_report_html.php?report_id=$report_id");
    } else {
        header("Location: generate_report_pdf.php?report_id=$report_id");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_report') {
    try {
        $report_type = $_POST['report_type'];
        $start_date = $_POST['start_date'] ?? date('Y-m-d');
        $end_date = $_POST['end_date'] ?? date('Y-m-d');
        $format = $_POST['format'] ?? 'HTML';
        $report_id = 'REP' . strtoupper(substr($report_type, 0, 3)) . date('YmdHis');
        
        $report_data = [];
        switch ($report_type) {
            case 'daily': $report_data = $clinic_report->generateDailyReport($start_date); break;
            case 'weekly': $report_data = $clinic_report->generateWeeklyReport($start_date, $end_date); break;
            case 'monthly': $report_data = $clinic_report->generateMonthlyReport($start_date); break;
            case 'custom': $report_data = $clinic_report->generateCustomReport($start_date, $end_date); break;
            case 'annual': $report_data = $clinic_report->generateAnnualReport($start_date); break;
        }
        
        if (empty($report_data)) throw new Exception("No data found for the selected criteria.");
        
        $report_info = [
            'report_id' => $report_id,
            'report_type' => ucfirst($report_type),
            'report_date' => date('Y-m-d'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'report_data' => json_encode($report_data),
            'generated_by' => $_SESSION['user']['name'] ?? 'System',
            'status' => 'Generated',
            'file_format' => $format
        ];
        
        if ($clinic_report->saveReport($report_info)) {
            $message = "Report generated successfully!";
        } else {
            $error = "Failed to save report.";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

if ($action === 'delete_report') {
    try {
        if ($clinic_report->deleteReport($_POST['report_id'])) $message = "Report deleted successfully!";
        else $error = "Failed to delete report.";
    } catch (Exception $e) { $error = "Error: " . $e->getMessage(); }
}

$reports = $clinic_report->getSavedReports(100);
$visit_trends = $clinic_report->getVisitTrends(7);
$medicine_stats = $clinic_report->getMedicineUsageStats(5);

$theme = $_SESSION['user']['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Reports - Clinic System</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <link href="../assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/dist/css/adminlte.min.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <style>
        .content-wrapper { background-color: #f4f6f9; overflow-x: hidden; }
        .card { border: 1px solid #dee2e6; border-radius: 0; box-shadow: none; margin-bottom: 1rem; opacity: 0; }
        .card-header { background-color: #fff; border-bottom: 1px solid #dee2e6; padding: 0.75rem 1.25rem; }
        .card-title { font-size: 1.1rem; color: #333; }
        .btn-primary { background-color: #007bff; border-color: #007bff; border-radius: 4px; padding: 6px 12px; transition: all 0.3s ease; }
        .btn-primary:active { transform: scale(0.95); }
        .table thead th { border-top: 0; border-bottom: 2px solid #dee2e6; color: #333; font-weight: 600; }
        .chart-container { height: 300px; padding: 10px; }
        .btn-group .btn { margin-right: 5px; border-radius: 4px !important; transition: all 0.3s ease; }
        .btn-group .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .btn-group .btn:active { transform: translateY(0) scale(0.95); }

        /* Animations */
        @keyframes slideInRight {
            from { transform: translateX(50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Applying slide show / wave effect */
        .animate-slide-up { animation: slideUp 0.7s ease-out forwards; }
        .animate-delay-1 { animation-delay: 0.15s; }
        .animate-delay-2 { animation-delay: 0.3s; }
        .animate-delay-3 { animation-delay: 0.45s; }

        .page-entrance { animation: slideInRight 0.6s ease-out forwards; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="clinic.php" class="nav-link">Home</a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="clinic.php" class="brand-link">
                <img src="../assets/pics/bcpLogo.png" alt="Logo" class="brand-image elevation-3" style="opacity: 0.9">
                <span class="brand-text font-weight-light">BCP Bulacan </span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                    <div class="info">
                        <a href="#" class="d-block">Admin <?= htmlspecialchars($_SESSION['user']['name']) ?></a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="clinic.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Employee_Patient.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Employee</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="MedicalRecordsHistory.php" class="nav-link">
                                <i class="nav-icon fas fa-file-medical"></i>
                                <p>Medical Records History</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="MedicinesInventory.php" class="nav-link">
                                <i class="nav-icon fas fa-pills"></i>
                                <p>Medicines Inventory</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Clinic_Reports.php" class="nav-link active">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Clinic Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Emergency.php" class="nav-link">
                                <i class="nav-icon fas fa-ambulance"></i>
                                <p>Emergency Cases</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../logout.php" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper page-entrance">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-8">
                            <h1 class="m-0">Clinic Reports</h1>
                            <p class="text-muted">Auto-generate comprehensive reports including daily/weekly/monthly statistics and health trends</p>
                        </div>
                        <div class="col-sm-4 text-right">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#generateReportModal">
                                <i class="fas fa-plus mr-1"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= $message ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= $error ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card animate-slide-up animate-delay-1">
                                <div class="card-header"><h3 class="card-title">Visit Trends</h3></div>
                                <div class="card-body p-0"><div class="chart-container"><canvas id="visitChart"></canvas></div></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card animate-slide-up animate-delay-2">
                                <div class="card-header"><h3 class="card-title">Medicine Usage</h3></div>
                                <div class="card-body p-0"><div class="chart-container"><canvas id="medicineChart"></canvas></div></div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3 animate-slide-up animate-delay-3">
                        <div class="card-header border-0">
                            <h3 class="card-title">Generated Reports</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" id="tableSearch" class="form-control" placeholder="Search">
                                    <div class="input-group-append"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Report ID</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Period</th>
                                        <th>Generated By</th>
                                        <th>Format</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($report['report_id']) ?></td>
                                        <td><span class="badge badge-info"><?= htmlspecialchars($report['report_type']) ?></span></td>
                                        <td><?= date('M d, Y', strtotime($report['report_date'])) ?></td>
                                        <td><?= date('M d', strtotime($report['start_date'])) ?> - <?= date('M d, Y', strtotime($report['end_date'])) ?></td>
                                        <td><?= htmlspecialchars($report['generated_by']) ?></td>
                                        <td><span class="badge badge-secondary"><?= htmlspecialchars($report['file_format']) ?></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info" onclick="viewReport('<?= $report['report_id'] ?>')"><i class="fas fa-eye"></i></button>
                                                <button class="btn btn-sm btn-success" onclick="downloadReport('<?= $report['report_id'] ?>')"><i class="fas fa-download"></i></button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteReport('<?= $report['report_id'] ?>')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Generate Modal -->
    <div class="modal fade" id="generateReportModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Generate Clinic Report</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="Clinic_Reports.php">
                    <input type="hidden" name="action" value="generate_report">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Report Type</label>
                            <select name="report_type" id="report_type_select" class="form-control" required>
                                <option value="daily">Daily Report</option>
                                <option value="weekly">Weekly Report</option>
                                <option value="monthly">Monthly Report</option>
                                <option value="custom">Custom Range</option>
                                <option value="annual">Annual Report</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" id="start_date_input" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6" id="end_date_group" style="display: none;">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" id="end_date_input" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Format</label>
                            <select name="format" class="form-control">
                                <option value="HTML">HTML</option>
                                <option value="PDF">PDF</option>
                                <option value="Excel">Excel</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewReportModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title">Report Details</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body" id="reportDetails">Loading...</div>
            </div>
        </div>
    </div>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/dist/js/adminlte.js"></script>
    <script>
        $(document).ready(function() {
            const chartOptions = {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top', align: 'center' } },
                scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } }, x: { grid: { display: false } } }
            };

            new Chart(document.getElementById('visitChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_map(function($t) { return date('D', strtotime($t['date'])); }, $visit_trends)) ?>,
                    datasets: [{
                        label: 'Patient Visits',
                        data: <?= json_encode(array_column($visit_trends, 'visits')) ?>,
                        borderColor: '#20c997', backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        borderWidth: 3, pointBackgroundColor: '#fff', pointBorderColor: '#20c997',
                        pointRadius: 5, fill: false, tension: 0.4
                    }]
                },
                options: chartOptions
            });

            new Chart(document.getElementById('medicineChart'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($medicine_stats, 'medicine_name')) ?>,
                    datasets: [{
                        label: 'Units Used',
                        data: <?= json_encode(array_column($medicine_stats, 'total_used')) ?>,
                        backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)'],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)'],
                        borderWidth: 1
                    }]
                },
                options: chartOptions
            });

            $("#tableSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Handle Report Type Selection and End Date visibility
            $('#report_type_select').change(function() {
                const type = $(this).val();
                if (type === 'daily') {
                    $('#end_date_group').hide();
                    $('#end_date_input').val($('#start_date_input').val());
                } else {
                    $('#end_date_group').show();
                    // Set default end date based on type
                    const startDate = new Date($('#start_date_input').val());
                    let endDate = new Date(startDate);
                    if (type === 'weekly') endDate.setDate(startDate.getDate() + 7);
                    else if (type === 'monthly') endDate.setMonth(startDate.getMonth() + 1);
                    else if (type === 'annual') endDate.setFullYear(startDate.getFullYear() + 1);
                    
                    $('#end_date_input').val(endDate.toISOString().split('T')[0]);
                }
            });

            $('#start_date_input').change(function() {
                if ($('#report_type_select').val() === 'daily') {
                    $('#end_date_input').val($(this).val());
                }
            });

            // Button click animation feedback
            $('.btn').on('mousedown', function() {
                $(this).css('transform', 'scale(0.95)');
            }).on('mouseup mouseleave', function() {
                $(this).css('transform', '');
            });
        });

        function viewReport(id) { $('#viewReportModal').modal('show'); $('#reportDetails').load('services/GetReportData.php?report_id=' + id); }
        function downloadReport(id) { window.location.href = 'Clinic_Reports.php?action=download&report_id=' + id; }
        function deleteReport(id) { if(confirm('Are you sure you want to delete this report?')) { /* Logic */ } }
    </script>
</body>
</html>
