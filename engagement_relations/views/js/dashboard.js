document.addEventListener('DOMContentLoaded', function() {
  console.log('Dashboard module loaded');
  loadNotifications();
  renderDashboardCharts();
});

function loadNotifications() {
  const notificationsList = document.getElementById('notifications-list');
  if (!notificationsList) return;

  fetch('../api/index.php?resource=communication&action=notifications')
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to fetch notifications');
      }
      return response.json();
    })
    .then(data => {
      if (!Array.isArray(data) || data.length === 0) {
        notificationsList.innerHTML = '<li class="text-muted">No notifications found.</li>';
        return;
      }

      notificationsList.innerHTML = '';
      data.forEach(notification => {
        const listItem = document.createElement('li');
        listItem.textContent = notification.message || 'Untitled notification';
        if (!notification.is_read) {
          listItem.style.fontWeight = 'bold';
          listItem.addEventListener('click', function() {
            fetch('../api/index.php?resource=communication&action=mark_notification_read', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ notification_id: notification.id })
            })
              .then(() => {
                listItem.style.fontWeight = 'normal';
              })
              .catch(err => {
                console.error('Unable to mark notification read:', err);
              });
          });
        }
        notificationsList.appendChild(listItem);
      });
    })
    .catch(error => {
      console.error('Error loading notifications:', error);
      notificationsList.innerHTML = '<li class="text-danger">Failed to load notifications.</li>';
    });
}

function renderDashboardCharts() {
  // Survey Chart (bar)
  var surveyCanvas = document.getElementById('dashboardSurveyChart');
  if (surveyCanvas && typeof Chart !== 'undefined') {
    try {
      var sLabels = JSON.parse(surveyCanvas.dataset.surveyLabels || '[]');
      var sValues = JSON.parse(surveyCanvas.dataset.surveyValues || '[]');
      var ctx = surveyCanvas.getContext('2d');
      if (ctx && Array.isArray(sLabels) && Array.isArray(sValues)) {
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: sLabels,
            datasets: [{
              label: 'Responses',
              data: sValues,
              backgroundColor: 'rgba(54,162,235,0.2)',
              borderColor: 'rgba(54,162,235,1)',
              borderWidth: 1
            }]
          },
          options: { scales: { y: { beginAtZero: true } }, responsive: true, maintainAspectRatio: false }
        });
      }
    } catch (ex) { console.error('Survey chart render error', ex); }
  }

  // Feedback Chart (doughnut)
  var feedbackCanvas = document.getElementById('dashboardFeedbackChart');
  if (feedbackCanvas && typeof Chart !== 'undefined') {
    try {
      var fLabels = JSON.parse(feedbackCanvas.dataset.feedbackLabels || '[]');
      var fValues = JSON.parse(feedbackCanvas.dataset.feedbackValues || '[]');
      var ctx2 = feedbackCanvas.getContext('2d');
      if (ctx2 && Array.isArray(fLabels) && Array.isArray(fValues)) {
        var colors = fLabels.map(function(_, i){
          var palette = ['#36A2EB','#FF6384','#FFCE56','#4BC0C0','#9966FF','#FF9F40'];
          return palette[i % palette.length];
        });
        new Chart(ctx2, {
          type: 'doughnut',
          data: { labels: fLabels, datasets: [{ data: fValues, backgroundColor: colors }] },
          options: { responsive: true, maintainAspectRatio: false }
        });
      }
    } catch (ex) { console.error('Feedback chart render error', ex); }
  }

  // Grievance Chart (bar by status)
  var grievanceCanvas = document.getElementById('dashboardGrievanceChart');
  if (grievanceCanvas && typeof Chart !== 'undefined') {
    try {
      var gLabels = JSON.parse(grievanceCanvas.dataset.grievanceLabels || '[]');
      var gValues = JSON.parse(grievanceCanvas.dataset.grievanceValues || '[]');
      var ctx3 = grievanceCanvas.getContext('2d');
      if (ctx3 && Array.isArray(gLabels) && Array.isArray(gValues)) {
        new Chart(ctx3, {
          type: 'bar',
          data: {
            labels: gLabels,
            datasets: [{
              label: 'Count',
              data: gValues,
              backgroundColor: 'rgba(255,99,132,0.2)',
              borderColor: 'rgba(255,99,132,1)',
              borderWidth: 1
            }]
          },
          options: { scales: { y: { beginAtZero: true } }, responsive: true, maintainAspectRatio: false }
        });
      }
    } catch (ex) { console.error('Grievance chart render error', ex); }
  }
}

