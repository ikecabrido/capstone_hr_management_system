<?php
/**
 * Workforce Analytics Dashboard
 * Main dashboard with all key metrics and visualizations
 */
?>

<div id="dashboard-content" class="dashboard-content">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h2>Executive Dashboard</h2>
        <p class="subtitle">Real-time workforce analytics and performance metrics</p>
    </div>

    <!-- Top KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #667eea;">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="kpi-content">
                <h3>Total Employees</h3>
                <p class="kpi-value" id="kpi-total-employees">0</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon" style="background: #f39c12;">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                    <path d="M12.5 7H11v6l5.25 3.15"></path>
                </svg>
            </div>
            <div class="kpi-content">
                <h3>Avg Performance</h3>
                <p class="kpi-value" id="kpi-avg-performance">0.0</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon" style="background: #27ae60;">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="kpi-content">
                <h3>Attendance Rate</h3>
                <p class="kpi-value" id="kpi-attendance-rate">0%</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon" style="background: #e74c3c;">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
                </svg>
            </div>
            <div class="kpi-content">
                <h3>At-Risk Employees</h3>
                <p class="kpi-value" id="kpi-at-risk">0</p>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="charts-row">
        <!-- Attendance Breakdown -->
        <div class="chart-card">
            <div class="chart-header">
                <h3>Attendance Status Distribution</h3>
            </div>
            <div class="chart-container">
                <canvas id="chartAttendanceBreakdown"></canvas>
            </div>
        </div>

        <!-- Performance Distribution -->
        <div class="chart-card">
            <div class="chart-header">
                <h3>Performance Ratings</h3>
            </div>
            <div class="chart-container">
                <canvas id="chartPerformanceDistribution"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="charts-row">
        <!-- Attendance Trend -->
        <div class="chart-card full-width">
            <div class="chart-header">
                <h3>Attendance Trend (Last 30 Days)</h3>
            </div>
            <div class="chart-container">
                <canvas id="chartAttendanceTrend"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 3 -->
    <div class="charts-row">
        <!-- Department Distribution -->
        <div class="chart-card">
            <div class="chart-header">
                <h3>Employees by Department</h3>
            </div>
            <div class="chart-container">
                <canvas id="chartDepartmentDistribution"></canvas>
            </div>
        </div>

        <!-- Tenure Distribution -->
        <div class="chart-card">
            <div class="chart-header">
                <h3>Employee Tenure</h3>
            </div>
            <div class="chart-container">
                <canvas id="chartTenureDistribution"></canvas>
            </div>
        </div>
    </div>

    <!-- At-Risk Employees Table -->
    <div class="data-card full-width">
        <div class="card-header">
            <h3>At-Risk Employees</h3>
            <p class="subtitle">Employees requiring attention or intervention</p>
        </div>
        <div class="table-container">
            <table class="data-table" id="tableAtRiskEmployees">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Performance Score</th>
                        <th>Risk Level</th>
                        <th>Tenure (Years)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #999;">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Department Distribution Table -->
    <div class="data-card full-width">
        <div class="card-header">
            <h3>Department Summary</h3>
            <p class="subtitle">Employee distribution across departments</p>
        </div>
        <div class="table-container">
            <table class="data-table" id="tableDepartmentSummary">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Employee Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Initialize dashboard on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeDashboard);
} else {
    initializeDashboard();
}

function initializeDashboard() {
    console.log('Initializing dashboard metrics...');
    loadDashboardMetrics();
}

function loadDashboardMetrics() {
    fetch('../api/dashboard_metrics.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Dashboard metrics loaded:', data);
            if (data.success && data.data) {
                displayMetrics(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading dashboard metrics:', error);
            showErrorMessage('Failed to load dashboard metrics');
        });
}

function displayMetrics(data) {
    // Update KPI cards
    if (data.employee_metrics) {
        document.getElementById('kpi-total-employees').textContent = data.employee_metrics.total_employees || 0;
        document.getElementById('kpi-avg-performance').textContent = (data.employee_metrics.avg_performance || 0).toFixed(2);
    }

    if (data.attendance_rate) {
        document.getElementById('kpi-attendance-rate').textContent = data.attendance_rate + '%';
    }

    if (data.at_risk_employees) {
        document.getElementById('kpi-at-risk').textContent = data.at_risk_employees.length || 0;
    }

    // Display charts
    displayAttendanceBreakdownChart(data.attendance_breakdown);
    displayPerformanceDistributionChart(data.performance_distribution);
    displayAttendanceTrendChart(data.attendance_trend);
    displayDepartmentDistributionChart(data.department_distribution);
    displayTenureDistributionChart(data.tenure_distribution);

    // Display tables
    displayAtRiskEmployeesTable(data.at_risk_employees);
    displayDepartmentSummaryTable(data.department_distribution);
}

function displayAttendanceBreakdownChart(data) {
    const ctx = document.getElementById('chartAttendanceBreakdown');
    if (!ctx) return;

    const labels = data.map(d => d.status);
    const values = data.map(d => parseInt(d.percentage));
    const colors = ['#27ae60', '#f39c12', '#e74c3c', '#3498db', '#9b59b6', '#e67e22'];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15 }
                }
            }
        }
    });
}

function displayPerformanceDistributionChart(data) {
    const ctx = document.getElementById('chartPerformanceDistribution');
    if (!ctx) return;

    const labels = data.map(d => d.performance_level);
    const values = data.map(d => parseInt(d.percentage));
    const colors = ['#27ae60', '#f39c12', '#e74c3c', '#3498db', '#9b59b6'];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Percentage (%)',
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: true }
            },
            scales: {
                x: { beginAtZero: true, max: 100 }
            }
        }
    });
}

function displayAttendanceTrendChart(data) {
    const ctx = document.getElementById('chartAttendanceTrend');
    if (!ctx) return;

    const labels = data.map(d => d.date);
    const rates = data.map(d => parseFloat(d.daily_rate));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Daily Attendance Rate (%)',
                data: rates,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
}

function displayDepartmentDistributionChart(data) {
    const ctx = document.getElementById('chartDepartmentDistribution');
    if (!ctx) return;

    const labels = data.map(d => d.department);
    const values = data.map(d => d.count);
    const colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe', '#43e97b', '#fa709a'];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Employee Count',
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function displayTenureDistributionChart(data) {
    const ctx = document.getElementById('chartTenureDistribution');
    if (!ctx) return;

    const labels = data.map(d => d.tenure_range);
    const values = data.map(d => d.count);
    const colors = ['#3498db', '#e74c3c', '#27ae60', '#f39c12'];

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15 }
                }
            }
        }
    });
}

function displayAtRiskEmployeesTable(data) {
    const table = document.getElementById('tableAtRiskEmployees');
    if (!table) return;

    const tbody = table.querySelector('tbody');
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #999;">No at-risk employees</td></tr>';
        return;
    }

    tbody.innerHTML = data.map(emp => `
        <tr>
            <td><strong>${emp.name}</strong></td>
            <td>${emp.department}</td>
            <td>${emp.position}</td>
            <td>${parseFloat(emp.performance_score).toFixed(2)}</td>
            <td>
                <span class="risk-badge risk-${emp.risk_level.toLowerCase()}">
                    ${emp.risk_level}
                </span>
            </td>
            <td>${emp.tenure_years}</td>
        </tr>
    `).join('');
}

function displayDepartmentSummaryTable(data) {
    const table = document.getElementById('tableDepartmentSummary');
    if (!table) return;

    const total = data.reduce((sum, d) => sum + d.count, 0);
    const tbody = table.querySelector('tbody');

    tbody.innerHTML = data.map(dept => {
        const percentage = ((dept.count / total) * 100).toFixed(1);
        return `
        <tr>
            <td><strong>${dept.department}</strong></td>
            <td>${dept.count}</td>
            <td>${percentage}%</td>
        </tr>
    `}).join('');
}

function showErrorMessage(message) {
    console.error(message);
    // You can add a toast notification here if available
}
</script>

<style>
.dashboard-content {
    padding: 20px;
}

.dashboard-header {
    margin-bottom: 30px;
}

.dashboard-header h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 5px;
}

.subtitle {
    color: #999;
    font-size: 14px;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kpi-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.kpi-icon svg {
    width: 30px;
    height: 30px;
}

.kpi-content h3 {
    font-size: 12px;
    text-transform: uppercase;
    color: #999;
    margin-bottom: 5px;
}

.kpi-value {
    font-size: 28px;
    font-weight: bold;
    color: #333;
}

.charts-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.chart-card, .data-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.chart-card.full-width, .data-card.full-width {
    grid-column: 1 / -1;
}

.chart-header, .card-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.chart-header h3, .card-header h3 {
    font-size: 16px;
    color: #333;
    margin: 0;
}

.chart-container {
    padding: 20px;
    position: relative;
    min-height: 300px;
}

.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: #f8f9fa;
}

.data-table th {
    padding: 15px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #666;
    border-bottom: 1px solid #ddd;
}

.data-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.data-table tbody tr:hover {
    background: #f8f9fa;
}

.risk-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.risk-high {
    background: #fee;
    color: #c33;
}

.risk-medium {
    background: #fef3cd;
    color: #664d03;
}

.risk-low {
    background: #d1e7dd;
    color: #0f5132;
}

@media (max-width: 768px) {
    .charts-row {
        grid-template-columns: 1fr;
    }
    
    .kpi-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-header h2 {
        font-size: 22px;
    }
}
</style>
