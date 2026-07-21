
    const passwordInput = document.getElementById('new-password');
    const confirmInput = document.getElementById('confirm-password');
    const strengthBar = document.getElementById('passwordStrength');
    const matchText = document.getElementById('passwordMatch');
    const saveBtn = document.getElementById('savePasswordBtn');

    function checkStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[\W]/.test(password)) strength += 1;
        return strength;
    }

    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        const strength = checkStrength(val);
        strengthBar.style.width = (strength * 20) + '%';
        strengthBar.className = 'progress-bar bg-' +
            (strength <= 2 ? 'danger' : strength <= 4 ? 'warning' : 'success');

        saveBtn.disabled = strength < 3 || val !== confirmInput.value;
    });

    confirmInput.addEventListener('input', () => {
        const val = confirmInput.value;
        if (passwordInput.value !== val) {
            matchText.textContent = "Passwords do not match!";
            matchText.className = "form-text text-danger";
            saveBtn.disabled = true;
        } else {
            matchText.textContent = "Passwords match!";
            matchText.className = "form-text text-success";
            saveBtn.disabled = checkStrength(passwordInput.value) < 3;
        }
    });