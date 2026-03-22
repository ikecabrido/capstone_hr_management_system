// ============================================
// GRIEVANCE STATUS COLOR CODING
// Provides color coding for grievance status
// ============================================

function getStatusColor(status) {
    switch((status || '').toLowerCase()) {
        case 'open': return '#ec1c1c';
        case 'in-progress': return '#f76b1c';
        case 'resolved': return '#2e7d32';
        case 'closed': return '#5e5e5e';
        default: return '#666';
    }
}

function injectGrievancesStyles() {
    const style = document.createElement('style');
    style.innerHTML = `
    #grievances-container { width: 100% !important; overflow-x: auto !important; padding: 10px !important; }
    .grievances-table { width: 100% !important; overflow-x: auto !important; background: #fff !important; border: 1px solid #ddd !important; border-radius: 8px !important; }
    .grievances-table table { width: 100% !important; border-collapse: collapse !important; table-layout: fixed !important; min-width: 880px !important; }
    .grievances-table th, .grievances-table td { padding: 10px 12px !important; border: 1px solid #e2e8f0 !important; text-align: left !important; word-break: break-word !important; white-space: normal !important; }
    .grievances-table th { background: #f7fafc !important; color: #2d3748 !important; position: sticky !important; top: 0 !important; z-index: 2 !important; }
    .grievances-table tbody tr:nth-child(odd) { background: #fbfcfe !important; }
    .grievances-table tbody tr:hover { background: #edf2f7 !important; }
    `;
    document.head.appendChild(style);
}

document.addEventListener('DOMContentLoaded', function () {
    injectGrievancesStyles();
    const container = document.getElementById('grievances-container');
    if (!container) return;

    container.innerHTML = '<div style="padding:20px; color:#666;">Loading grievances...</div>';

    const apiUrls = ['api/grievances.php', 'api/grievances_data.php'];

    const loadData = (index = 0) => {
        if (index >= apiUrls.length) {
            container.innerHTML = '<div style="padding:20px; color:#a00;">Error loading grievances: API not available.</div>';
            return;
        }

        fetch(apiUrls[index], {
            cache: 'no-store',
            credentials: 'same-origin',
        })
        .then((res) => {
            if (!res.ok) {
                throw new Error(`HTTP ${res.status} ${res.statusText}`);
            }
            return res.json();
        })
        .then((data) => {
            console.debug('grievances fetch:', apiUrls[index], data);
            if (!data || typeof data !== 'object') {
                throw new Error('Invalid JSON response from ' + apiUrls[index]);
            }
            if (data.error) {
                if (index + 1 < apiUrls.length) {
                    return loadData(index + 1);
                }
                throw new Error(data.error);
            }

            const grievances = data.grievances || data.data || [];

            if (!Array.isArray(grievances) || grievances.length === 0) {
                container.innerHTML = '<div style="padding:20px; color:#666;">No grievances found.</div>';
                return;
            }

        const rows = grievances.map((item) => {
            const status = item.status || 'unknown';
            return `
            <tr>
                <td>${item.id || '-'}</td>
                <td>${item.employee_id || '-'}</td>
                <td>${item.subject || '-'}</td>
                <td>${item.priority || '-'}</td>
                <td><span style="color:${getStatusColor(status)}; font-weight:bold;">${status}</span></td>
                <td>${item.assigned_to || '-'}</td>
                <td>${item.created_at || '-'}</td>
                <td>${item.updated_at || '-'}</td>
            </tr>
            `;
        }).join('');

        container.innerHTML = `
            <div class="grievances-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee ID</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Date Created</th>
                            <th>Date Updated</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
        `;
    })
    .catch((err) => {
        console.error(err);
        if (index + 1 < apiUrls.length) {
            loadData(index + 1);
            return;
        }
        container.innerHTML = `<div style="padding:20px; color:#a00;">Error loading grievances: ${err.message}</div>`;
    });
    };

    loadData();
});
