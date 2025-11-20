document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
    const username = document.getElementById("username");
    const password = document.getElementById("password");
    const usernameError = document.getElementById("usernameError");
    const passwordError = document.getElementById("passwordError");
    const lockoutOverlay = document.getElementById("lockoutOverlay");
    const countdownTimer = document.getElementById("countdownTimer");
    const loginContainer = document.getElementById("loginContainer");
    const loginButton = document.getElementById("loginButton");

    // Prevent spaces in username and password
    document.querySelectorAll("#username, #password").forEach(input => {
        input.addEventListener("keydown", function(e) {
            if (e.key === " ") {
                e.preventDefault();
            }
        });

        input.addEventListener("input", function() {
            this.value = this.value.replace(/\s/g, "");
        });
    });

    // Form validation
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Check lockout first
        if (isLockedOut()) {
            return;
        }

        let valid = true;

        // Reset error states
        username.classList.remove("error");
        password.classList.remove("error");
        usernameError.style.display = "none";
        passwordError.style.display = "none";

        // Validate email
        const emailValue = username.value.trim();  
        if (!emailValue) {
            usernameError.textContent = "Email is required.";
            usernameError.style.display = "block";
            username.classList.add("error");
            valid = false;
        } else if (/\s/.test(emailValue)) {
            usernameError.textContent = "Email cannot contain spaces.";
            usernameError.style.display = "block";
            username.classList.add("error");
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
            usernameError.textContent = "Please enter a valid email address.";
            usernameError.style.display = "block";
            username.classList.add("error");
            valid = false;
        } else {
            usernameError.style.display = "none";
            username.classList.remove("error");
        }

        // Validate password
        const passwordValue = password.value;
        if (!passwordValue) {
            passwordError.textContent = "Password is required.";
            passwordError.style.display = "block";
            password.classList.add("error");
            valid = false;
        } else if (/^\s/.test(passwordValue)) {
            passwordError.textContent = "Password cannot start with a space.";
            passwordError.style.display = "block";
            password.classList.add("error");
            valid = false;
        } else if (passwordValue.length < 8) {
            passwordError.textContent = "Password must be at least 8 characters.";
            passwordError.style.display = "block";
            password.classList.add("error");
            valid = false;
        }

        // Submit if valid
        if (valid) {
            form.submit();
        }
    });

    // ===== LOCKOUT COUNTDOWN FUNCTIONALITY =====
    let countdownInterval = null;

    function isLockedOut() {
        const lockoutUntil = localStorage.getItem('company_lockout_until');
        if (!lockoutUntil) return false;

        const now = Math.floor(Date.now() / 1000);
        return parseInt(lockoutUntil) > now;
    }

    function getRemainingSeconds() {
        const lockoutUntil = localStorage.getItem('company_lockout_until');
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
        localStorage.removeItem('company_lockout_until');
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
        localStorage.removeItem('company_lockout_until');
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