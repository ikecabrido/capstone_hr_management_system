(function() {
  const apiBase = '../api/index.php';
  const refreshIntervalMs = 30000;

  function fetchJson(url) {
    return fetch(url, { credentials: 'same-origin' })
      .then(response => {
        if (!response.ok) {
          throw new Error('API request failed: ' + response.status);
        }
        return response.json();
      });
  }

  function parseApiData(response) {
    if (Array.isArray(response)) {
      return response;
    }
    if (response && Array.isArray(response.data)) {
      return response.data;
    }
    return [];
  }

  function safeText(value) {
    const text = value == null ? '' : String(value);
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function priorityBadgeClass(priority) {
    const normalized = String(priority || '').toLowerCase().trim();
    if (normalized === 'high' || normalized === 'urgent') {
      return 'danger';
    }
    if (normalized === 'normal') {
      return 'secondary';
    }
    return 'info';
  }

  function statusBadgeClass(status) {
    const normalized = String(status || '').toLowerCase().trim();
    if (normalized === 'resolved' || normalized === 'closed') {
      return 'success';
    }
    if (normalized === 'under review' || normalized === 'underreview') {
      return 'info';
    }
    if (normalized === 'escalated') {
      return 'danger';
    }
    return 'warning';
  }

  function formatMoney(value) {
    if (value == null || value === '') {
      return '—';
    }
    const amount = Number(value);
    if (Number.isNaN(amount)) {
      return '—';
    }
    return '₱' + amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  function renderAnnouncements(announcements) {
    const container = document.getElementById('announcements-container');
    if (!container) {
      return;
    }

    if (!Array.isArray(announcements) || !announcements.length) {
      container.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-bullhorn fa-3x mb-3"></i><h5>No announcements yet</h5><p>Company announcements will appear here.</p></div>';
      return;
    }

    container.innerHTML = announcements.slice(0, 5).map(function(announcement) {
      const priority = safeText(announcement.priority || 'normal');
      const category = safeText(announcement.category || 'general');
      const author = safeText(announcement.author_name || announcement.created_by || 'Admin');
      const title = safeText(announcement.title || 'Untitled announcement');
      const content = safeText(String(announcement.content || '')).replace(/\n/g, '<br>');
      const dateString = announcement.created_at ? safeText(announcement.created_at) : 'Unknown date';
      return '\n        <div class="announcement-card card mb-3">\n          <div class="card-body">\n            <div class="d-flex justify-content-between align-items-start mb-2">\n              <h6 class="card-title text-primary mb-1">' + title + '</h6>\n              <span class="badge badge-' + priorityBadgeClass(priority) + '">' + safeText(priority.charAt(0).toUpperCase() + priority.slice(1)) + '</span>\n            </div>\n            <p class="card-text text-muted small mb-2">\n              <i class="fas fa-calendar"></i> ' + dateString + ' |\n              <i class="fas fa-tag"></i> ' + category + ' |\n              <i class="fas fa-user"></i> ' + author + '\n            </p>\n            <p class="card-text">' + (content.length > 0 ? content : '<em>No content</em>') + '</p>\n            <button class="btn btn-sm btn-outline-primary" onclick="viewFullAnnouncement(' + Number(announcement.eer_announcements_id || announcement.id || 0) + ')">\n              <i class="fas fa-eye"></i> Read More\n            </button>\n          </div>\n        </div>';
    }).join('');
  }

  function renderMessages(messages) {
    const container = document.getElementById('messages-container');
    if (!container) {
      return;
    }

    if (!Array.isArray(messages) || !messages.length) {
      container.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-comments fa-3x mb-3"></i><h5>No messages yet</h5><p>Your conversations with HR will appear here.</p></div>';
      return;
    }

    const currentEmployeeId = window.currentEmployeeId ? Number(window.currentEmployeeId) : null;

    container.innerHTML = messages.map(function(message) {
      const senderId = Number(message.sender_id || 0);
      const senderName = safeText(message.sender_name || message.sender || 'Unknown');
      const time = safeText(message.timestamp || message.created_at || '');
      const content = safeText(String(message.message || ''));
      const isSent = currentEmployeeId && senderId === currentEmployeeId;
      return '\n        <div class="message-bubble ' + (isSent ? 'sent' : 'received') + '">\n          <div class="p-2">\n            <div class="d-flex justify-content-between align-items-center mb-1">\n              <small class="text-muted"><i class="fas fa-user"></i> ' + senderName + '</small>\n              <small class="text-muted"><i class="fas fa-clock"></i> ' + time + '</small>\n            </div>\n            <p class="mb-0">' + (content || '<em>(No message content)</em>') + '</p>\n          </div>\n        </div>';
    }).join('');
  }

  function renderGrievanceRows(grievances) {
    const tbody = document.querySelector('#all-grievances-table tbody');
    if (!tbody) {
      return;
    }

    const rowsHtml = grievances.map(function(grievance) {
      const id = Number(grievance.id || grievance.eer_grievance_id || 0);
      const employeeName = safeText(grievance.employee_name || 'Unknown');
      const subject = safeText(grievance.subject || '');
      const category = safeText(grievance.category || 'N/A');
      const status = safeText(grievance.status || 'Pending');
      const priority = safeText(grievance.priority || 'Medium');
      const dateSubmitted = safeText(grievance.created_at ? (new Date(grievance.created_at)).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A');
      const payslipId = grievance.payslip_id ? Number(grievance.payslip_id) : null;
      let payslipLabel = 'None';
      if (payslipId) {
        payslipLabel = 'Payslip ' + payslipId;
        if (grievance.gross_pay || grievance.net_pay) {
          payslipLabel += ' | Gross ' + formatMoney(grievance.gross_pay) + ' | Net ' + formatMoney(grievance.net_pay);
        }
      }
      const employeeAdjustments = safeText(grievance.employee_adjustments || '');
      const adjustmentsCell = employeeAdjustments ? '<small title="' + employeeAdjustments + '">' + (employeeAdjustments.length > 50 ? safeText(employeeAdjustments).slice(0, 50) + '...' : employeeAdjustments) + '</small>' : '<span class="text-muted">None</span>';
      const payrollModule = safeText((grievance.payroll_module || '').toLowerCase());
      const searchText = safeText([subject, employeeName, category, payslipLabel, payrollModule, grievance.payroll_reference_id || ''].join(' '));

      return '\n        <tr class="grievance-row" data-id="' + id + '" data-status="' + safeText((grievance.status || '').toLowerCase()) + '" data-category="' + safeText(category) + '" data-priority="' + safeText((grievance.priority || '').toLowerCase()) + '" data-date="' + safeText(grievance.created_at ? grievance.created_at.split(' ')[0] : '') + '" data-payroll-module="' + payrollModule + '" data-search="' + safeText(searchText).toLowerCase() + '">\n          <td>' + id + '</td>\n          <td>' + employeeName + '</td>\n          <td>' + subject + '</td>\n          <td>' + category + '</td>\n          <td><span class="badge badge-' + statusBadgeClass(status) + '">' + status + '</span></td>\n          <td><span class="badge badge-' + (priority.toLowerCase() === 'high' ? 'danger' : 'secondary') + '">' + priority + '</span></td>\n          <td>' + dateSubmitted + '</td>\n          <td>' + safeText(payslipLabel) + '</td>\n          <td>' + adjustmentsCell + '</td>\n          <td><button class="btn btn-sm btn-info" title="View Grievance" onclick="viewGrievanceDetails(' + id + ')"><i class="fas fa-eye"></i></button></td>\n        </tr>';
    }).join('');

    tbody.innerHTML = rowsHtml || '<tr class="no-results-row"><td colspan="10" class="text-center text-muted">No grievance records found.</td></tr>';
    if (window.jQuery) {
      window.originalGrievanceRows = window.jQuery('#all-grievances-table tbody .grievance-row').toArray();
    }
    if (typeof window.filterGrievances === 'function') {
      window.filterGrievances();
    }
  }

  function renderDashboardCount(id, value) {
    const el = document.getElementById(id);
    if (!el) {
      return;
    }
    el.textContent = Number(value || 0);
  }

  function refreshAnnouncements() {
    return fetchJson(apiBase + '?resource=communication&action=announcements')
      .then(parseApiData)
      .then(renderAnnouncements)
      .catch(function(err) {
        console.warn('Unable to refresh announcements:', err);
      });
  }

  function refreshMessages() {
    return fetchJson(apiBase + '?resource=message&action=threads')
      .then(parseApiData)
      .then(renderMessages)
      .catch(function(err) {
        console.warn('Unable to refresh messages:', err);
      });
  }

  function refreshGrievances() {
    return fetchJson(apiBase + '?resource=grievance&action=list')
      .then(parseApiData)
      .then(renderGrievanceRows)
      .catch(function(err) {
        console.warn('Unable to refresh grievances:', err);
      });
  }

  function refreshDashboardCounts() {
    const tasks = [
      { id: 'count-announcements', url: apiBase + '?resource=communication&action=announcements' },
      { id: 'count-surveys', url: apiBase + '?resource=survey&action=list' },
      { id: 'count-feedback', url: apiBase + '?resource=feedback&action=list' },
      { id: 'count-recognitions', url: apiBase + '?resource=recognition&action=list' },
      { id: 'count-grievances', url: apiBase + '?resource=grievance&action=list' },
      { id: 'count-feed', url: apiBase + '?resource=social&action=feed' },
      { id: 'count-employees', url: apiBase + '?resource=employee_list' },
      { id: 'count-groups', url: apiBase + '?resource=group&action=list' },
      { id: 'count-notifications', url: apiBase + '?resource=communication&action=notifications' }
    ];

    tasks.forEach(function(task) {
      fetchJson(task.url)
        .then(function(result) {
          const data = parseApiData(result);
          renderDashboardCount(task.id, data.length);
        })
        .catch(function(err) {
          console.warn('Unable to refresh dashboard count for ' + task.id + ':', err);
        });
    });
  }

  function initLiveUpdates() {
    if (document.getElementById('announcements-container')) {
      refreshAnnouncements();
      setInterval(refreshAnnouncements, refreshIntervalMs);
    }

    if (document.getElementById('messages-container')) {
      refreshMessages();
      setInterval(refreshMessages, refreshIntervalMs);
    }

    if (document.getElementById('all-grievances-table')) {
      refreshGrievances();
      setInterval(refreshGrievances, refreshIntervalMs);
    }

    if (typeof window.fetchSocialFeed === 'function') {
      window.fetchSocialFeed();
      setInterval(window.fetchSocialFeed, refreshIntervalMs);
    }

    if (document.getElementById('count-announcements') || document.getElementById('count-surveys') || document.getElementById('count-grievances') || document.getElementById('count-feed') || document.getElementById('count-employees') || document.getElementById('count-groups') || document.getElementById('count-notifications')) {
      refreshDashboardCounts();
      setInterval(refreshDashboardCounts, refreshIntervalMs);
    }
  }

  window.LiveUpdates = {
    refreshAnnouncements,
    refreshMessages,
    refreshGrievances,
    refreshDashboardCounts,
    initLiveUpdates
  };

  window.addEventListener('load', function() {
    if (typeof window.LiveUpdates === 'object' && typeof window.LiveUpdates.initLiveUpdates === 'function') {
      window.LiveUpdates.initLiveUpdates();
    }
  });
})();
