<!-- TAB 3: DIVERSITY & INCLUSION -->
<div class="wfa-container" id="diversityContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Diversity Data...
    </div>
</div>

<script>
async function loadDiversityTab() {
    const container = document.getElementById('diversityContainer');
    
    try {
        const basePath = '/capstone_hr_management_system';
        const deptResponse = await fetch(`${basePath}/api/wfa/department_analytics.php`);
        const deptData = await deptResponse.json();
        const diversityResponse = await fetch(`${basePath}/api/wfa/diversity_metrics.php`);
        const diversityData = await diversityResponse.json();
        
        const departments = deptData.data?.departments || [];
        const genderData = diversityData.data?.gender_summary || [];
        
        let html = `
            <!-- Diversity Metrics -->
            <div class="wfa-metrics-grid">
                <div class="wfa-metric-card">
                    <div class="wfa-metric-label">Total Employees</div>
                    <div class="wfa-metric-value">${departments.reduce((sum, d) => sum + (d.employee_count || 0), 0)}</div>
                    <div class="wfa-metric-change">Organization</div>
                </div>
                
                <div class="wfa-metric-card info">
                    <div class="wfa-metric-label">Departments</div>
                    <div class="wfa-metric-value">${departments.length}</div>
                    <div class="wfa-metric-change">Active units</div>
                </div>
                
                <div class="wfa-metric-card success">
                    <div class="wfa-metric-label">Avg Department Size</div>
                    <div class="wfa-metric-value">${departments.length > 0 ? (departments.reduce((sum, d) => sum + (d.employee_count || 0), 0) / departments.length).toFixed(0) : 0}</div>
                    <div class="wfa-metric-change">Employees</div>
                </div>
                
                <div class="wfa-metric-card warning">
                    <div class="wfa-metric-label">Avg Performance</div>
                    <div class="wfa-metric-value">${departments.length > 0 ? (departments.reduce((sum, d) => sum + (d.average_performance_score || 0), 0) / departments.length).toFixed(1) : 0}/5.0</div>
                    <div class="wfa-metric-change">Rating</div>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="wfa-charts-grid">
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Gender Distribution</div>
                    <canvas id="genderChart"></canvas>
                </div>
                
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Employees by Department</div>
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
            
            <!-- Department Diversity Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Department Diversity & Statistics</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Employees</th>
                            <th>Avg Salary</th>
                            <th>Avg Performance</th>
                            <th>Avg Tenure (Years)</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        if (departments.length > 0) {
            departments.forEach(d => {
                html += `
                    <tr>
                        <td><strong>${d.department || 'N/A'}</strong></td>
                        <td>${d.employee_count || 0}</td>
                        <td>₱${(d.average_salary || 0).toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
                        <td>${(d.average_performance_score || 0).toFixed(1)}/5.0</td>
                        <td>${(d.average_tenure_years || 0).toFixed(1)}</td>
                    </tr>
                `;
            });
        } else {
            html += '<tr><td colspan="5" style="text-align: center; padding: 20px;">No department data available</td></tr>';
        }
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        container.innerHTML = html;
        initDiversityCharts(genderData, departments);
        
    } catch (error) {
        console.error('Error loading diversity data:', error);
        container.innerHTML = '<div class="wfa-error">Error loading diversity data</div>';
    }
}

function initDiversityCharts(genderData, departments) {
    // Gender Chart
    if (genderData.length > 0) {
        const genderCtx = document.getElementById('genderChart')?.getContext('2d');
        if (genderCtx) {
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: genderData.map(g => g.category_value),
                    datasets: [{
                        data: genderData.map(g => g.employee_count),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    }
    
    // Department Chart
    if (departments.length > 0) {
        const deptCtx = document.getElementById('departmentChart')?.getContext('2d');
        if (deptCtx) {
            new Chart(deptCtx, {
                type: 'bar',
                data: {
                    labels: departments.map(d => d.department),
                    datasets: [{
                        label: 'Employee Count',
                        data: departments.map(d => d.employee_count),
                        backgroundColor: '#17a2b8'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const diversityTab = document.querySelector('a[href="#diversity"]');
    if (diversityTab) {
        diversityTab.addEventListener('click', function() {
            loadDiversityTab();
        });
    }
});
</script>
