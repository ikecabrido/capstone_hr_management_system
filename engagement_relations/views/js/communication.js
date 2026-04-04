document.addEventListener('DOMContentLoaded', function() {
  loadReceiverList();
  loadAnnouncements();
  loadMessageThreads();

  const forms = document.querySelectorAll('form');
  forms.forEach(function(form) {
    form.addEventListener('submit', function() {
      const button = form.querySelector('button[type=submit]');
      if (button) { button.textContent = 'Sending...'; button.disabled = true; }
    });
  });
});

function loadReceiverList() {
  const sel = document.getElementById('receiver-id');
  if (!sel) return;

  fetch('../api/employee_list.php')
    .then(response => {
      if (!response.ok) throw new Error('Failed to load receiver list');
      return response.json();
    })
    .then(list => {
      list.forEach(emp => {
        const opt = document.createElement('option');
        opt.value = emp.employee_id;
        opt.textContent = (emp.full_name || emp.name || emp.employee_id) + ' (' + emp.employee_id + ')';
        sel.appendChild(opt);
      });
    })
    .catch(error => {
      console.error('Error loading receiver list:', error);
    });
}

function loadAnnouncements() {
  const announcementsList = document.getElementById('announcementsList');
  if (!announcementsList) return;

  fetch('../api/communication.php?action=announcements')
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch announcements');
      return response.json();
    })
    .then(announcements => {
      if (!announcements || announcements.length === 0) {
        announcementsList.innerHTML = '<li class="list-group-item text-muted">No announcements yet.</li>';
        return;
      }

      let html = '';
      announcements.forEach(a => {
        html += `
          <li class="list-group-item">
            <strong>${escapeHtml(a.title || 'Announcement')}</strong>
            <br>
            <p>${escapeHtml(a.content || '')}</p>
            <small class="text-muted">By ${escapeHtml(a.created_by_name || a.created_by || 'Unknown')} at ${escapeHtml(a.created_at || '')}</small>
          </li>
        `;
      });

      announcementsList.innerHTML = html;
    })
    .catch(error => {
      console.error('Error loading announcements:', error);
      announcementsList.innerHTML = '<li class="list-group-item text-danger">Error loading announcements</li>';
    });
}

function loadMessageThreads() {
  const messagesList = document.getElementById('messagesList');
  if (!messagesList) return;

  const empId = getUserEmployeeId();
  if (!empId || empId <= 0) {
    messagesList.innerHTML = '<li class="list-group-item text-danger">Error: Invalid employee ID</li>';
    return;
  }

  fetch('../api/communication.php?action=messages&employee_id=' + empId)
    .then(response => {
      if (!response.ok) {
        return response.json().then(err => {
          throw new Error(err.error || 'Failed to fetch messages');
        });
      }
      return response.json();
    })
    .then(threads => {
      if (!threads || threads.length === 0) {
        messagesList.innerHTML = '<li class="list-group-item text-muted">No messages yet.</li>';
        return;
      }

      let html = '';
      threads.forEach(t => {
        const currentEmpId = empId;
        let otherPersonName = 'Unknown';
        let direction = '';

        if (t.sender_id == currentEmpId) {
          otherPersonName = t.receiver_name || 'Unknown';
          direction = 'To: ';
        } else if (t.receiver_id == currentEmpId) {
          otherPersonName = t.sender_name || 'Unknown';
          direction = 'From: ';
        } else {
          otherPersonName = t.sender_name || t.receiver_name || 'Unknown';
        }

        html += `
          <li class="list-group-item">
            <strong>${direction}${escapeHtml(otherPersonName)}</strong>
            <br>
            <p>${escapeHtml((t.message || '').substring(0, 100))}...</p>
            <small class="text-muted">${escapeHtml(t.timestamp || t.created_at || '')}</small>
          </li>
        `;
      });

      messagesList.innerHTML = html;
    })
    .catch(error => {
      console.error('Error loading message threads:', error);
      messagesList.innerHTML = '<li class="list-group-item text-danger">Error: ' + escapeHtml(error.message) + '</li>';
    });
}

function getUserEmployeeId() {
  const body = document.body;
  if (body && body.dataset.employeeId) {
    const id = parseInt(body.dataset.employeeId, 10);
    if (id > 0) return id;
  }

  const attr = document.querySelector('[data-employee-id]');
  if (attr && attr.getAttribute('data-employee-id')) {
    const id = parseInt(attr.getAttribute('data-employee-id'), 10);
    if (id > 0) return id;
  }

  const input = document.querySelector('input[name="employee_id"]');
  if (input && input.value) {
    const id = parseInt(input.value, 10);
    if (id > 0) return id;
  }

  return 0;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

