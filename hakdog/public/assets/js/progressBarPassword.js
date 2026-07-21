const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');
const strengthBar = document.getElementById('strengthBar');
const strengthText = document.getElementById('strengthText');
const matchError = document.getElementById('matchError');

password.addEventListener('input', function () {
    const val = password.value;
    let strength = 0;

    if (val.length >= 8) strength += 25;
    if (/[A-Z]/.test(val)) strength += 25;
    if (/[0-9]/.test(val)) strength += 25;
    if (/[^A-Za-z0-9]/.test(val)) strength += 25;

    strengthBar.style.width = strength + "%";

    if (strength <= 25) {
        strengthBar.className = "progress-bar bg-danger rounded-pill";
        strengthText.textContent = "Weak";
        strengthText.className = "text-danger fw-semibold d-block mt-1";
    } else if (strength <= 50) {
        strengthBar.className = "progress-bar bg-warning rounded-pill";
        strengthText.textContent = "Fair";
        strengthText.className = "text-warning fw-semibold d-block mt-1";
    } else if (strength <= 75) {
        strengthBar.className = "progress-bar bg-info rounded-pill";
        strengthText.textContent = "Good";
        strengthText.className = "text-info fw-semibold d-block mt-1";
    } else {
        strengthBar.className = "progress-bar bg-success rounded-pill";
        strengthText.textContent = "Strong";
        strengthText.className = "text-success fw-semibold d-block mt-1";
    }
});

confirmPassword.addEventListener('input', function () {
    if (confirmPassword.value !== password.value) {
        matchError.classList.remove('d-none');
    } else {
        matchError.classList.add('d-none');
    }
});

function validateForm() {
    if (password.value !== confirmPassword.value) {
        matchError.classList.remove('d-none');
        return false;
    }
    return true;
}

document.addEventListener("DOMContentLoaded", function () {

    const password = document.getElementById('new-password');
    const confirmPassword = document.getElementById('confirm-password');
    const strengthBar = document.getElementById('strengthBar');

    if (!password) return; 

    password.addEventListener('input', function () {
        const val = password.value;
        let strength = 0;

        if (val.length >= 8) strength += 25;
        if (/[A-Z]/.test(val)) strength += 25;
        if (/[0-9]/.test(val)) strength += 25;
        if (/[^A-Za-z0-9]/.test(val)) strength += 25;

        strengthBar.style.width = strength + "%";

        if (strength <= 25) {
            strengthBar.className = "progress-bar bg-danger rounded-pill";
        } else if (strength <= 50) {
            strengthBar.className = "progress-bar bg-warning rounded-pill";
        } else if (strength <= 75) {
            strengthBar.className = "progress-bar bg-info rounded-pill";
        } else {
            strengthBar.className = "progress-bar bg-success rounded-pill";
        }
    });

});