<?php
session_start();
require_once "../auth/auth_check.php";
$theme = $_SESSION['user']['theme'] ?? 'light';

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Workforce Analytics and Reporting Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link
    rel="stylesheet"
    href="../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="custom.css" />
  <link rel="stylesheet" href="../layout/toast.css" />
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- WFA Dashboard Styles -->
  <style>
    .wfa-container {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
    }

    .wfa-metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 15px;
      margin-bottom: 30px;
    }

    .wfa-metric-card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      border-left: 4px solid #007bff;
    }

    .wfa-metric-card.danger { border-left-color: #dc3545; }
    .wfa-metric-card.warning { border-left-color: #ffc107; }
    .wfa-metric-card.success { border-left-color: #28a745; }
    .wfa-metric-card.info { border-left-color: #17a2b8; }

    .wfa-metric-label {
      font-size: 0.85rem;
      color: #6c757d;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
    }

    .wfa-metric-value {
      font-size: 2rem;
      font-weight: bold;
      color: #212529;
      margin-bottom: 5px;
    }

    .wfa-metric-change {
      font-size: 0.85rem;
      color: #6c757d;
    }

    .wfa-charts-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .wfa-chart-container {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .wfa-chart-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 15px;
      color: #212529;
    }

    .wfa-table-container {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }

    .wfa-table {
      width: 100%;
      border-collapse: collapse;
    }

    .wfa-table thead {
      background-color: #f8f9fa;
      border-bottom: 2px solid #dee2e6;
    }

    .wfa-table th {
      padding: 12px;
      text-align: left;
      font-weight: 600;
      color: #212529;
      font-size: 0.9rem;
    }

    .wfa-table td {
      padding: 12px;
      border-bottom: 1px solid #dee2e6;
    }

    .wfa-table tbody tr:hover {
      background-color: #f8f9fa;
    }

    .wfa-risk-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .wfa-risk-badge.high { 
      background-color: #f8d7da;
      color: #721c24;
    }

    .wfa-risk-badge.medium { 
      background-color: #fff3cd;
      color: #856404;
    }

    .wfa-risk-badge.low { 
      background-color: #d4edda;
      color: #155724;
    }

    .wfa-loading {
      text-align: center;
      padding: 40px;
      color: #6c757d;
    }

    .wfa-error {
      background-color: #f8d7da;
      color: #721c24;
      padding: 15px;
      border-radius: 4px;
      margin-bottom: 20px;
    }
  </style>
</head>

<body
  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Preloader -->
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__wobble"
        src="../assets/pics/bcpLogo.png"
        alt="AdminLTELogo"
        height="60"
        width="60" />
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="workforce.php" class="nav-link">Home</a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
          <div class="nav-link" id="clock">--:--:--</div>
        </li>

        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>

        <li class="nav-item">
          <a
            class="nav-link"
            href="#"
            id="darkToggle"
            role="button"
            title="Toggle Dark Mode">
            <i class="fas fa-moon" id="themeIcon"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="workforce.php" class="brand-link">

        <img
          src="../assets/pics/bcpLogo.png"
          alt="AdminLTE Logo"
          class="brand-image elevation-3"
          style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan </span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="image">
          </div>
          <div class="info">
            <a href="#" onclick="openGlobalModal('Profile Settings ','../user_profile/profile_form.php')" class="d-block">
              Admin <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul
            class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item">
              <a href="#dashboard" data-toggle="tab" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#attrition" data-toggle="tab" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Attrition & Turnover</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#diversity" data-toggle="tab" class="nav-link">
                <i class="nav-icon fas fa-handshake"></i>
                <p>Diversity & Inclusion</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#performance" data-toggle="tab" class="nav-link">
                <i class="nav-icon fas fa-star"></i>
                <p>Performance</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#reports" data-toggle="tab" class="nav-link">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>Custom Reports</p>
              </a>
            </li>


            <!-- Logout -->
            <li class="nav-item">
              <a href="../logout.php" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Logout</p>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Workforce Analytics and Reporting Management System</h1>
            </div>
            <!-- /.col -->

            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">

          <!-- Tab Content -->
          <div class="tab-content" id="mainTabContent">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
              <div class="wfa-container" id="dashboardContainer">
                <div class="wfa-loading">
                  <i class="fas fa-spinner fa-spin"></i> Loading Dashboard...
                </div>
              </div>
            </div>
            <!-- /.tab-pane dashboard -->

            <!-- Attrition Tab -->
            <div class="tab-pane fade" id="attrition" role="tabpanel">
              <?php include 'public/attrition.php'; ?>
            </div>
            <!-- /.tab-pane attrition -->

            <!-- Diversity Tab -->
            <div class="tab-pane fade" id="diversity" role="tabpanel">
              <?php include 'public/diversity.php'; ?>
            </div>
            <!-- /.tab-pane diversity -->

            <!-- Performance Tab -->
            <div class="tab-pane fade" id="performance" role="tabpanel">
              <?php include 'public/performance.php'; ?>
            </div>
            <!-- /.tab-pane performance -->

            <!-- Reports Tab -->
            <div class="tab-pane fade" id="reports" role="tabpanel">
              <?php include 'public/reports.php'; ?>
            </div>
            <!-- /.tab-pane reports -->
          </div>
          <!-- /.tab-content -->
        </div>
        <!--/. container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->

  </div>
  <!-- ./wrapper -->
  <?php include "../layout/global_modal.php"; ?>
  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="../assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../assets/dist/js/adminlte.js"></script>

  <!-- PAGE PLUGINS -->
  <!-- jQuery Mapael -->
  <script src="../assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="../assets/plugins/raphael/raphael.min.js"></script>
  <script src="../assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="../assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <!-- ChartJS -->
  <script src="../assets/plugins/chart.js/Chart.min.js"></script>

  <!-- AdminLTE for demo purposes -->
  <!-- <script src="assets/dist/js/demo.js"></script> -->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!-- <script src="assets/dist/js/pages/dashboard2.js"></script> -->
  <script src="../assets/dist/js/theme.js"></script>
  <script src="../assets/dist/js/time.js"></script>
  <script src="../assets/dist/js/global_modal.js"></script>
  <script src="../assets/dist/js/profile.js"></script>
  <!-- Dashboard Analytics -->
  <script src="assets/dashboard.js"></script>

  <script>
    // WFA Dashboard Data Loader
    let wfaCharts = {
      departmentChart: null,
      genderChart: null,
      attritionChart: null,
      separationChart: null
    };

    async function loadWFADashboard() {
      try {
        const date = new Date().toISOString().split('T')[0];
        const basePath = '/capstone_hr_management_system';
        
        console.log('Loading WFA Dashboard for date:', date);
        
        // Fetch dashboard metrics
        console.log('Fetching:', `${basePath}/api/wfa/dashboard_metrics.php?date=${date}`);
        const metricsResponse = await fetch(`${basePath}/api/wfa/dashboard_metrics.php?date=${date}`);
        if (!metricsResponse.ok) throw new Error(`Metrics API error: ${metricsResponse.status}`);
        const metricsData = await metricsResponse.json();
        console.log('Metrics data:', metricsData);
        
        // Fetch at-risk employees
        const atRiskResponse = await fetch(`${basePath}/api/wfa/at_risk_employees.php?limit=5&risk_level=high`);
        if (!atRiskResponse.ok) throw new Error(`At-risk API error: ${atRiskResponse.status}`);
        const atRiskData = await atRiskResponse.json();
        console.log('At-risk data:', atRiskData);
        
        // Fetch attrition metrics
        const attritionResponse = await fetch(`${basePath}/api/wfa/attrition_metrics.php`);
        if (!attritionResponse.ok) throw new Error(`Attrition API error: ${attritionResponse.status}`);
        const attritionData = await attritionResponse.json();
        console.log('Attrition data:', attritionData);
        
        // Fetch department analytics
        const deptResponse = await fetch(`${basePath}/api/wfa/department_analytics.php?date=${date}`);
        if (!deptResponse.ok) throw new Error(`Department API error: ${deptResponse.status}`);
        const deptData = await deptResponse.json();
        console.log('Department data:', deptData);
        
        // Fetch diversity metrics
        const diversityResponse = await fetch(`${basePath}/api/wfa/diversity_metrics.php?date=${date}&category=gender`);
        if (!diversityResponse.ok) throw new Error(`Diversity API error: ${diversityResponse.status}`);
        const diversityData = await diversityResponse.json();
        console.log('Diversity data:', diversityData);

        // Build the dashboard HTML
        buildDashboard(metricsData, atRiskData, attritionData, deptData, diversityData);
      } catch (error) {
        console.error('Error loading WFA dashboard:', error);
        const container = document.getElementById('dashboardContainer');
        container.innerHTML = `
          <div class="wfa-error" style="padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
            <h4>Dashboard Loading Error</h4>
            <p>${error.message}</p>
            <p><small>Check browser console (F12) for more details</small></p>
          </div>
        `;
      }
    }

    function buildDashboard(metricsData, atRiskData, attritionData, deptData, diversityData) {
      const metrics = metricsData.data?.employee_metrics || {};
      const atRiskCount = metricsData.data?.at_risk_count || 0;
      
      let html = `
        <!-- Metric Cards -->
        <div class="wfa-metrics-grid">
          <div class="wfa-metric-card success">
            <div class="wfa-metric-label">Total Employees</div>
            <div class="wfa-metric-value">${metrics.total_employees || 0}</div>
            <div class="wfa-metric-change">Active workforce</div>
          </div>
          
          <div class="wfa-metric-card info">
            <div class="wfa-metric-label">New Hires (YTD)</div>
            <div class="wfa-metric-value">${metrics.new_hires_this_year || 0}</div>
            <div class="wfa-metric-change">This year</div>
          </div>
          
          <div class="wfa-metric-card danger">
            <div class="wfa-metric-label">At-Risk Employees</div>
            <div class="wfa-metric-value">${atRiskCount}</div>
            <div class="wfa-metric-change">High risk</div>
          </div>
          
          <div class="wfa-metric-card warning">
            <div class="wfa-metric-label">Avg Performance</div>
            <div class="wfa-metric-value">${(metrics.average_performance_score || 0).toFixed(1)}/5.0</div>
            <div class="wfa-metric-change">Rating</div>
          </div>
          
          <div class="wfa-metric-card">
            <div class="wfa-metric-label">Departments</div>
            <div class="wfa-metric-value">${metrics.total_departments || 0}</div>
            <div class="wfa-metric-change">Org units</div>
          </div>
          
          <div class="wfa-metric-card">
            <div class="wfa-metric-label">Avg Salary</div>
            <div class="wfa-metric-value">₱${(metrics.average_salary || 0).toLocaleString('en-US', {maximumFractionDigits: 0})}</div>
            <div class="wfa-metric-change">Organization</div>
          </div>
        </div>

        <!-- Charts Grid -->
        <div class="wfa-charts-grid">
          <div class="wfa-chart-container">
            <div class="wfa-chart-title">Employees by Department</div>
            <canvas id="wfaDeptChart"></canvas>
          </div>
          
          <div class="wfa-chart-container">
            <div class="wfa-chart-title">Gender Distribution</div>
            <canvas id="wfaGenderChart"></canvas>
          </div>
          
          <div class="wfa-chart-container">
            <div class="wfa-chart-title">Monthly Attrition</div>
            <canvas id="wfaAttritionChart"></canvas>
          </div>
          
          <div class="wfa-chart-container">
            <div class="wfa-chart-title">Separation Types</div>
            <canvas id="wfaSeparationChart"></canvas>
          </div>
        </div>

        <!-- At-Risk Employees Table -->
        <div class="wfa-table-container">
          <h3 style="margin-bottom: 15px;">High-Risk Employees</h3>
          <table class="wfa-table">
            <thead>
              <tr>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Position</th>
                <th>Risk Level</th>
                <th>Risk Score</th>
                <th>Performance</th>
              </tr>
            </thead>
            <tbody>
      `;
      
      if (atRiskData.data?.employees && atRiskData.data.employees.length > 0) {
        atRiskData.data.employees.forEach(emp => {
          const riskClass = emp.risk_level.toLowerCase();
          html += `
            <tr>
              <td><strong>${emp.employee_name || 'N/A'}</strong></td>
              <td>${emp.department || 'N/A'}</td>
              <td>${emp.position || 'N/A'}</td>
              <td><span class="wfa-risk-badge ${riskClass}">${emp.risk_level}</span></td>
              <td>${emp.risk_score || 0}</td>
              <td>${emp.performance_score || 0}/5.0</td>
            </tr>
          `;
        });
      } else {
        html += '<tr><td colspan="6" style="text-align: center; padding: 20px;">No high-risk employees</td></tr>';
      }
      
      html += `
            </tbody>
          </table>
        </div>

        <!-- Department Statistics Table -->
        <div class="wfa-table-container">
          <h3 style="margin-bottom: 15px;">Department Statistics</h3>
          <table class="wfa-table">
            <thead>
              <tr>
                <th>Department</th>
                <th>Employees</th>
                <th>Avg Salary</th>
                <th>Avg Performance</th>
                <th>Avg Tenure</th>
              </tr>
            </thead>
            <tbody>
      `;
      
      if (deptData.data?.departments && deptData.data.departments.length > 0) {
        deptData.data.departments.forEach(dept => {
          html += `
            <tr>
              <td><strong>${dept.department}</strong></td>
              <td>${dept.employee_count}</td>
              <td>₱${(dept.average_salary || 0).toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
              <td>${(dept.average_performance_score || 0).toFixed(1)}/5.0</td>
              <td>${(dept.average_tenure_years || 0).toFixed(1)} years</td>
            </tr>
          `;
        });
      }
      
      html += `
            </tbody>
          </table>
        </div>
      `;

      document.getElementById('dashboardContainer').innerHTML = html;

      // Initialize Charts
      initializeCharts(deptData, diversityData, attritionData);
    }

    function initializeCharts(deptData, diversityData, attritionData) {
      // Department Chart
      const deptCtx = document.getElementById('wfaDeptChart')?.getContext('2d');
      if (deptCtx && deptData.data?.departments) {
        new Chart(deptCtx, {
          type: 'bar',
          data: {
            labels: deptData.data.departments.map(d => d.department),
            datasets: [{
              label: 'Employee Count',
              data: deptData.data.departments.map(d => d.employee_count),
              backgroundColor: '#007bff',
              borderColor: '#0056b3',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
          }
        });
      }

      // Gender Chart
      const genderCtx = document.getElementById('wfaGenderChart')?.getContext('2d');
      if (genderCtx && diversityData.data?.gender_summary) {
        new Chart(genderCtx, {
          type: 'doughnut',
          data: {
            labels: diversityData.data.gender_summary.map(g => g.category_value),
            datasets: [{
              data: diversityData.data.gender_summary.map(g => g.employee_count),
              backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
          }
        });
      }

      // Attrition Chart
      const attrCtx = document.getElementById('wfaAttritionChart')?.getContext('2d');
      if (attrCtx && attritionData.data?.monthly_summary) {
        const last12 = attritionData.data.monthly_summary.slice(-12);
        new Chart(attrCtx, {
          type: 'line',
          data: {
            labels: last12.map(a => a.year_month.substring(0, 7)),
            datasets: [{
              label: 'Attrition Rate %',
              data: last12.map(a => a.attrition_rate_percent),
              borderColor: '#dc3545',
              backgroundColor: 'rgba(220, 53, 69, 0.1)',
              tension: 0.4,
              fill: true
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true, max: 100 } }
          }
        });
      }

      // Separation Type Chart
      const sepCtx = document.getElementById('wfaSeparationChart')?.getContext('2d');
      if (sepCtx && attritionData.data?.by_separation_type) {
        new Chart(sepCtx, {
          type: 'pie',
          data: {
            labels: attritionData.data.by_separation_type.map(s => s.separation_type),
            datasets: [{
              data: attritionData.data.by_separation_type.map(s => s.count),
              backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
          }
        });
      }
    }

    // Load dashboard on page load
    document.addEventListener('DOMContentLoaded', function() {
      loadWFADashboard();
      
      const preloader = document.querySelector('.preloader');
      setTimeout(() => {
        if (preloader) {
          preloader.style.display = 'none';
        }
      }, 3000);
    });
  </script>

  <script>
    // Hide preloader after page loads
    document.addEventListener('DOMContentLoaded', function() {
      const preloader = document.querySelector('.preloader');
      setTimeout(() => {
        if (preloader) {
          preloader.style.display = 'none';
        }
      }, 3000); // Allow animation to loop multiple times
    });
  </script>
</body>

</html>