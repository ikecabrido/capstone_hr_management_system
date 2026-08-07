document.addEventListener('DOMContentLoaded', function() {
  var surveyForm = document.querySelector('.survey-form');
  if (surveyForm) {
    surveyForm.addEventListener('submit', function() {
      surveyForm.querySelector('button[type=submit]').textContent = 'Creating...';
    });
  }

  var chartCanvas = document.getElementById('surveyResultsChart');
  if (!chartCanvas || typeof Chart === 'undefined') return;

  var labels = [];
  var values = [];

  try {
    labels = JSON.parse(chartCanvas.dataset.surveyLabels || '[]');
    values = JSON.parse(chartCanvas.dataset.surveyValues || '[]');
  } catch (ex) {
    console.error('Failed to parse survey chart data', ex);
  }

  if (!Array.isArray(labels) || !Array.isArray(values)) {
    return;
  }

  var ctx = chartCanvas.getContext('2d');
  if (!ctx) return;

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Responses',
        data: values,
        backgroundColor: values.map(function() {
          return 'rgba(75, 192, 192, 0.2)';
        }),
        borderColor: values.map(function() {
          return 'rgba(75, 192, 192, 1)';
        }),
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
});