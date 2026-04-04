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
            
            <!-- Compare Snapshots -->
            <div class="wfa-table-container" style="margin-top: 35px;">
                <h3 style="margin-bottom: 15px;">🔄 Compare Snapshots</h3>
                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Snapshot 1:</label>
                        <select id="compareSnapshot1" class="wfa-filter-select">
                            <option value="">Select a snapshot...</option>
        `;
        
        snapshotHistory.forEach(snapshot => {
            const date = new Date(snapshot.snapshot_date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            html += `<option value="${snapshot.snapshot_id}">${snapshot.snapshot_name} (${date})</option>`;
        });
        
        html += `
                        </select>
                    </div>
                    
                    <div style="flex: 1;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Snapshot 2:</label>
                        <select id="compareSnapshot2" class="wfa-filter-select">
                            <option value="">Select a snapshot...</option>
        `;
        
        snapshotHistory.forEach(snapshot => {
            const date = new Date(snapshot.snapshot_date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            html += `<option value="${snapshot.snapshot_id}">${snapshot.snapshot_name} (${date})</option>`;
        });
        
        html += `
                        </select>
                    </div>
                    
                    <button class="wfa-btn wfa-btn-info" onclick="compareSnapshots()" style="align-self: flex-end;">
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
            alert(`Snapshot: ${snapshot.snapshot_name}\nDate: ${snapshot.snapshot_date}\nTotal Employees: ${snapshot.total_employees}\nAttrition Rate: ${snapshot.attrition_rate}%`);
        }
    } catch (error) {
        alert('Error viewing snapshot: ' + error.message);
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

async function compareSnapshots() {
    const id1 = document.getElementById('compareSnapshot1').value;
    const id2 = document.getElementById('compareSnapshot2').value;
    
    if (!id1 || !id2) {
        alert('Please select both snapshots');
        return;
    }
    
    const basePath = '/capstone_hr_management_system';
    
    try {
        const response = await fetch(`${basePath}/api/wfa/report_snapshots.php?action=compare&id1=${id1}&id2=${id2}`);
        const data = await response.json();
        
        if (data.success) {
            const comp = data.data;
            const changes = comp.changes;
            
            const resultsHtml = `
                <h4>Comparison Results</h4>
                <table class="wfa-table" style="width: 100%;">
                    <tr>
                        <td><strong>Metric</strong></td>
                        <td style="text-align: center;"><strong>Change</strong></td>
                        <td style="text-align: center;"><strong>Trend</strong></td>
                    </tr>
                    <tr>
                        <td>Total Employees</td>
                        <td style="text-align: center;"><strong>${changes.employee_change > 0 ? '+' : ''}${changes.employee_change}</strong></td>
                        <td style="text-align: center;">${changes.employee_change > 0 ? '📈' : (changes.employee_change < 0 ? '📉' : '➡️')}</td>
                    </tr>
                    <tr>
                        <td>Active Employees</td>
                        <td style="text-align: center;"><strong>${changes.active_change > 0 ? '+' : ''}${changes.active_change}</strong></td>
                        <td style="text-align: center;">${changes.active_change > 0 ? '📈' : (changes.active_change < 0 ? '📉' : '➡️')}</td>
                    </tr>
                    <tr>
                        <td>Attrition Rate (%)</td>
                        <td style="text-align: center;"><strong>${changes.attrition_change > 0 ? '+' : ''}${changes.attrition_change}%</strong></td>
                        <td style="text-align: center;">${changes.attrition_change > 0 ? '📈' : (changes.attrition_change < 0 ? '📉' : '➡️')}</td>
                    </tr>
                    <tr>
                        <td>Average Tenure (years)</td>
                        <td style="text-align: center;"><strong>${changes.tenure_change > 0 ? '+' : ''}${changes.tenure_change}</strong></td>
                        <td style="text-align: center;">${changes.tenure_change > 0 ? '📈' : (changes.tenure_change < 0 ? '📉' : '➡️')}</td>
                    </tr>
                </table>
            `;
            
            document.getElementById('comparisonResults').innerHTML = resultsHtml;
            document.getElementById('comparisonResults').style.display = 'block';
        }
    } catch (error) {
        alert('Error comparing snapshots: ' + error.message);
    }
}

function filterSnapshots() {
    // Implementation for filtering by type
    loadSnapshotsTab();
}
</script>
