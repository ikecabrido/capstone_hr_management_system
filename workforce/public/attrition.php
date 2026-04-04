<!-- TAB: ATTRITION & TURNOVER -->
<div class="wfa-container" id="attritionContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Attrition Data...
    </div>
</div>

<script>
async function loadAttritionTab() {
    console.log('loadAttritionTab() called');
    const container = document.getElementById('attritionContainer');
    
    if (!container) {
        console.error('Attrition container not found');
        return;
    }
    
    try {
        const basePath = '/capstone_hr_management_system';
        console.log('Fetching employee data for attrition...');
        
        // Fetch both employee data and insights
        const [empResponse, insightsResponse] = await Promise.all([
            fetch(`${basePath}/api/wfa/employees_data.php`),
            fetch(`${basePath}/api/wfa/insights_analytics.php`)
        ]);
        
        if (!empResponse.ok) throw new Error(`Employees API Error: ${empResponse.status}`);
        if (!insightsResponse.ok) throw new Error(`Insights API Error: ${insightsResponse.status}`);
        
        const empData = await empResponse.json();
        const insightsData = await insightsResponse.json();
        
        const employees = empData.data?.employees || [];
        const totalEmployees = employees.length;
        const insights = insightsData.data?.insights || [];
        
        // Calculate employment status
        const statusDist = {};
        employees.forEach(emp => {
            if (!statusDist[emp.employment_status]) {
                statusDist[emp.employment_status] = 0;
            }
            statusDist[emp.employment_status]++;
        });
        
        // Calculate tenure groups
        const tenureGroups = {
            'New (0-1 year)': employees.filter(e => parseInt(e.years_employed) <= 1).length,
            'Developing (1-3 years)': employees.filter(e => parseInt(e.years_employed) > 1 && parseInt(e.years_employed) <= 3).length,
            'Experienced (3-5 years)': employees.filter(e => parseInt(e.years_employed) > 3 && parseInt(e.years_employed) <= 5).length,
            'Senior (5+ years)': employees.filter(e => parseInt(e.years_employed) > 5).length
        };
        
        // Calculate department attrition
        const deptStats = {};
        employees.forEach(emp => {
            if (!deptStats[emp.department]) {
                deptStats[emp.department] = { total: 0, active: 0, inactive: 0 };
            }
            deptStats[emp.department].total++;
            if (emp.employment_status === 'Active') {
                deptStats[emp.department].active++;
            } else {
                deptStats[emp.department].inactive++;
            }
        });
        
        // Calculate turnover rates per department
        const deptTurnover = Object.entries(deptStats).map(([dept, stats]) => ({
            department: dept,
            total: stats.total,
            active: stats.active,
            inactive: stats.inactive,
            turnover_rate: stats.total > 0 ? ((stats.inactive / stats.total) * 100).toFixed(1) : 0,
            health: stats.total > 0 ? (
                ((stats.inactive / stats.total) * 100) > 20 ? 'At Risk' : 
                ((stats.inactive / stats.total) * 100) > 10 ? 'Caution' : 
                'Healthy'
            ) : 'Unknown'
        })).sort((a, b) => parseFloat(b.turnover_rate) - parseFloat(a.turnover_rate));
        
        const activeCount = statusDist['Active'] || 0;
        const inactiveCount = totalEmployees - activeCount;
        const attritionRate = totalEmployees > 0 ? ((inactiveCount / totalEmployees) * 100).toFixed(1) : 0;
        
        let html = `
            <!-- Insights Alert Section -->
            <div style="margin-bottom: 30px;">
                ${insights.map(insight => `
                    <div class="wfa-insight-card ${insight.type}">
                        <div class="insight-header">
                            <span class="insight-icon">${insight.icon}</span>
                            <div>
                                <h4 style="margin: 0 0 5px 0; color: #333;">${insight.title}</h4>
                                <p style="margin: 0; color: #666; font-size: 14px;">${insight.message}</p>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
            
            <!-- Attrition Metrics -->
            <div class="wfa-metrics-grid">
                <div class="wfa-metric-card">
                    <div class="wfa-metric-label">Total Employees</div>
                    <div class="wfa-metric-value">${totalEmployees}</div>
                    <div class="wfa-metric-change">Current Headcount</div>
                </div>
                
                <div class="wfa-metric-card success">
                    <div class="wfa-metric-label">Active Employees</div>
                    <div class="wfa-metric-value">${activeCount}</div>
                    <div class="wfa-metric-change">Currently Active</div>
                </div>
                
                <div class="wfa-metric-card danger">
                    <div class="wfa-metric-label">Inactive Employees</div>
                    <div class="wfa-metric-value">${inactiveCount}</div>
                    <div class="wfa-metric-change">Separated</div>
                </div>
                
                <div class="wfa-metric-card warning">
                    <div class="wfa-metric-label">Attrition Rate</div>
                    <div class="wfa-metric-value">${attritionRate}%</div>
                    <div class="wfa-metric-change">Overall Rate</div>
                </div>
            </div>
            
            <!-- Attrition Charts -->
            <div class="wfa-charts-grid">
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Tenure Distribution</div>
                    <canvas id="tenureChart"></canvas>
                </div>
                
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Employment Status</div>
                    <canvas id="attritionStatusChart"></canvas>
                </div>
            </div>
            
            <!-- Tenure Distribution Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Employee Tenure Distribution</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Tenure Group</th>
                            <th>Employee Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        Object.keys(tenureGroups).forEach(group => {
            const count = tenureGroups[group];
            const percent = totalEmployees > 0 ? ((count / totalEmployees) * 100).toFixed(1) : 0;
            html += `
                <tr>
                    <td><strong>${group}</strong></td>
                    <td>${count}</td>
                    <td>${percent}%</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
            
            <!-- Department Attrition Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">📊 Department Turnover Analysis</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Total Employees</th>
                            <th>Active</th>
                            <th>Inactive</th>
                            <th>Turnover Rate</th>
                            <th>Health Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        deptTurnover.forEach(dept => {
            const healthColor = dept.health === 'At Risk' ? '#dc3545' : (dept.health === 'Caution' ? '#ffc107' : '#28a745');
            const healthBg = dept.health === 'At Risk' ? '#ffe5e5' : (dept.health === 'Caution' ? '#fff9e6' : '#e5f5ea');
            html += `
                <tr>
                    <td><strong>${dept.department}</strong></td>
                    <td>${dept.total}</td>
                    <td><span style="color: #4CAF50; font-weight: bold;">${dept.active}</span></td>
                    <td><span style="color: #f44336; font-weight: bold;">${dept.inactive}</span></td>
                    <td><strong style="color: ${dept.turnover_rate > 15 ? '#dc3545' : (dept.turnover_rate > 10 ? '#ffc107' : '#28a745')}">${dept.turnover_rate}%</strong></td>
                    <td><span style="background: ${healthBg}; color: ${healthColor}; padding: 5px 10px; border-radius: 4px; font-weight: 600;">${dept.health}</span></td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        console.log('Attrition HTML generated');
        container.innerHTML = html;
        
        // Initialize charts
        initializeAttritionCharts(tenureGroups, statusDist, totalEmployees);
        
    } catch (error) {
        console.error('Error loading attrition data:', error);
        container.innerHTML = '<div style="padding: 20px; color: #d32f2f;">Error loading attrition data: ' + error.message + '</div>';
    }
}

function initializeAttritionCharts(tenureGroups, statusDist, totalEmployees) {
    console.log('Initializing attrition charts');
    
    // Tenure Chart
    const tenureCtx = document.getElementById('tenureChart');
    if (tenureCtx) {
        const tenureLabels = Object.keys(tenureGroups);
        const tenureCounts = tenureLabels.map(t => tenureGroups[t]);
        
        new Chart(tenureCtx, {
            type: 'bar',
            data: {
                labels: tenureLabels,
                datasets: [{
                    label: 'Employees',
                    data: tenureCounts,
                    backgroundColor: '#667eea',
                    borderColor: '#764ba2',
                    borderWidth: 1
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
        console.log('Tenure chart created');
    }
    
    // Status Chart
    const statusCtx = document.getElementById('attritionStatusChart');
    if (statusCtx) {
        const statusLabels = Object.keys(statusDist);
        const statusCounts = statusLabels.map(s => statusDist[s]);
        
        const colors = [];
        statusLabels.forEach(label => {
            colors.push(label === 'Active' ? '#4CAF50' : '#f44336');
        });
        
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: colors,
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
                        labels: { padding: 15, font: { size: 12 } }
                    }
                }
            }
        });
        console.log('Status chart created');
    }
}
</script>
