<!-- TAB 4: PERFORMANCE -->
<div class="wfa-container" id="performanceContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Performance Data...
    </div>
</div>

<script>
async function loadPerformanceTab() {
    const container = document.getElementById('performanceContainer');
    
    try {
        const basePath = '/capstone_hr_management_system';
        const deptResponse = await fetch(`${basePath}/api/wfa/department_analytics.php`);
        const deptData = await deptResponse.json();
        const atRiskResponse = await fetch(`${basePath}/api/wfa/at_risk_employees.php?limit=50`);
        const atRiskData = await atRiskResponse.json();
        
        const departments = deptData.data?.departments || [];
        const employees = atRiskData.data?.employees || [];
        
        let html = `
            <!-- Performance Metrics -->
            <div class="wfa-metrics-grid">
                <div class="wfa-metric-card success">
                    <div class="wfa-metric-label">Avg Performance Score</div>
                    <div class="wfa-metric-value">${departments.length > 0 ? (departments.reduce((sum, d) => sum + (d.average_performance_score || 0), 0) / departments.length).toFixed(1) : 0}/5.0</div>
                    <div class="wfa-metric-change">Organization</div>
                </div>
                
                <div class="wfa-metric-card info">
                    <div class="wfa-metric-label">High Performers</div>
                    <div class="wfa-metric-value">${departments.filter(d => (d.average_performance_score || 0) >= 4.0).length}</div>
                    <div class="wfa-metric-change">Depts (≥4.0)</div>
                </div>
                
                <div class="wfa-metric-card warning">
                    <div class="wfa-metric-label">At-Risk Employees</div>
                    <div class="wfa-metric-value">${employees.length}</div>
                    <div class="wfa-metric-change">Monitored</div>
                </div>
                
                <div class="wfa-metric-card">
                    <div class="wfa-metric-label">Departments Reviewed</div>
                    <div class="wfa-metric-value">${departments.length}</div>
                    <div class="wfa-metric-change">Active</div>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="wfa-charts-grid">
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Dept Performance Distribution</div>
                    <canvas id="performanceDistChart"></canvas>
                </div>
                
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Risk Level Distribution</div>
                    <canvas id="riskLevelChart"></canvas>
                </div>
            </div>
            
            <!-- At-Risk Employees Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Monitored Employees (Performance & Risk)</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Performance</th>
                            <th>Risk Level</th>
                            <th>Risk Score</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        if (employees.length > 0) {
            employees.slice(0, 20).forEach(emp => {
                const riskClass = emp.risk_level.toLowerCase();
                html += `
                    <tr>
                        <td><strong>${emp.employee_name || 'N/A'}</strong></td>
                        <td>${emp.department || 'N/A'}</td>
                        <td>${emp.position || 'N/A'}</td>
                        <td>${emp.performance_score || 0}/5.0</td>
                        <td><span class="wfa-risk-badge ${riskClass}">${emp.risk_level}</span></td>
                        <td>${emp.risk_score || 0}</td>
                    </tr>
                `;
            });
        } else {
            html += '<tr><td colspan="6" style="text-align: center; padding: 20px;">No performance data available</td></tr>';
        }
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        container.innerHTML = html;
        initPerformanceCharts(departments, employees);
        
    } catch (error) {
        console.error('Error loading performance data:', error);
        container.innerHTML = '<div class="wfa-error">Error loading performance data</div>';
    }
}

function initPerformanceCharts(departments, employees) {
    // Department Performance Distribution
    if (departments.length > 0) {
        const perfCtx = document.getElementById('performanceDistChart')?.getContext('2d');
        if (perfCtx) {
            new Chart(perfCtx, {
                type: 'bar',
                data: {
                    labels: departments.map(d => d.department),
                    datasets: [{
                        label: 'Performance Score',
                        data: departments.map(d => d.average_performance_score || 0),
                        backgroundColor: departments.map(d => {
                            const score = d.average_performance_score || 0;
                            return score >= 4.0 ? '#28a745' : score >= 3.0 ? '#ffc107' : '#dc3545';
                        })
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, max: 5 } }
                }
            });
        }
    }
    
    // Risk Level Distribution
    if (employees.length > 0) {
        const riskCounts = {
            'high': employees.filter(e => e.risk_level === 'high').length,
            'medium': employees.filter(e => e.risk_level === 'medium').length,
            'low': employees.filter(e => e.risk_level === 'low').length
        };
        
        const riskCtx = document.getElementById('riskLevelChart')?.getContext('2d');
        if (riskCtx) {
            new Chart(riskCtx, {
                type: 'pie',
                data: {
                    labels: ['High Risk', 'Medium Risk', 'Low Risk'],
                    datasets: [{
                        data: [riskCounts.high, riskCounts.medium, riskCounts.low],
                        backgroundColor: ['#dc3545', '#ffc107', '#28a745']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const perfTab = document.querySelector('a[href="#performance"]');
    if (perfTab) {
        perfTab.addEventListener('click', function() {
            loadPerformanceTab();
        });
    }
});
</script>
