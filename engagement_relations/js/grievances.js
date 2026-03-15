document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('grievances-container');
  if (!container) return;

  const apiBase = `${window.location.origin}${window.location.pathname.replace(/\/[^\/]*$/, '')}/api`;

  container.innerHTML = '<div style="padding:20px; color:#666;">Loading grievances...</div>';

  fetch(`${apiBase}/grievances_data.php`, { cache: 'no-store', credentials: 'same-origin' })
    .then((res) => {
      return res.text().then((text) => {
        if (!res.ok) {
          throw new Error(`HTTP ${res.status} ${res.statusText}: ${text}`);
        }
        try {
          return JSON.parse(text);
        } catch (err) {
          throw new Error(`Invalid JSON from server: ${err.message} | ${text}`);
        }
      });
    })
    .then((data) => {
      if (!data.success || !Array.isArray(data.grievances)) {
        throw new Error(data.error || 'Invalid data format');
      }

      if (data.grievances.length === 0) {
        container.innerHTML = '<div style="padding:20px; color:#666;">No grievances found.</div>';
        return;
      }

      const statusColors = {
        open: '#ec1c1c',
        pending: '#ecb01c',
        under_investigation: '#1ca7ec',
        resolved: '#1cec7a',
        closed: '#888',
      };

      const cards = data.grievances.map((item) => {
        const status = (item.status || 'open').toLowerCase();
        const color = statusColors[status] || '#999';

        let actionsHTML = '';
        if (item.actions) {
          try {
            const actions = JSON.parse(item.actions);
            actionsHTML = actions
              .map((act) => `<div class="grievance-action"><strong>${new Date(act.action_date).toLocaleString()}</strong>: ${act.action_taken}</div>`)
              .join('');
          } catch (e) {
            actionsHTML = '';
          }
        }

        return `
          <div class="grievance-card">
            <div class="grievance-header">
              <div class="grievance-subject">${item.subject || 'No subject'}</div>
              <div class="grievance-status" style="background:${color};">${status}</div>
            </div>
            <div class="grievance-content">${item.description || ''}</div>
            <div class="grievance-meta">Priority: ${item.priority || 'normal'} | Assigned to: ${item.assigned_to || 'unassigned'} | Filed: ${item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A'}</div>
            ${actionsHTML ? `<div class="grievance-actions"><strong>Actions:</strong>${actionsHTML}</div>` : ''}
          </div>
        `;
      }).join('');

      container.innerHTML = cards;
    })
    .catch((err) => {
      container.innerHTML = `<div style="padding:20px; color:#a00;">Error loading grievances: ${err.message}</div>`;
      console.error(err);
    });
});
