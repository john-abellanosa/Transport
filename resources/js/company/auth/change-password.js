document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const strengthMeter = document.getElementById('strengthMeter');
    const messageDiv = document.getElementById('message');
    const form = document.getElementById('passwordForm');

    const lengthReq = document.getElementById('lengthReq');
    const uppercaseReq = document.getElementById('uppercaseReq');
    const numberReq = document.getElementById('numberReq');
    const specialReq = document.getElementById('specialReq');

    // Toggle show/hide password
    function setupPasswordToggle(toggleElement, inputElement) {
        toggleElement.addEventListener('click', function() {
            const type = inputElement.type === 'password' ? 'text' : 'password';
            inputElement.type = type;
            toggleElement.classList.toggle('fa-eye');
            toggleElement.classList.toggle('fa-eye-slash');
        });
    }
    setupPasswordToggle(togglePassword, passwordInput);
    setupPasswordToggle(toggleConfirmPassword, confirmInput);

    // Password strength
    passwordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        let strength = 0;

        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(password);

        updateRequirement(lengthReq, hasLength);
        updateRequirement(uppercaseReq, hasUppercase);
        updateRequirement(numberReq, hasNumber);
        updateRequirement(specialReq, hasSpecial);

        if (hasLength) strength += 25;
        if (hasUppercase) strength += 25;
        if (hasNumber) strength += 25;
        if (hasSpecial) strength += 25;

        strengthMeter.style.width = strength + '%';
        strengthMeter.style.background =
            strength < 50 ? '#e74c3c' :
            strength < 100 ? '#f39c12' :
            '#2ecc71';

        checkPasswordsMatch();
    });

    function updateRequirement(element, met) {
        const icon = element.querySelector("i");
        if (met) {
            element.classList.add("met");
            icon.classList.remove("fa-circle");
            icon.classList.add("fa-check-circle");
        } else {
            element.classList.remove("met");
            icon.classList.remove("fa-check-circle");
            icon.classList.add("fa-circle");
        }
    }

    confirmInput.addEventListener('input', checkPasswordsMatch);

    function checkPasswordsMatch() {
        const confirmMessage = document.getElementById('confirmMessage');
        if (passwordInput.value && confirmInput.value) {
            if (passwordInput.value === confirmInput.value) {
                confirmMessage.style.color = '#2ecc71';
                confirmMessage.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
            } else {
                confirmMessage.style.color = '#e74c3c';
                confirmMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> Passwords do not match';
            }
        } else {
            confirmMessage.innerHTML = '';
        }
    }
 
    // Fix form submission
    form.addEventListener('submit', function(e) {
    e.preventDefault();

    // Reset error messages
    document.getElementById('passwordError').innerText = '';
    document.getElementById('confirmError').innerText = '';

    let valid = true;

    // Password validations
    if (!passwordInput.value) {
        document.getElementById('passwordError').innerText = "Please enter new password";
        valid = false;
    } else if (/^\s/.test(passwordInput.value)) {
        document.getElementById('passwordError').innerText = "Password cannot start with spaces.";
        valid = false;
    } else if (passwordInput.value.length < 8) {
        document.getElementById('passwordError').innerText = "";
        valid = false;
    } else if (!/[A-Z]/.test(passwordInput.value)) {
        document.getElementById('passwordError').innerText = "";
        valid = false;
    } else if (!/[0-9]/.test(passwordInput.value)) {
        document.getElementById('passwordError').innerText = "";
        valid = false;
    } else if (!/[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(passwordInput.value)) {
        document.getElementById('passwordError').innerText = "";
        valid = false;
    }

    // Confirm password validations
    if (!confirmInput.value) {
        document.getElementById('confirmError').innerText = "Please confirm your password.";
        valid = false;
    } else if (/^\s/.test(confirmInput.value)) {
        document.getElementById('confirmError').innerText = "Confirm password cannot start with spaces.";
        valid = false;
    } else if (passwordInput.value !== confirmInput.value) {
        document.getElementById('confirmError').innerText = "";
        valid = false;
    }

    // ✅ Submit only if everything is valid
    if (valid) {
        form.submit();
    }
});

    function showMessage(text, type) {
        messageDiv.innerHTML = text;
        messageDiv.className = 'message ' + type;
        messageDiv.style.display = 'block';
        setTimeout(() => { messageDiv.style.display = 'none'; }, 3000);
    }
});
