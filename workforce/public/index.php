<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workforce Analytics & Reporting Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/bcp-logo2.png" alt="BCP" class="logo-image">
            </div>
        </div>
        
        <div class="user-profile">
            <div class="user-avatar">HR</div>
            <h3 class="user-name">HR Analytics</h3>
            <p class="user-email">analytics@bcp.edu.ph</p>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <h4 class="nav-section-title">NAVIGATION</h4>
                <button class="nav-btn active" data-tab="dashboard" onclick="switchTab('dashboard')">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </button>
                <button class="nav-btn" data-tab="attrition" onclick="switchTab('attrition')">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Attrition</span>
                </button>
                <button class="nav-btn" data-tab="diversity" onclick="switchTab('diversity')">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Diversity</span>
                </button>
                <button class="nav-btn" data-tab="performance" onclick="switchTab('performance')">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                        <polyline points="17 6 23 6 23 12"></polyline>
                    </svg>
                    <span>Performance</span>
                </button>
                <button class="nav-btn" data-tab="custom-report" onclick="switchTab('custom-report')">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Reports</span>
                </button>
            </div>
        </nav>
    </aside>

    <div class="main-content">
        <div class="container">
        <!-- Header -->
        <header class="header">
            <!-- Hamburger Menu Button -->
            <button class="hamburger-menu" id="hamburger" onclick="toggleSidebar()">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="header-content">
                <h1>Bestlink College of the Philippines</h1>
                <p>Workforce Analytics & Reporting</p>
            </div>
            <div class="header-utilities">
                <div class="clock" id="clock">00:00:00</div>
                <button class="menu-btn" onclick="toggleMenu()" title="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </header>

        <!-- Include Tab Content Files -->
        <div id="dashboard" class="tab-content active">
            <?php include 'dashboard.php'; ?>
        </div>
        <?php include 'attrition.php'; ?>
        <?php include 'diversity.php'; ?>
        <?php include 'performance.php'; ?>
        <?php include 'reports.php'; ?>
        </div>
    </div>

    <!-- Chart instances storage -->
    <script>
        let chartInstances = {
            department: null,
            gender: null,
            age: null,
            attrition: null,
            performance: null,
            genderDiversity: null,
            ageDiversity: null,
            departmentDiversity: null,
            performanceDist: null,
            salary: null,
            tenure: null
        };

        let allAtRiskEmployees = {};
        let customReportData = [];
    </script>

    <script src="../assets/app.js"></script>
</body>
</html>
