<!-- TAB: DIVERSITY & INCLUSION -->
<div class="wfa-container" id="diversityContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Diversity Data...
    </div>
</div>

<script>
async function loadDiversityTab() {
    console.log('loadDiversityTab() called');
    const container = document.getElementById('diversityContainer');
    
    if (!container) {
        console.error('Diversity container not found');
        return;
    }
    
    try {
        const basePath = '/capstone_hr_management_system';
        console.log('Fetching employee data for diversity...');
        
        const response = await fetch(`${basePath}/api/wfa/employees_data.php`);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Employee data:', data);
        
        const employees = data.data?.employees || [];
        const totalEmployees = employees.length;
        
        // Calculate department distribution
        const deptDist = {};
        employees.forEach(emp => {
            if (!deptDist[emp.department]) {
                deptDist[emp.department] = 0;
            }
            deptDist[emp.department]++;
        });
        
        // Calculate position distribution
        const posDist = {};
        employees.forEach(emp => {
            if (!posDist[emp.position]) {
                posDist[emp.position] = 0;
            }
            posDist[emp.position]++;
        });
        
        // Calculate tenure distribution
        const tenureDist = {};
        employees.forEach(emp => {
            const tenure = parseInt(emp.years_employed) || 0;
            const tenureRange = tenure === 0 ? '0 years' : tenure === 1 ? '1 year' : `${tenure} years`;
            if (!tenureDist[tenureRange]) {
                tenureDist[tenureRange] = 0;
            }
            tenureDist[tenureRange]++;
        });
        
        // Calculate status distribution
        const statusDist = {};
        employees.forEach(emp => {
            if (!statusDist[emp.employment_status]) {
                statusDist[emp.employment_status] = 0;
            }
            statusDist[emp.employment_status]++;
        });
        
        const activeCount = statusDist['Active'] || 0;
        const departmentCount = Object.keys(deptDist).length;
        
        let html = `
            <!-- Diversity Metrics -->
            <div class="wfa-metrics-grid">
                <div class="wfa-metric-card">
                    <div class="wfa-metric-label">Total Employees</div>
                    <div class="wfa-metric-value">${totalEmployees}</div>
                    <div class="wfa-metric-change">Organization</div>
                </div>
                
                <div class="wfa-metric-card success">
                    <div class="wfa-metric-label">Active Employees</div>
                    <div class="wfa-metric-value">${activeCount}</div>
                    <div class="wfa-metric-change">Currently Employed</div>
                </div>
                
                <div class="wfa-metric-card info">
                    <div class="wfa-metric-label">Departments</div>
                    <div class="wfa-metric-value">${departmentCount}</div>
                    <div class="wfa-metric-change">Unique Departments</div>
                </div>
                
                <div class="wfa-metric-card warning">
                    <div class="wfa-metric-label">Positions</div>
                    <div class="wfa-metric-value">${Object.keys(posDist).length}</div>
                    <div class="wfa-metric-change">Unique Positions</div>
                </div>
            </div>
            
            <!-- Distribution Charts -->
            <div class="wfa-charts-grid">
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Department Distribution</div>
                    <canvas id="departmentChart"></canvas>
                </div>
                
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Employment Status Distribution</div>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            
            <!-- Department Breakdown Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Department Breakdown</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Employee Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        Object.keys(deptDist).sort().forEach(dept => {
            const count = deptDist[dept];
            const percent = totalEmployees > 0 ? ((count / totalEmployees) * 100).toFixed(1) : 0;
            html += `
                <tr>
                    <td><strong>${dept}</strong></td>
                    <td>${count}</td>
                    <td>${percent}%</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
            
            <!-- Position Distribution Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Position Breakdown</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Employee Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        Object.keys(posDist).sort().forEach(pos => {
            const count = posDist[pos];
            const percent = totalEmployees > 0 ? ((count / totalEmployees) * 100).toFixed(1) : 0;
            html += `
                <tr>
                    <td><strong>${pos}</strong></td>
                    <td>${count}</td>
                    <td>${percent}%</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
            
            <!-- Tenure Distribution Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Tenure Distribution</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Tenure</th>
                            <th>Employee Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        const tenureOrder = ['0 years', '1 year'];
        Object.keys(tenureDist).sort().forEach(tenure => {
            const count = tenureDist[tenure];
            const percent = totalEmployees > 0 ? ((count / totalEmployees) * 100).toFixed(1) : 0;
            html += `
                <tr>
                    <td><strong>${tenure}</strong></td>
                    <td>${count}</td>
                    <td>${percent}%</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        console.log('Diversity HTML generated');
        container.innerHTML = html;
        
        // Initialize charts
        initializeDiversityCharts(deptDist, statusDist);
        
    } catch (error) {
        console.error('Error loading diversity data:', error);
        container.innerHTML = '<div style="padding: 20px; color: #d32f2f;">Error loading diversity data: ' + error.message + '</div>';
    }
}

function initializeDiversityCharts(deptDist, statusDist) {
    console.log('Initializing diversity charts');
    
    // Department Chart
    const deptCtx = document.getElementById('departmentChart');
    if (deptCtx) {
        const deptLabels = Object.keys(deptDist).sort();
        const deptCounts = deptLabels.map(d => deptDist[d]);
        
        new Chart(deptCtx, {
            type: 'pie',
            data: {
                labels: deptLabels,
                datasets: [{
                    data: deptCounts,
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#f093fb',
                        '#f5576c',
                        '#00c9ff',
                        '#92fe9d',
                        '#ffa751',
                        '#ffe259'
                    ],
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
        console.log('Department chart created');
    }
    
    // Status Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const statusLabels = Object.keys(statusDist);
        const statusCounts = statusLabels.map(s => statusDist[s]);
        
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Employees',
                    data: statusCounts,
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
        console.log('Status chart created');
    }
}
</script>
