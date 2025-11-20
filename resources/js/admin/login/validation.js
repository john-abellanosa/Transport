window.togglePassword = function () {
    const passwordField = document.getElementById("password");
    if (passwordField) {
        passwordField.type =
            passwordField.type === "password" ? "text" : "password";
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const passwordField = document.getElementById("password");
    const showPasswordCheckbox = document.getElementById("showPassword");
    const form = document.getElementById("loginForm");
    const username = document.getElementById("username");
    const password = document.getElementById("password");
    const usernameError = document.getElementById("usernameError");
    const passwordError = document.getElementById("passwordError");
    const lockoutOverlay = document.getElementById("lockoutOverlay");
    const countdownTimer = document.getElementById("countdownTimer");
    const loginContainer = document.getElementById("loginContainer");
    const loginButton = document.getElementById("loginButton");

    // Password visibility toggle
    if (passwordField && showPasswordCheckbox) {
        showPasswordCheckbox.addEventListener("change", () => {
            passwordField.type = showPasswordCheckbox.checked ? "text" : "password";
        });
    }

    // Prevent spaces in username and password
    document.querySelectorAll("#username, #password").forEach((input) => {
        input.addEventListener("keydown", function (e) {
            if (e.key === " ") e.preventDefault();
        });

        input.addEventListener("input", function () {
            this.value = this.value.replace(/\s/g, "");
        });
    });

    // Form validation
    form.addEventListener("submit", function (e) {
        // Check lockout first
        if (isLockedOut()) {
            e.preventDefault();
            return;
        }

        let valid = true;
        username.classList.remove("error");
        password.classList.remove("error");
        usernameError.textContent = "";
        passwordError.textContent = "";

        if (username.value.trim() === "") {
            username.classList.add("error");
            usernameError.textContent = "Username is required.";
            valid = false;
        } else if (/\s/.test(username.value)) {
            username.classList.add("error");
            usernameError.textContent = "Username cannot contain spaces.";
            valid = false;
        }

        if (password.value.trim() === "") {
            password.classList.add("error");
            passwordError.textContent = "Password is required.";
            valid = false;
        } else if (/^\s/.test(password.value)) {
            password.classList.add("error");
            passwordError.textContent = "Password cannot start with a space.";
            valid = false;
        } else if (password.value.length < 8) {
            password.classList.add("error");
            passwordError.textContent = "Password must be at least 8 characters.";
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    // ===== LOCKOUT COUNTDOWN FUNCTIONALITY =====
    let countdownInterval = null;

    function isLockedOut() {
        const lockoutUntil = localStorage.getItem('admin_lockout_until');
        if (!lockoutUntil) return false;

        const now = Math.floor(Date.now() / 1000);
        return parseInt(lockoutUntil) > now;
    }

    function getRemainingSeconds() {
        const lockoutUntil = localStorage.getItem('admin_lockout_until');
        if (!lockoutUntil) return 0;

        const now = Math.floor(Date.now() / 1000);
        const remaining = parseInt(lockoutUntil) - now;
        return remaining > 0 ? remaining : 0;
    }

    function showLockout() {
        lockoutOverlay.classList.add('active');
        loginContainer.classList.add('form-disabled');
        if (loginButton) {
            loginButton.disabled = true;
        }
    }

    function hideLockout() {
        lockoutOverlay.classList.remove('active');
        loginContainer.classList.remove('form-disabled');
        if (loginButton) {
            loginButton.disabled = false;
        }
        localStorage.removeItem('admin_lockout_until');
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
        
        // Reload page to clear server-side rate limiter
        window.location.href = window.location.pathname;
    }

    function updateCountdown() {
        const remaining = getRemainingSeconds();

        if (remaining <= 0) {
            hideLockout();
            return;
        }

        // Update display
        if (countdownTimer) {
            countdownTimer.textContent = remaining;
        }
    }

    function startCountdown() {
        if (!isLockedOut()) {
            hideLockout();
            return;
        }

        showLockout();
        updateCountdown();

        // Clear any existing interval
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }

        // Update every second
        countdownInterval = setInterval(() => {
            updateCountdown();
        }, 1000);
    }

    // Initialize on page load - check if locked out
    if (isLockedOut()) {
        startCountdown();
    } else {
        // Clean up any stale lockout data
        localStorage.removeItem('admin_lockout_until');
    }

    // Prevent any form interaction during lockout
    loginContainer.addEventListener('click', function(e) {
        if (isLockedOut()) {
            e.preventDefault();
            e.stopPropagation();
        }
    }, true);

    // Double-check on form submission
    form.addEventListener("submit", function(e) {
        if (isLockedOut()) {
            e.preventDefault();
            e.stopPropagation();
            showLockout();
        }
    }, true);
});