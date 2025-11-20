<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" href="{{ asset('img/express.png') }}">
    <link rel="stylesheet" href="{{ asset('css/driver/auth/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/driver/auth/forgot-password.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
</head>

<body>

    @if (request('expired'))
        <div id="error-alert" class="error_alert">Your OTP has expired. Please request a new one.</div>
    @endif

    <div class="container">
        <div class="left">
            <img src="{{ asset('img/driver.jpg') }}" alt="Driver">
        </div>

        <div class="right-section">
            <div class="right-side">
                <img class="logo" src="{{ asset('img/logo.png') }}" alt="Express Logo">

                <div class="login-form">
                    <h1 class="welcome-title">Forgot Password?</h1>
                    <p class="welcome-desc">
                        Enter your registered email address and we'll send you an OTP to reset your password.
                    </p>

                    <form id="forgotForm" method="POST" action="{{ route('driver.sendOtp') }}">
                        @csrf

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="text" name="email" id="email"
                                placeholder="Enter your registered email" value="{{ old('email') }}"
                                class="{{ session('error') || $errors->has('email') ? 'is-invalid' : '' }}"
                                autocomplete="email">

                            <div class="error-message" id="emailError">
                                @if (session('error'))
                                    {{ session('error') }}
                                @endif
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="send-button">Submit</button>

                        <a href="{{ route('driver.login') }}" class="back-link">Back to Login</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('forgotForm');
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');

        form.addEventListener('submit', function(e) {
            emailError.textContent = '';
            emailInput.classList.remove('is-invalid');
            const email = emailInput.value.trim();

            if (email === '') {
                e.preventDefault();
                emailError.textContent = 'Please enter your email address.';
                emailInput.classList.add('is-invalid');
            } else if (/\s/.test(email)) {
                e.preventDefault();
                emailError.textContent = 'Email cannot contain spaces.';
                emailInput.classList.add('is-invalid');
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                emailError.textContent = 'Please enter a valid email address.';
                emailInput.classList.add('is-invalid');
            }
        });

        // Clear error on input
        emailInput.addEventListener('input', function() {
            if (emailError.textContent) emailError.textContent = '';
            this.classList.remove('is-invalid');
        });

        // Prevent typing spaces in the email field
        emailInput.addEventListener('keydown', function(e) {
            if (e.key === ' ') {
                e.preventDefault(); // block space key
            }
        });
    </script>


    @vite(['resources/js/admin/pages/alert.js'])
</body>

</html>
