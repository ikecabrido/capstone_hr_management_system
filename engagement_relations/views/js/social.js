document.addEventListener('DOMContentLoaded', function() {
  const commentForms = document.querySelectorAll('.comment-form');
  commentForms.forEach(function(form) {
    form.addEventListener('submit', function() {
      const button = form.querySelector('button[type=submit]');
      if (button) { button.textContent = 'Posting...'; button.disabled = true; }
    });
  });
});