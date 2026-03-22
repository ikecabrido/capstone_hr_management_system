
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function checkPasswordMatch() {
    const pwd = document.getElementById('password').value;
    const confirm = document.getElementById('confirmPassword').value;
    const error = document.getElementById('passwordError');

    if (pwd !== confirm) {
        error.classList.remove('d-none');
        return false;
    }

    error.classList.add('d-none');
    return true;
}

const passwordInput = document.getElementById('password');
const strengthBar = document.getElementById('strengthBar');
const strengthText = document.getElementById('strengthText');

passwordInput.addEventListener('input', () => {
    const val = passwordInput.value;
    let strength = 0;

    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const levels = ['Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success'];

    strengthBar.className = 'progress-bar ' + colors[strength - 1];
    strengthBar.style.width = (strength * 25) + '%';
    strengthText.textContent = levels[strength - 1] || '';
});

function validateForm() {
    const pwd = passwordInput.value;
    const confirm = document.getElementById('confirmPassword').value;
    const error = document.getElementById('matchError');

    if (pwd !== confirm) {
        error.classList.remove('d-none');
        return false;
    }

    error.classList.add('d-none');
    return true;
}
