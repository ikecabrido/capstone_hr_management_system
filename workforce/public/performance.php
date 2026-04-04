<!-- TAB: PERFORMANCE & DEVELOPMENT -->
<div class="wfa-container" id="performanceContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Performance Data...
    </div>
</div>

<script>
async function loadPerformanceTab() {
    console.log('loadPerformanceTab() called');
    const container = document.getElementById('performanceContainer');
    
    if (!container) {
        console.error('Performance container not found');
        return;
    }
    
    try {
        const basePath = '/capstone_hr_management_system';
        console.log('Fetching employee data...');
        
        const response = await fetch(`${basePath}/api/wfa/employees_data.php`);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Employee data:', data);
        
        const employees = data.data?.employees || [];
        const totalEmployees = employees.length;
        
        // Calculate performance metrics
        const activeEmployees = employees.filter(e => e.employment_status === 'Active').length;
        const avgTenure = totalEmployees > 0 ? (employees.reduce((sum, e) => sum + (parseInt(e.years_employed) || 0), 0) / totalEmployees).toFixed(1) : 0;
        
        // Group by department
        const deptStats = {};
        employees.forEach(emp => {
            if (!deptStats[emp.department]) {
                deptStats[emp.department] = { count: 0, positions: new Set() };
            }
            deptStats[emp.department].count++;
            deptStats[emp.department].positions.add(emp.position);
        });
        
        let html = `
            <!-- Performance Metrics -->
            <div class="wfa-metrics-grid">
                <div class="wfa-metric-card success">
                    <div class="wfa-metric-label">Total Employees</div>
                    <div class="wfa-metric-value">${totalEmployees}</div>
                    <div class="wfa-metric-change">Organization</div>
                </div>
                
                <div class="wfa-metric-card info">
                    <div class="wfa-metric-label">Active Employees</div>
                    <div class="wfa-metric-value">${activeEmployees}</div>
                    <div class="wfa-metric-change">Currently Active</div>
                </div>
                
                <div class="wfa-metric-card warning">
                    <div class="wfa-metric-label">Avg Tenure</div>
                    <div class="wfa-metric-value">${avgTenure} yrs</div>
                    <div class="wfa-metric-change">Years with Company</div>
                </div>
                
                <div class="wfa-metric-card">
                    <div class="wfa-metric-label">Departments</div>
                    <div class="wfa-metric-value">${Object.keys(deptStats).length}</div>
                    <div class="wfa-metric-change">Active Departments</div>
                </div>
            </div>
            
            <!-- Department Distribution Chart -->
            <div class="wfa-charts-grid">
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Employees by Department</div>
                    <canvas id="deptChart"></canvas>
                </div>
                
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Employee Status</div>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            
            <!-- Employee Performance Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Employee Directory</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Hire Date</th>
                            <th>Years</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        if (employees.length > 0) {
            employees.forEach(emp => {
                const hireDate = new Date(emp.date_hired).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'});
                const statusClass = emp.employment_status === 'Active' ? 'success' : 'danger';
                html += `
                    <tr>
                        <td><strong>${emp.full_name}</strong></td>
                        <td>${emp.position || 'N/A'}</td>
                        <td>${emp.department || 'N/A'}</td>
                        <td>${emp.email || 'N/A'}</td>
                        <td>${hireDate}</td>
                        <td>${emp.years_employed || 0}</td>
                        <td><span class="wfa-risk-badge ${statusClass}">${emp.employment_status}</span></td>
                    </tr>
                `;
            });
        } else {
            html += '<tr><td colspan="7" style="text-align: center; padding: 20px;">No employee data available</td></tr>';
        }
        
        html += `
                    </tbody>
                </table>
            </div>
            
            <!-- Department Stats Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Department Summary</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Employee Count</th>
                            <th>Unique Positions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        Object.keys(deptStats).sort().forEach(dept => {
            html += `
                <tr>
                    <td><strong>${dept}</strong></td>
                    <td>${deptStats[dept].count}</td>
                    <td>${deptStats[dept].positions.size}</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        console.log('HTML generated, inserting into container');
        container.innerHTML = html;
        
        // Initialize charts
        console.log('Initializing charts...');
        initializePerformanceCharts(employees, deptStats);
        
    } catch (error) {
        console.error('Error loading performance data:', error);
        container.innerHTML = '<div style="padding: 20px; color: #d32f2f;">Error loading performance data: ' + error.message + '</div>';
    }
}

function initializePerformanceCharts(employees, deptStats) {
    console.log('Initializing performance charts');
    
    // Department Chart
    const deptCtx = document.getElementById('deptChart');
    if (deptCtx) {
        const deptLabels = Object.keys(deptStats).sort();
        const deptCounts = deptLabels.map(d => deptStats[d].count);
        
        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [{
                    label: 'Employees',
                    data: deptCounts,
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
        console.log('Department chart created');
    }
    
    // Status Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const statusCounts = {
            'Active': employees.filter(e => e.employment_status === 'Active').length,
            'Inactive': employees.filter(e => e.employment_status !== 'Active').length
        };
        
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(statusCounts),
                datasets: [{
                    data: Object.values(statusCounts),
                    backgroundColor: ['#4CAF50', '#f44336'],
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
