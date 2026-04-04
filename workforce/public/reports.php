<!-- TAB: CUSTOM REPORTS -->
<div class="wfa-container" id="reportsContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Reports Data...
    </div>
</div>

<script>
let allEmployees = [];

async function loadReportsTab() {
    console.log('loadReportsTab() called');
    const container = document.getElementById('reportsContainer');
    
    if (!container) {
        console.error('Reports container not found');
        return;
    }
    
    try {
        const basePath = '/capstone_hr_management_system';
        console.log('Fetching employee data for reports...');
        
        const response = await fetch(`${basePath}/api/wfa/employees_data.php`);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Employee data:', data);
        
        allEmployees = data.data?.employees || [];
        
        // Get unique departments
        const departments = [...new Set(allEmployees.map(e => e.department).filter(d => d))];
        
        let html = `
            <!-- Report Statistics Cards -->
            <div class="wfa-metrics-grid">
                <div class="wfa-metric-card">
                    <div class="wfa-metric-label">Total Employees</div>
                    <div class="wfa-metric-value" id="reportTotalCount">0</div>
                    <div class="wfa-metric-change">In Organization</div>
                </div>
                
                <div class="wfa-metric-card success">
                    <div class="wfa-metric-label">Active Employees</div>
                    <div class="wfa-metric-value" id="reportActiveCount">0</div>
                    <div class="wfa-metric-change">Currently Active</div>
                </div>
                
                <div class="wfa-metric-card info">
                    <div class="wfa-metric-label">Filtered Results</div>
                    <div class="wfa-metric-value" id="reportFilteredCount">0</div>
                    <div class="wfa-metric-change">Matching Criteria</div>
                </div>
                
                <div class="wfa-metric-card warning">
                    <div class="wfa-metric-label">Departments</div>
                    <div class="wfa-metric-value" id="reportDeptCount">0</div>
                    <div class="wfa-metric-change">Unique Departments</div>
                </div>
            </div>
            
            <!-- Report Filters -->
            <div class="wfa-filters-container">
                <div class="wfa-filter-row">
                    <div class="wfa-filter-group">
                        <label>Department:</label>
                        <select id="reportDeptFilter" class="wfa-filter-select" onchange="generateReport()">
                            <option value="">All Departments</option>
        `;
        
        departments.sort().forEach(dept => {
            html += `<option value="${dept}">${dept}</option>`;
        });
        
        html += `
                        </select>
                    </div>
                    
                    <div class="wfa-filter-group">
                        <label>Employment Status:</label>
                        <select id="reportStatusFilter" class="wfa-filter-select" onchange="generateReport()">
                            <option value="">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="wfa-filter-group">
                        <label>Search by Name/Email:</label>
                        <input type="text" id="reportSearchFilter" class="wfa-filter-input" placeholder="Enter name or email" onkeyup="generateReport()">
                    </div>
                </div>
                
                <div class="wfa-filter-actions">
                    <button class="wfa-btn wfa-btn-primary" onclick="generateReport()">
                        <i class="fas fa-search"></i> Generate Report
                    </button>
                    <button class="wfa-btn wfa-btn-info" onclick="exportReportCSV()">
                        <i class="fas fa-file-csv"></i> Export to CSV
                    </button>
                    <button class="wfa-btn wfa-btn-secondary" onclick="clearReportFilters()">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                </div>
            </div>
            
            <!-- Employee Report Data Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Employee Report Data</h3>
                <table class="wfa-table" id="reportTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Hire Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        <tr><td colspan="8" style="text-align: center; padding: 20px;">Click "Generate Report" to view results</td></tr>
                    </tbody>
                </table>
            </div>
        `;
        
        console.log('Reports HTML generated');
        container.innerHTML = html;
        
        // Initial statistics
        updateReportStats(allEmployees);
        
    } catch (error) {
        console.error('Error loading reports data:', error);
        container.innerHTML = '<div style="padding: 20px; color: #d32f2f;">Error loading reports data: ' + error.message + '</div>';
    }
}

function generateReport() {
    const deptFilter = document.getElementById('reportDeptFilter')?.value || '';
    const statusFilter = document.getElementById('reportStatusFilter')?.value || '';
    const searchFilter = document.getElementById('reportSearchFilter')?.value.toLowerCase() || '';
    
    let filtered = allEmployees.filter(emp => {
        const deptMatch = !deptFilter || emp.department === deptFilter;
        const statusMatch = !statusFilter || emp.employment_status === statusFilter;
        const searchMatch = !searchFilter || 
                           emp.full_name.toLowerCase().includes(searchFilter) || 
                           emp.email.toLowerCase().includes(searchFilter);
        return deptMatch && statusMatch && searchMatch;
    });
    
    updateReportTable(filtered);
    updateReportStats(filtered);
}

function updateReportTable(filteredEmployees) {
    const tbody = document.getElementById('reportTableBody');
    
    if (!tbody) return;
    
    if (filteredEmployees.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px;">No employees match the selected criteria</td></tr>';
        return;
    }
    
    let html = '';
    filteredEmployees.forEach(emp => {
        const hireDate = new Date(emp.date_hired).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'});
        const statusClass = emp.employment_status === 'Active' ? 'success' : 'danger';
        html += `
            <tr>
                <td>${emp.employee_id}</td>
                <td><strong>${emp.full_name}</strong></td>
                <td>${emp.position || 'N/A'}</td>
                <td>${emp.department || 'N/A'}</td>
                <td>${emp.email || 'N/A'}</td>
                <td>${emp.contact_number || 'N/A'}</td>
                <td>${hireDate}</td>
                <td><span class="wfa-risk-badge ${statusClass}">${emp.employment_status}</span></td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function updateReportStats(filteredEmployees) {
    const totalCount = allEmployees.length;
    const activeCount = allEmployees.filter(e => e.employment_status === 'Active').length;
    const filteredCount = filteredEmployees.length;
    const deptCount = [...new Set(allEmployees.map(e => e.department).filter(d => d))].length;
    
    document.getElementById('reportTotalCount').textContent = totalCount;
    document.getElementById('reportActiveCount').textContent = activeCount;
    document.getElementById('reportFilteredCount').textContent = filteredCount;
    document.getElementById('reportDeptCount').textContent = deptCount;
}

function exportReportCSV() {
    const deptFilter = document.getElementById('reportDeptFilter')?.value || '';
    const statusFilter = document.getElementById('reportStatusFilter')?.value || '';
    const searchFilter = document.getElementById('reportSearchFilter')?.value.toLowerCase() || '';
    
    let filtered = allEmployees.filter(emp => {
        const deptMatch = !deptFilter || emp.department === deptFilter;
        const statusMatch = !statusFilter || emp.employment_status === statusFilter;
        const searchMatch = !searchFilter || 
                           emp.full_name.toLowerCase().includes(searchFilter) || 
                           emp.email.toLowerCase().includes(searchFilter);
        return deptMatch && statusMatch && searchMatch;
    });
    
    if (filtered.length === 0) {
        alert('No data to export');
        return;
    }
    
    // Calculate statistics
    const activeCount = filtered.filter(e => e.employment_status === 'Active').length;
    const inactiveCount = filtered.filter(e => e.employment_status !== 'Active').length;
    const deptGroups = {};
    filtered.forEach(emp => {
        deptGroups[emp.department] = (deptGroups[emp.department] || 0) + 1;
    });
    
    const reportDate = new Date();
    let csv = '';
    
    // Header Section
    csv += 'EMPLOYEE REPORT\n';
    csv += `Generated: ${reportDate.toLocaleString('en-US')}\n`;
    csv += '\n';
    
    // Report Summary Section
    csv += 'REPORT SUMMARY\n';
    csv += `Total Employees: ${filtered.length}\n`;
    csv += `Active Employees: ${activeCount}\n`;
    csv += `Inactive Employees: ${inactiveCount}\n`;
    if (deptFilter) csv += `Department Filter: ${deptFilter}\n`;
    if (statusFilter) csv += `Status Filter: ${statusFilter}\n`;
    csv += '\n';
    
    // Department Breakdown
    csv += 'DEPARTMENT BREAKDOWN\n';
    csv += 'Department,Count\n';
    Object.entries(deptGroups).forEach(([dept, count]) => {
        csv += `"${dept}","${count}"\n`;
    });
    csv += '\n';
    
    // Employee Data Section
    csv += 'EMPLOYEE DIRECTORY\n';
    csv += 'Employee ID,Full Name,Position,Department,Email,Contact Number,Hire Date,Years Employed,Employment Status\n';
    
    // Sort by department then name
    filtered.sort((a, b) => {
        const deptCompare = a.department.localeCompare(b.department);
        return deptCompare !== 0 ? deptCompare : a.full_name.localeCompare(b.full_name);
    });
    
    let lastDept = '';
    filtered.forEach(emp => {
        // Add department separator
        if (emp.department !== lastDept) {
            csv += `\n--- ${emp.department} ---\n`;
            lastDept = emp.department;
        }
        
        const hireDate = new Date(emp.date_hired).toLocaleDateString('en-US');
        const yearsEmployed = (parseFloat(emp.years_employed) || 0).toFixed(1);
        
        csv += `"${emp.employee_id}","${emp.full_name}","${emp.position || 'N/A'}","${emp.department || 'N/A'}","${emp.email || 'N/A'}","${emp.contact_number || 'N/A'}","${hireDate}","${yearsEmployed}","${emp.employment_status}"\n`;
    });
    
    // Download CSV
    const filename = `Employee_Report_${reportDate.toISOString().split('T')[0]}.csv`;
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function clearReportFilters() {
    document.getElementById('reportDeptFilter').value = '';
    document.getElementById('reportStatusFilter').value = '';
    document.getElementById('reportSearchFilter').value = '';
    generateReport();
}
</script>
