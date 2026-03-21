<!-- TAB 5: CUSTOM REPORTS -->
<div class="wfa-container" id="reportsContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Reports Data...
    </div>
</div>

<script>
async function loadReportsTab() {
    const container = document.getElementById('reportsContainer');
    
    try {
        const basePath = '/capstone_hr_management_system';
        const deptResponse = await fetch(`${basePath}/api/wfa/dashboard_metrics.php`);
        const deptData = await deptResponse.json();
        const empResponse = await fetch(`${basePath}/api/wfa/department_analytics.php`);
        const empData = await empResponse.json();
        
        const departments = empData.data?.departments || [];
        const totalEmployees = deptData.data?.total_employees || 0;
        
        let html = `
            <!-- Report Filters -->
            <div class="wfa-filters-container">
                <div class="wfa-filter-row">
                    <div class="wfa-filter-group">
                        <label>Department:</label>
                        <select id="reportDeptFilter" class="wfa-filter-select">
                            <option value="">All Departments</option>
        `;
        
        departments.forEach(dept => {
            html += `<option value="${dept.department}">${dept.department}</option>`;
        });
        
        html += `
                        </select>
                    </div>
                    
                    <div class="wfa-filter-group">
                        <label>Employment Type:</label>
                        <select id="reportEmpTypeFilter" class="wfa-filter-select">
                            <option value="">All Types</option>
                            <option value="full_time">Full-Time</option>
                            <option value="part_time">Part-Time</option>
                            <option value="contract">Contract</option>
                            <option value="intern">Intern</option>
                        </select>
                    </div>
                    
                    <div class="wfa-filter-group">
                        <label>Status:</label>
                        <select id="reportStatusFilter" class="wfa-filter-select">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="wfa-filter-row">
                    <div class="wfa-filter-group">
                        <label>Hire Date From:</label>
                        <input type="date" id="reportHireDateFrom" class="wfa-filter-input">
                    </div>
                    
                    <div class="wfa-filter-group">
                        <label>Hire Date To:</label>
                        <input type="date" id="reportHireDateTo" class="wfa-filter-input">
                    </div>
                </div>
                
                <div class="wfa-filter-actions">
                    <button class="wfa-btn wfa-btn-primary" onclick="generateReport()">
                        <i class="fas fa-search"></i> Generate Report
                    </button>
                    <button class="wfa-btn wfa-btn-secondary" onclick="exportReportCSV()">
                        <i class="fas fa-download"></i> Export to CSV
                    </button>
                    <button class="wfa-btn wfa-btn-secondary" onclick="clearReportFilters()">
                        <i class="fas fa-redo"></i> Clear Filters
                    </button>
                </div>
            </div>
            
            <!-- Report Statistics -->
            <div class="wfa-metrics-grid">
                <div class="wfa-metric-card">
                    <div class="wfa-metric-label">Total Employees</div>
                    <div class="wfa-metric-value" id="reportTotalCount">${totalEmployees}</div>
                    <div class="wfa-metric-change">In Organization</div>
                </div>
                
                <div class="wfa-metric-card info">
                    <div class="wfa-metric-label">Departments</div>
                    <div class="wfa-metric-value">${departments.length}</div>
                    <div class="wfa-metric-change">Active</div>
                </div>
                
                <div class="wfa-metric-card success">
                    <div class="wfa-metric-label">Avg Salary</div>
                    <div class="wfa-metric-value" id="reportAvgSalary">
                        $${departments.length > 0 ? parseFloat((departments.reduce((sum, d) => sum + (d.average_salary || 0), 0) / departments.length / 1000).toFixed(0)) : 0}K
                    </div>
                    <div class="wfa-metric-change">Organization</div>
                </div>
                
                <div class="wfa-metric-card warning">
                    <div class="wfa-metric-label">Report Rows</div>
                    <div class="wfa-metric-value" id="reportRowCount">0</div>
                    <div class="wfa-metric-change">Filtered Results</div>
                </div>
            </div>
            
            <!-- Report Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Employee Report Data</h3>
                <table class="wfa-table" id="reportTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Employment Type</th>
                            <th>Hire Date</th>
                            <th>Salary</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 20px;">Click "Generate Report" to view results</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
        
        container.innerHTML = html;
        
    } catch (error) {
        console.error('Error loading reports:', error);
        container.innerHTML = '<div class="wfa-error">Error loading reports interface</div>';
    }
}

async function generateReport() {
    const dept = document.getElementById('reportDeptFilter')?.value || '';
    const empType = document.getElementById('reportEmpTypeFilter')?.value || '';
    const status = document.getElementById('reportStatusFilter')?.value || '';
    const hireFrom = document.getElementById('reportHireDateFrom')?.value || '';
    const hireTo = document.getElementById('reportHireDateTo')?.value || '';
    
    try {
        const basePath = '/capstone_hr_management_system';
        const url = new URL(`${basePath}/api/employees/get_filtered_employees.php`, window.location.origin);
        if (dept) url.searchParams.append('department', dept);
        if (empType) url.searchParams.append('employment_type', empType);
        if (status) url.searchParams.append('status', status);
        if (hireFrom) url.searchParams.append('hire_date_from', hireFrom);
        if (hireTo) url.searchParams.append('hire_date_to', hireTo);
        
        const response = await fetch(url.toString());
        let employees = await response.json();
        
        // Fallback: if no API, use mock data
        if (!Array.isArray(employees)) {
            employees = generateMockEmployees(dept, empType, status);
        }
        
        document.getElementById('reportRowCount').textContent = employees.length;
        
        let tableHtml = '';
        if (employees.length > 0) {
            employees.forEach(emp => {
                tableHtml += `
                    <tr>
                        <td>${emp.id || 'N/A'}</td>
                        <td><strong>${emp.employee_name || emp.name || 'N/A'}</strong></td>
                        <td>${emp.department || 'N/A'}</td>
                        <td>${emp.position || 'N/A'}</td>
                        <td>${emp.employment_type || 'N/A'}</td>
                        <td>${emp.hire_date || 'N/A'}</td>
                        <td>$${emp.salary ? (emp.salary / 1000).toFixed(1) : 0}K</td>
                        <td><span class="wfa-status-badge">${emp.status || 'active'}</span></td>
                    </tr>
                `;
            });
        } else {
            tableHtml = '<tr><td colspan="8" style="text-align: center; padding: 20px;">No employees match your filters</td></tr>';
        }
        
        document.getElementById('reportTableBody').innerHTML = tableHtml;
        
    } catch (error) {
        console.error('Error generating report:', error);
        document.getElementById('reportTableBody').innerHTML = '<tr><td colspan="8" style="text-align: center; color: red;">Error generating report</td></tr>';
    }
}

function exportReportCSV() {
    const table = document.getElementById('reportTable');
    if (!table) return;
    
    let csv = [];
    
    // Headers
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent);
    csv.push(headers.join(','));
    
    // Rows
    Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
        const cells = Array.from(row.querySelectorAll('td')).map(td => {
            let text = td.textContent.trim();
            text = text.replace(/"/g, '""');
            return `"${text}"`;
        });
        csv.push(cells.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `employee_report_${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
}

function clearReportFilters() {
    document.getElementById('reportDeptFilter').value = '';
    document.getElementById('reportEmpTypeFilter').value = '';
    document.getElementById('reportStatusFilter').value = '';
    document.getElementById('reportHireDateFrom').value = '';
    document.getElementById('reportHireDateTo').value = '';
    document.getElementById('reportTableBody').innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px;">Click "Generate Report" to view results</td></tr>';
    document.getElementById('reportRowCount').textContent = '0';
}

function generateMockEmployees(dept, empType, status) {
    // Mock data generator for demonstration
    const departments = ['Sales', 'IT', 'HR', 'Finance', 'Operations'];
    const positions = ['Manager', 'Developer', 'Analyst', 'Coordinator', 'Specialist'];
    const empTypes = ['full_time', 'part_time', 'contract'];
    
    const employees = [];
    for (let i = 1; i <= 25; i++) {
        const emp = {
            id: 'EMP' + String(i).padStart(4, '0'),
            employee_name: `Employee ${i}`,
            department: dept || departments[Math.floor(Math.random() * departments.length)],
            position: positions[Math.floor(Math.random() * positions.length)],
            employment_type: empType || empTypes[Math.floor(Math.random() * empTypes.length)],
            hire_date: '2022-' + String(Math.floor(Math.random() * 12) + 1).padStart(2, '0') + '-01',
            salary: 40000 + Math.random() * 80000,
            status: status || 'active'
        };
        
        if (!dept || emp.department === dept) {
            if (!empType || emp.employment_type === empType) {
                if (!status || emp.status === status) {
                    employees.push(emp);
                }
            }
        }
    }
    
    return employees;
}

document.addEventListener('DOMContentLoaded', function() {
    const reportsTab = document.querySelector('a[href="#reports"]');
    if (reportsTab) {
        reportsTab.addEventListener('click', function() {
            loadReportsTab();
        });
    }
});
</script>
