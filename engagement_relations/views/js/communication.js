document.addEventListener('DOMContentLoaded', function() {
  // Load announcements and threads from API
  loadAnnouncements();
  // Message threads are loaded directly in PHP, no API call needed
  
  // Handle form submissions
  const forms = document.querySelectorAll('.communication-form form');
  forms.forEach(function(form) {
    form.addEventListener('submit', function() {
      const button = form.querySelector('button[type=submit]');
      if (button) { button.textContent = 'Sending...'; button.disabled = true; }
    });
  });
});

// Load announcements from API
function loadAnnouncements() {
  const announcementsList = document.getElementById('announcementsList');
  if (!announcementsList) return;
  
  console.log('Loading announcements from API...');
  
  fetch('../api/communication.php?action=announcements')
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch announcements');
      return response.json();
    })
    .then(announcements => {
      console.log('Announcements loaded:', announcements);
      
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
            <small class="text-muted">By ${escapeHtml(a.employee_name || 'Unknown')} at ${a.created_at || ''}</small>
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

// Load message threads from API
function loadMessageThreads() {
  const messagesList = document.getElementById('messagesList');
  if (!messagesList) return;
  
  const empId = getUserEmployeeId();
  console.log('Loading message threads from API with employee_id=' + empId);
  
  if (!empId || empId <= 0) {
    console.error('Invalid employee ID:', empId);
    messagesList.innerHTML = '<li class="list-group-item text-danger">Error: Invalid employee ID</li>';
    return;
  }
  
  const url = '../api/communication.php?action=messages&employee_id=' + empId;
  console.log('API URL:', url);
  
  fetch(url)
    .then(response => {
      console.log('Response status:', response.status);
      if (!response.ok) {
        return response.json().then(err => {
          throw new Error(err.error || 'Failed to fetch messages');
        });
      }
      return response.json();
    })
    .then(threads => {
      console.log('Message threads loaded:', threads);
      
      if (!threads || threads.length === 0) {
        messagesList.innerHTML = '<li class="list-group-item text-muted">No messages yet.</li>';
        return;
      }
      
      let html = '';
      threads.forEach(t => {
        // Determine who the message is between
        const currentEmpId = empId;
        let otherPersonName = 'Unknown';
        let direction = '';
        
        if (t.sender_id == currentEmpId) {
          // Current user is sender, show receiver
          otherPersonName = t.receiver_name || 'Unknown';
          direction = 'To: ';
        } else if (t.receiver_id == currentEmpId) {
          // Current user is receiver, show sender
          otherPersonName = t.sender_name || 'Unknown';
          direction = 'From: ';
        } else {
          // Shouldn't happen, but fallback
          otherPersonName = t.sender_name || t.receiver_name || 'Unknown';
        }
        
        html += `
          <li class="list-group-item">
            <strong>${direction}${escapeHtml(otherPersonName)}</strong>
            <br>
            <p>${escapeHtml((t.message || '').substring(0, 100))}...</p>
            <small class="text-muted">${t.timestamp || t.created_at || ''}</small>
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

// Helper function to get employee ID from page
function getUserEmployeeId() {
  // Try to find employee ID from body data attribute
  const body = document.body;
  if (body && body.dataset.employeeId) {
    const id = parseInt(body.dataset.employeeId, 10);
    if (id > 0) {
      console.log('Employee ID from body:', id);
      return id;
    }
  }
  
  // Try to find employee ID from any data attribute
  const attr = document.querySelector('[data-employee-id]');
  if (attr && attr.getAttribute('data-employee-id')) {
    const id = parseInt(attr.getAttribute('data-employee-id'), 10);
    if (id > 0) {
      console.log('Employee ID from element:', id);
      return id;
    }
  }
  
  const input = document.querySelector('input[name="employee_id"]');
  if (input && input.value) {
    const id = parseInt(input.value, 10);
    if (id > 0) {
      console.log('Employee ID from input:', id);
      return id;
    }
  }
  
  // Default fallback
  console.warn('No employee ID found, defaulting to 0');
  return 0;
}

// Helper function to escape HTML
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}