document.addEventListener("DOMContentLoaded", function () {
    const startInput = document.getElementById("start_date");
    const endInput = document.getElementById("end_date");

    const today = new Date();
    const tomorrow = new Date();

    tomorrow.setDate(today.getDate() + 1);

    const formatDate = (date) => date.toISOString().split('T')[0];

    startInput.value = formatDate(today);
    endInput.value = formatDate(tomorrow);

    startInput.min = formatDate(today);
    endInput.min = formatDate(today);
});
document.getElementById("start_date").addEventListener("change", function () {
    const start = new Date(this.value);
    const end = new Date(start);
    end.setDate(start.getDate() + 1);

    const formatDate = (date) => date.toISOString().split('T')[0];

    document.getElementById("end_date").value = formatDate(end);
    document.getElementById("end_date").min = formatDate(start);
});

function validatePassword() {
    const pass = document.getElementById('newPassword').value;
    const confirm = document.getElementById('confirmPassword').value;
    const errorDiv = document.getElementById('passwordError');

    if (pass.length < 6) {
        errorDiv.style.display = 'block';
        errorDiv.innerText = 'Password must be at least 6 characters.';
        return false;
    }

    if (pass !== confirm) {
        errorDiv.style.display = 'block';
        errorDiv.innerText = 'Passwords do not match.';
        return false;
    }

    errorDiv.style.display = 'none';
    return true;
}