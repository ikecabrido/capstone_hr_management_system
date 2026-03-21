document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('auditlogs-container');
  if (!container) return;

  container.innerHTML = '<div style="padding:20px; color:#666;">Loading audit logs...</div>';

  fetch('api/auditlogs.php', {
    cache: 'no-store',
    credentials: 'same-origin',
  })
    .then((res) => {
      if (!res.ok) {
        throw new Error(`Failed to fetch audit logs (${res.status})`);
      }
      return res.json();
    })
    .then((data) => {
      const logs = data.audit_logs || [];
      if (!Array.isArray(logs) || logs.length === 0) {
        container.innerHTML = '<div style="padding:20px; color:#666;">No audit logs found.</div>';
        return;
      }

      const rows = logs
        .map((log) => {
          const action = log.action || 'unknown';
          const targetType = log.target_type || 'n/a';
          const performedBy = log.performed_by || 'Unknown';
          const details = log.details || '-';
          const timestamp = log.performed_at || log.timestamp || log.created_at || '-';

          return `
            <tr>
              <td>${performedBy}</td>
              <td>${action}</td>
              <td>${targetType}</td>
              <td style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${details}</td>
              <td>${timestamp}</td>
            </tr>
          `;
        })
        .join('');

      container.innerHTML = `
        <div class="audit-logs-table">
          <table>
            <thead>
              <tr>
                <th>User</th>
                <th>Action</th>
                <th>Module</th>
                <th>Details</th>
                <th>Timestamp</th>
              </tr>
            </thead>
            <tbody>${rows}</tbody>
          </table>
        </div>
      `;
    })
    .catch((err) => {
      container.innerHTML = `<div style="padding:20px; color:#a00;">Error loading audit logs: ${err.message}</div>`;
      console.error(err);
    });
});