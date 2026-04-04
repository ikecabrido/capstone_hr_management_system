document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.grievance-form');
  if (form) {
    form.addEventListener('submit', function() {
      const button = form.querySelector('button[type=submit]');
      if (button) { button.textContent = 'Submitting...'; button.disabled = true; }
    });
  }

  var grievancesContainer = document.getElementById('grievances-container');
  var employees = [];

  fetch('../api/employee_list.php')
    .then(function(response) { return response.json(); })
    .then(function(employeeData) {
      if (Array.isArray(employeeData)) {
        employees = employeeData;
      }
    })
    .catch(function() {
      if (grievancesContainer) {
        grievancesContainer.innerHTML = '<div class="alert alert-warning">Unable to load employee list.</div>';
      }
    })
    .finally(function() {
      fetch('../api/grievance.php?action=list')
        .then(function(response) { return response.json(); })
        .then(function(data) {
          if (data && data.success && Array.isArray(data.data)) {
            renderGrievances(data.data, data.grievance_updates || {}, employees);
          } else if (grievancesContainer) {
            grievancesContainer.innerHTML = '<div class="alert alert-info">No grievances found.</div>';
          }
        })
        .catch(function() {
          if (grievancesContainer) {
            grievancesContainer.innerHTML = '<div class="alert alert-danger">Error loading grievances.</div>';
          }
        });
    });
});

function escapeHtml(text) {
  if (text === null || text === undefined) return '';
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function statusBadgeClass(status) {
  var s = (status || '').toLowerCase();
  if (s === 'resolved') return 'success';
  if (s === 'for investigation') return 'warning';
  if (s === 'under review') return 'info';
  if (s === 'closed') return 'dark';
  return 'secondary';
}

function renderGrievances(grievances, updates, employees) {
  var container = document.getElementById('grievances-container');
  if (!container) return;
  if (!Array.isArray(grievances) || grievances.length === 0) {
    container.innerHTML = '<p class="text-muted">No grievances submitted yet.</p>';
    return;
  }

  var employeeOptions = employees.map(function(emp) {
    return '<option value="' + escapeHtml(emp.employee_id) + '">' + escapeHtml(emp.full_name || emp.name || emp.employee_id) + '</option>';
  }).join('');

  container.innerHTML = '';
  grievances.forEach(function(g) {
    var badgeClass = statusBadgeClass(g.status);
    var attachmentHtml = g.attachment_path ? '<div class="mt-2"><a href="../../' + escapeHtml(g.attachment_path) + '" target="_blank" class="badge badge-primary"><i class="fas fa-download"></i> Download Attachment</a></div>' : '';
    var updateHtml = '';
    if (updates && updates[g.eer_grievance_id] && updates[g.eer_grievance_id].length) {
      updateHtml = '<div class="mt-3"><strong>Updates:</strong><ul class="mb-0 pl-3">' + updates[g.eer_grievance_id].map(function(u) {
        return '<li>' + escapeHtml(u.update_text) + ' <small class="text-muted">by ' + escapeHtml(u.updated_by) + ' on ' + escapeHtml(u.updated_at) + '</small></li>';
      }).join('') + '</ul></div>';
    }

    var selectHtml = employees.length ? '<select class="form-control form-control-sm" name="assign_to" required><option value="">Assign to...</option>' + employees.map(function(emp) {
      return '<option value="' + escapeHtml(emp.employee_id) + '"' + (g.assigned_to === emp.employee_id ? ' selected' : '') + '>' + escapeHtml(emp.full_name || emp.name || emp.employee_id) + '</option>';
    }).join('') + '</select>' : '<select class="form-control form-control-sm" disabled><option value="">No employees available</option></select>';

    container.insertAdjacentHTML('beforeend',
      '<div class="card mb-3">' +
        '<div class="card-body">' +
          '<div class="d-flex justify-content-between align-items-center">' +
            '<h5 class="card-title mb-0">' + escapeHtml(g.subject) + '</h5>' +
            '<span class="badge badge-pill badge-' + badgeClass + '">' + escapeHtml(g.status) + '</span>' +
          '</div>' +
          '<p class="card-text mt-2 text-clamp-3">' + escapeHtml(g.description).replace(/\n/g, '<br>') + '</p>' +
          '<small class="text-muted">Filed by ' + escapeHtml(g.employee_name || g.employee_id) + ' on ' + escapeHtml(g.created_at) + '</small>' +
          attachmentHtml +
          '<form method="post" class="form-inline mt-3">' +
            '<input type="hidden" name="id" value="' + escapeHtml(g.eer_grievance_id) + '">' +
            '<div class="form-group mr-2">' +
              '<select class="form-control form-control-sm" name="status">' +
                '<option value="Submitted"' + (g.status && g.status.toLowerCase() === 'submitted' ? ' selected' : '') + '>Submitted</option>' +
                '<option value="Under Review"' + (g.status && g.status.toLowerCase() === 'under review' ? ' selected' : '') + '>Under Review</option>' +
                '<option value="For Investigation"' + (g.status && g.status.toLowerCase() === 'for investigation' ? ' selected' : '') + '>For Investigation</option>' +
                '<option value="Resolved"' + (g.status && g.status.toLowerCase() === 'resolved' ? ' selected' : '') + '>Resolved</option>' +
                '<option value="Closed"' + (g.status && g.status.toLowerCase() === 'closed' ? ' selected' : '') + '>Closed</option>' +
              '</select>' +
            '</div>' +
            '<button class="btn btn-sm btn-primary" type="submit" name="update_status">Update</button>' +
          '</form>' +
          '<form method="post" class="form-inline mt-2">' +
            '<input type="hidden" name="id" value="' + escapeHtml(g.eer_grievance_id) + '">' +
            '<div class="form-group mr-2">' + selectHtml + '</div>' +
            '<button class="btn btn-sm btn-outline-success" type="submit" name="assign_grievance">Assign</button>' +
          '</form>' +
          updateHtml +
        '</div>' +
      '</div>'
    );
  });
}
