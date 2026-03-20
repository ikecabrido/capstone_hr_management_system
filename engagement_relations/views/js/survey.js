document.addEventListener('DOMContentLoaded', function() {
  var surveyForm = document.querySelector('.survey-form');
  if (surveyForm) {
    surveyForm.addEventListener('submit', function() {
      surveyForm.querySelector('button[type=submit]').textContent = 'Creating...';
    });
  }
});