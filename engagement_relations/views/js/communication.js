document.addEventListener('DOMContentLoaded', function() {
  const forms = document.querySelectorAll('.communication-form form');
  forms.forEach(function(form) {
    form.addEventListener('submit', function() {
      const button = form.querySelector('button[type=submit]');
      if (button) { button.textContent = 'Sending...'; button.disabled = true; }
    });
  });
});