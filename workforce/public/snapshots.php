<!-- TAB: REPORT HISTORY & SNAPSHOTS -->
<div class="wfa-container" id="snapshotContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Report History...
    </div>
</div>

<script>
let snapshotHistory = [];

async function loadSnapshotsTab() {
    console.log('loadSnapshotsTab() called');
    const container = document.getElementById('snapshotContainer');
    
    if (!container) {
        console.error('Snapshot container not found');
        return;
    }
    
    try {
        const basePath = '/capstone_hr_management_system';
        console.log('Fetching snapshot history...');
        
        const response = await fetch(`${basePath}/api/wfa/report_snapshots.php?action=list&limit=20`);
        if (!response.ok) throw new Error(`API Error: ${response.status}`);
        
        const data = await response.json();
        console.log('Snapshot data:', data);
        
        snapshotHistory = data.data || [];
        
        let html = `
            <div class="wfa-snapshots-header">
                <h2 style="margin: 0 0 10px 0; color: #333;">📋 Report History & Snapshots</h2>
                <p style="margin: 0; color: #666; font-size: 14px;">Track and compare historical analytics reports</p>
            </div>
            
            <!-- Snapshot Actions -->
            <div class="wfa-snapshot-actions" style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <label for="snapshotType" style="font-weight: 600; margin-right: 10px;">Filter by Type:</label>
                <select id="snapshotType" class="wfa-filter-select" onchange="filterSnapshots()">
                    <option value="">All Types</option>
                    <option value="dashboard">Dashboard</option>
                    <option value="attrition">Attrition</option>
                    <option value="diversity">Diversity</option>
                    <option value="performance">Performance</option>
                </select>
                
                <button class="wfa-btn wfa-btn-success" onclick="createCurrentSnapshot()" style="margin-left: 15px;">
                    <i class="fas fa-camera"></i> Save Current Snapshot
                </button>
            </div>
            
            <!-- Snapshots Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">📸 Saved Snapshots</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Report Name</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Total Employees</th>
                            <th>Attrition Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        if (snapshotHistory.length === 0) {
            html += '<tr><td colspan="6" style="text-align: center; padding: 30px; color: #999;">No snapshots saved yet. Create one to get started!</td></tr>';
        } else {
            snapshotHistory.forEach(snapshot => {
                const date = new Date(snapshot.snapshot_date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                html += `
                    <tr>
                        <td><strong>${snapshot.snapshot_name}</strong></td>
                        <td><span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">${snapshot.snapshot_type.toUpperCase()}</span></td>
                        <td>${date}</td>
                        <td>${snapshot.total_employees}</td>
                        <td><strong>${snapshot.attrition_rate}%</strong></td>
                        <td>
                            <button class="wfa-btn wfa-btn-sm" style="background: #17a2b8; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; font-size: 12px;" onclick="viewSnapshot(${snapshot.snapshot_id})">View</button>
                            <button class="wfa-btn wfa-btn-sm" style="background: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; font-size: 12px; margin-left: 5px;" onclick="deleteSnapshot(${snapshot.snapshot_id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
        }
        
        html += `
                    </tbody>
                </table>
            </div>
            
            <!-- Compare Snapshots by Month -->
            <div class="wfa-table-container" style="margin-top: 35px;">
                <h3 style="margin-bottom: 15px;">📊 Compare Monthly Trends</h3>
                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Month 1:</label>
                        <select id="compareMonth1" class="wfa-filter-select">
                            <option value="">Select a month...</option>
        `;
        
        // Extract unique months from snapshots
        const monthsMap = new Map();
        snapshotHistory.forEach(snapshot => {
            const datePart = snapshot.snapshot_date.split(' ')[0];
            const [year, month] = datePart.split('-');
            const monthLabel = new Date(`${year}-${month}-01T00:00:00`).toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
            const monthKey = `${year}-${month}`;
            if (!monthsMap.has(monthKey)) {
                monthsMap.set(monthKey, monthLabel);
            }
        });
        
        const months = Array.from(monthsMap.entries()).sort((a, b) => new Date(`${b[0]}-01`) - new Date(`${a[0]}-01`));
        months.forEach(([monthKey, monthLabel]) => {
            html += `<option value="${monthKey}">${monthLabel}</option>`;
        });
        
        html += `
                        </select>
                    </div>
                    
                    <div style="flex: 1;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Month 2:</label>
                        <select id="compareMonth2" class="wfa-filter-select">
                            <option value="">Select a month...</option>
        `;
        
        months.forEach(([monthKey, monthLabel]) => {
            html += `<option value="${monthKey}">${monthLabel}</option>`;
        });
        
        html += `
                        </select>
                    </div>
                    
                    <button class="wfa-btn wfa-btn-info" onclick="compareMonthlySnapshots()" style="align-self: flex-end;">
                        <i class="fas fa-exchange-alt"></i> Compare
                    </button>
                </div>
                
                <div id="comparisonResults" style="display: none; background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;">
                    <!-- Comparison results will be displayed here -->
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        
    } catch (error) {
        console.error('Error loading snapshots:', error);
        container.innerHTML = `
            <div style="padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
                <h4>Error Loading Snapshots</h4>
                <p>${error.message}</p>
            </div>
        `;
    }
}

async function createCurrentSnapshot() {
    const name = prompt('Enter snapshot name:', 'Dashboard ' + new Date().toLocaleDateString());
    if (!name) return;
    
    const basePath = '/capstone_hr_management_system';
    
    try {
        // First fetch current analytics data
        const response = await fetch(`${basePath}/api/wfa/insights_analytics.php`);
        if (!response.ok) throw new Error('Failed to fetch current data');
        
        const analyticsData = await response.json();
        const kpis = analyticsData.data.kpis;
        
        // Save snapshot
        const saveResponse = await fetch(`${basePath}/api/wfa/report_snapshots.php?action=save`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: 'dashboard',
                name: name,
                total_employees: kpis.total_employees,
                active_employees: kpis.active_employees,
                inactive_employees: kpis.inactive_employees,
                attrition_rate: kpis.attrition_rate,
                average_tenure: kpis.average_tenure,
                department_count: kpis.departments,
                position_count: kpis.departments,
                snapshot_data: analyticsData.data,
                created_by: 'Admin',
                notes: 'Auto-saved snapshot'
            })
        });
        
        const saveData = await saveResponse.json();
        if (saveData.success) {
            alert('Snapshot saved successfully!');
            loadSnapshotsTab(); // Reload the list
        }
    } catch (error) {
        alert('Error saving snapshot: ' + error.message);
    }
}

async function viewSnapshot(id) {
    const basePath = '/capstone_hr_management_system';
    
    try {
        const response = await fetch(`${basePath}/api/wfa/report_snapshots.php?action=get&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const snapshot = data.data;
            displaySnapshotModal(snapshot);
        }
    } catch (error) {
        alert('Error viewing snapshot: ' + error.message);
    }
}

function displaySnapshotModal(snapshot) {
    const date = new Date(snapshot.snapshot_date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const modalHtml = `
        <div class="snapshot-modal-overlay" id="snapshotModalOverlay" onclick="closeSnapshotModal()">
            <div class="snapshot-modal" onclick="event.stopPropagation()">
                <div class="snapshot-modal-header">
                    <h2>${snapshot.snapshot_name}</h2>
                    <button class="snapshot-modal-close" onclick="closeSnapshotModal()">&times;</button>
                </div>
                
                <div class="snapshot-modal-body">
                    <div class="snapshot-info-bar" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 25px;">
                        <div style="padding: 12px; background: #f0f7ff; border-left: 4px solid #007bff; border-radius: 4px;">
                            <p style="margin: 0; font-size: 12px; color: #666; font-weight: 600;">DATE</p>
                            <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: bold; color: #333;">${date}</p>
                        </div>
                        <div style="padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                            <p style="margin: 0; font-size: 12px; color: #666; font-weight: 600;">SNAPSHOT TYPE</p>
                            <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: bold; color: #333;">${snapshot.snapshot_type.toUpperCase()}</p>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
                        <!-- Employee Overview Chart -->
                        <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #e0e0e0;">
                            <h4 style="margin: 0 0 15px 0; color: #333;">Employee Status</h4>
                            <canvas id="employeeChart" height="200"></canvas>
                        </div>
                        
                        <!-- Key Metrics -->
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px;">
                                <p style="margin: 0; font-size: 12px; opacity: 0.9; font-weight: 600;">TOTAL EMPLOYEES</p>
                                <p style="margin: 8px 0 0 0; font-size: 28px; font-weight: bold;">${snapshot.total_employees}</p>
                            </div>
                            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 15px; border-radius: 8px;">
                                <p style="margin: 0; font-size: 12px; opacity: 0.9; font-weight: 600;">ATTRITION RATE</p>
                                <p style="margin: 8px 0 0 0; font-size: 28px; font-weight: bold;">${snapshot.attrition_rate}%</p>
                            </div>
                            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 15px; border-radius: 8px;">
                                <p style="margin: 0; font-size: 12px; opacity: 0.9; font-weight: 600;">AVG TENURE</p>
                                <p style="margin: 8px 0 0 0; font-size: 28px; font-weight: bold;">${snapshot.average_tenure}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Metrics Table -->
                    <div style="background: white; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: #f8f9fa; border-bottom: 2px solid #e0e0e0;">
                                <tr>
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Metric</th>
                                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #333;">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom: 1px solid #e0e0e0;">
                                    <td style="padding: 12px; color: #666;">Active Employees</td>
                                    <td style="padding: 12px; text-align: right; font-weight: bold; color: #28a745;">${snapshot.active_employees || 0}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e0e0e0;">
                                    <td style="padding: 12px; color: #666;">Inactive Employees</td>
                                    <td style="padding: 12px; text-align: right; font-weight: bold; color: #dc3545;">${snapshot.inactive_employees || 0}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e0e0e0;">
                                    <td style="padding: 12px; color: #666;">Departments</td>
                                    <td style="padding: 12px; text-align: right; font-weight: bold; color: #007bff;">${snapshot.department_count || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; color: #666;">Positions</td>
                                    <td style="padding: 12px; text-align: right; font-weight: bold; color: #17a2b8;">${snapshot.position_count || 'N/A'}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="snapshot-modal-footer">
                    <button class="wfa-btn wfa-btn-secondary" onclick="closeSnapshotModal()" style="width: 100%;">Close</button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('snapshotModalOverlay');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Add CSS for modal if not already added
    if (!document.getElementById('snapshotModalStyles')) {
        const style = document.createElement('style');
        style.id = 'snapshotModalStyles';
        style.textContent = `
            .snapshot-modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                animation: fadeIn 0.3s ease;
            }
            
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }
                to {
                    opacity: 1;
                }
            }
            
            .snapshot-modal {
                background: white;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                max-width: 900px;
                width: 90vw;
                max-height: 90vh;
                overflow-y: auto;
                animation: slideUp 0.3s ease;
            }
            
            @keyframes slideUp {
                from {
                    transform: translateY(50px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
            
            .snapshot-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 25px;
                border-bottom: 2px solid #f0f0f0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            
            .snapshot-modal-header h2 {
                margin: 0;
                font-size: 24px;
            }
            
            .snapshot-modal-close {
                background: none;
                border: none;
                font-size: 28px;
                cursor: pointer;
                color: white;
                opacity: 0.8;
                transition: opacity 0.2s;
            }
            
            .snapshot-modal-close:hover {
                opacity: 1;
            }
            
            .snapshot-modal-body {
                padding: 25px;
            }
            
            .snapshot-modal-footer {
                padding: 15px 25px;
                border-top: 1px solid #f0f0f0;
                background: #f8f9fa;
                display: flex;
                gap: 10px;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Initialize Chart.js chart
    setTimeout(() => {
        initializeEmployeeChart(snapshot);
    }, 100);
}

function initializeEmployeeChart(snapshot) {
    const ctx = document.getElementById('employeeChart');
    if (!ctx) return;
    
    const activeEmployees = snapshot.active_employees || 0;
    const inactiveEmployees = snapshot.inactive_employees || 0;
    
    // Check if Chart.js is loaded
    if (typeof Chart !== 'undefined') {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [activeEmployees, inactiveEmployees],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderColor: ['#218838', '#c82333'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

function closeSnapshotModal() {
    const modal = document.getElementById('snapshotModalOverlay');
    if (modal) {
        modal.remove();
    }
}

async function deleteSnapshot(id) {
    if (!confirm('Are you sure you want to delete this snapshot?')) return;
    
    const basePath = '/capstone_hr_management_system';
    
    try {
        const response = await fetch(`${basePath}/api/wfa/report_snapshots.php?action=delete&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            alert('Snapshot deleted successfully!');
            loadSnapshotsTab(); // Reload the list
        }
    } catch (error) {
        alert('Error deleting snapshot: ' + error.message);
    }
}

function compareMonthlySnapshots() {
    const month1 = document.getElementById('compareMonth1').value;
    const month2 = document.getElementById('compareMonth2').value;
    
    if (!month1 || !month2) {
        alert('Please select both months');
        return;
    }
    
    // Calculate aggregated metrics for each month
    const monthlyData1 = calculateMonthlyAggregates(month1);
    const monthlyData2 = calculateMonthlyAggregates(month2);
    
    if (!monthlyData1.values.length || !monthlyData2.values.length) {
        alert('No snapshots found for one or both selected months');
        return;
    }
    
    // Calculate averages
    const avgMonth1 = {
        employees: (monthlyData1.values.reduce((sum, m) => sum + m.total_employees, 0) / monthlyData1.values.length).toFixed(0),
        active: (monthlyData1.values.reduce((sum, m) => sum + m.active_employees, 0) / monthlyData1.values.length).toFixed(0),
        attrition: (monthlyData1.values.reduce((sum, m) => sum + parseFloat(m.attrition_rate), 0) / monthlyData1.values.length).toFixed(2),
        tenure: (monthlyData1.values.reduce((sum, m) => sum + parseFloat(m.average_tenure), 0) / monthlyData1.values.length).toFixed(2)
    };
    
    const avgMonth2 = {
        employees: (monthlyData2.values.reduce((sum, m) => sum + m.total_employees, 0) / monthlyData2.values.length).toFixed(0),
        active: (monthlyData2.values.reduce((sum, m) => sum + m.active_employees, 0) / monthlyData2.values.length).toFixed(0),
        attrition: (monthlyData2.values.reduce((sum, m) => sum + parseFloat(m.attrition_rate), 0) / monthlyData2.values.length).toFixed(2),
        tenure: (monthlyData2.values.reduce((sum, m) => sum + parseFloat(m.average_tenure), 0) / monthlyData2.values.length).toFixed(2)
    };
    
    // Calculate changes
    const employeeChange = avgMonth2.employees - avgMonth1.employees;
    const activeChange = avgMonth2.active - avgMonth1.active;
    const attritionChange = (avgMonth2.attrition - avgMonth1.attrition).toFixed(2);
    const tenureChange = (avgMonth2.tenure - avgMonth1.tenure).toFixed(2);
    
    const month1Label = new Date(month1).toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
    const month2Label = new Date(month2).toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
    
    const resultsHtml = `
        <h4>Monthly Comparison Results</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div style="background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #007bff;">
                <h5 style="margin: 0 0 10px 0; color: #333;">${month1Label}</h5>
                <p style="margin: 5px 0;"><strong>Avg Employees:</strong> ${avgMonth1.employees}</p>
                <p style="margin: 5px 0;"><strong>Avg Active:</strong> ${avgMonth1.active}</p>
                <p style="margin: 5px 0;"><strong>Avg Attrition:</strong> ${avgMonth1.attrition}%</p>
                <p style="margin: 5px 0;"><strong>Avg Tenure:</strong> ${avgMonth1.tenure} yrs</p>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;">
                <h5 style="margin: 0 0 10px 0; color: #333;">${month2Label}</h5>
                <p style="margin: 5px 0;"><strong>Avg Employees:</strong> ${avgMonth2.employees}</p>
                <p style="margin: 5px 0;"><strong>Avg Active:</strong> ${avgMonth2.active}</p>
                <p style="margin: 5px 0;"><strong>Avg Attrition:</strong> ${avgMonth2.attrition}%</p>
                <p style="margin: 5px 0;"><strong>Avg Tenure:</strong> ${avgMonth2.tenure} yrs</p>
            </div>
        </div>
        <table class="wfa-table" style="width: 100%; margin-bottom: 30px;">
            <thead>
                <tr>
                    <th><strong>Metric</strong></th>
                    <th style="text-align: right;"><strong>${month1Label}</strong></th>
                    <th style="text-align: right;"><strong>${month2Label}</strong></th>
                    <th style="text-align: center;"><strong>Change</strong></th>
                    <th style="text-align: center;"><strong>Trend</strong></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Employees</td>
                    <td style="text-align: right;">${avgMonth1.employees}</td>
                    <td style="text-align: right;">${avgMonth2.employees}</td>
                    <td style="text-align: center;"><strong>${employeeChange > 0 ? '+' : ''}${employeeChange}</strong></td>
                    <td style="text-align: center;">${employeeChange > 0 ? '📈' : (employeeChange < 0 ? '📉' : '➡️')}</td>
                </tr>
                <tr>
                    <td>Active Employees</td>
                    <td style="text-align: right;">${avgMonth1.active}</td>
                    <td style="text-align: right;">${avgMonth2.active}</td>
                    <td style="text-align: center;"><strong>${activeChange > 0 ? '+' : ''}${activeChange}</strong></td>
                    <td style="text-align: center;">${activeChange > 0 ? '📈' : (activeChange < 0 ? '📉' : '➡️')}</td>
                </tr>
                <tr style="background: ${attritionChange > 0 ? '#fff3cd' : '#d4edda'};">
                    <td>Attrition Rate (%)</td>
                    <td style="text-align: right;">${avgMonth1.attrition}%</td>
                    <td style="text-align: right;">${avgMonth2.attrition}%</td>
                    <td style="text-align: center;"><strong>${attritionChange > 0 ? '+' : ''}${attritionChange}%</strong></td>
                    <td style="text-align: center;">${attritionChange > 0 ? '📈' : (attritionChange < 0 ? '📉' : '➡️')}</td>
                </tr>
                <tr>
                    <td>Average Tenure (years)</td>
                    <td style="text-align: right;">${avgMonth1.tenure}</td>
                    <td style="text-align: right;">${avgMonth2.tenure}</td>
                    <td style="text-align: center;"><strong>${tenureChange > 0 ? '+' : ''}${tenureChange}</strong></td>
                    <td style="text-align: center;">${tenureChange > 0 ? '📈' : (tenureChange < 0 ? '📉' : '➡️')}</td>
                </tr>
            </tbody>
        </table>
        
        <h4 style="margin-top: 30px;">📊 Monthly Trend Analysis</h4>
        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; position: relative; height: 400px;">
            <canvas id="monthlyTrendChart" style="width: 100% !important; height: 100% !important;"></canvas>
        </div>
    `;
    
    document.getElementById('comparisonResults').innerHTML = resultsHtml;
    document.getElementById('comparisonResults').style.display = 'block';
    
    // Initialize the trend chart - increased timeout to ensure DOM is ready
    setTimeout(() => {
        initializeMonthlyTrendChart(month1Label, month2Label, avgMonth1, avgMonth2);
    }, 250);
}

function initializeMonthlyTrendChart(month1Label, month2Label, avgMonth1, avgMonth2) {
    console.log('Attempting to initialize monthly trend chart...');
    
    const ctx = document.getElementById('monthlyTrendChart');
    
    if (!ctx) {
        console.error('Canvas element monthlyTrendChart not found');
        return;
    }
    
    if (typeof Chart === 'undefined') {
        console.error('Chart.js library is not loaded');
        return;
    }
    
    // Get 2D context
    const chartCtx = ctx.getContext('2d');
    if (!chartCtx) {
        console.error('Failed to get 2D context from canvas');
        return;
    }
    
    try {
        // Store reference to destroy previous chart if exists
        if (ctx.chart) {
            ctx.chart.destroy();
        }
        
        // Create new chart
        ctx.chart = new Chart(chartCtx, {
            type: 'line',
            data: {
                labels: [month1Label, month2Label],
                datasets: [
                    {
                        label: 'Total Employees',
                        data: [parseInt(avgMonth1.employees), parseInt(avgMonth2.employees)],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 8,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 10
                    },
                    {
                        label: 'Active Employees',
                        data: [parseInt(avgMonth1.active), parseInt(avgMonth2.active)],
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 8,
                        pointBackgroundColor: '#28a745',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 10
                    },
                    {
                        label: 'Attrition Rate (%)',
                        data: [parseFloat(avgMonth1.attrition), parseFloat(avgMonth2.attrition)],
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 8,
                        pointBackgroundColor: '#dc3545',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 10
                    },
                    {
                        label: 'Avg Tenure (years)',
                        data: [parseFloat(avgMonth1.tenure), parseFloat(avgMonth2.tenure)],
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 8,
                        pointBackgroundColor: '#17a2b8',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 10
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 13,
                                weight: 'bold'
                            },
                            padding: 20,
                            usePointStyle: true,
                            boxWidth: 8,
                            boxHeight: 8
                        }
                    },
                    filler: {
                        propagate: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: true
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            padding: 10
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: true
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            padding: 10
                        }
                    }
                }
            }
        });
        
        console.log('Monthly trend chart initialized successfully');
    } catch (error) {
        console.error('Error initializing chart:', error);
    }
}

function calculateMonthlyAggregates(monthKey) {
    const snapshots = snapshotHistory.filter(snapshot => {
        const datePart = snapshot.snapshot_date.split(' ')[0];
        const snapshotMonth = datePart.substring(0, 7);
        return snapshotMonth === monthKey;
    });
    
    return {
        month: monthKey,
        values: snapshots
    };
}

function filterSnapshots() {
    // Implementation for filtering by type
    loadSnapshotsTab();
}
</script>
