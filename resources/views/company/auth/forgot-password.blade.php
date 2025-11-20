<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="icon" href="{{ asset('img/express.png') }}">
    <link rel="stylesheet" href="{{ asset('css/company/auth/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/company/auth/forgot-password.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <div class="container">
        <div class="left">
            <img src="{{ asset('img/company.png') }}" alt="Company Image">
        </div>

        <div class="right-section">
            <div class="right-side">
                <img class="logo" src="{{ asset('img/logo.png') }}" alt="Express Logo">
                <div class="login-form">
                    <div class="icon">
                        <img src="{{ asset('img/mail.png') }}" alt="Email Icon">
                    </div>

                    <div class="login-form">
                        <h1 class="welcome-title">Forgot Password?</h1>
                        <p class="welcome-desc">
                            Enter your registered email address and we'll send you an OTP to reset your password.
                        </p>
                        
                        <form method="POST" action="{{ route('company.password.sendOtp') }}" id="forgotPasswordForm">
                            @csrf
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="text" id="email" name="email" placeholder="example@gmail.com"
                                    value="{{ old('email') }}"
                                    class="@error('email') is-invalid @enderror {{ session('error') ? 'is-invalid' : '' }}">

                                @error('email')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror

                                {{-- Show backend error (like "No account found") under the field --}}
                                @if (session('error'))
                                    <div class="error-message">{{ session('error') }}</div>
                                @endif

                                <div id="emailError" class="error-message"></div>
                            </div>

                            <button type="submit" id="submitBtn" class="send-button">Submit</button>

                            <a href="{{ route('company.login') }}" class="back-link">Back to Login</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
                const emailInput = document.getElementById('email');
                const emailError = document.getElementById('emailError');
                const submitBtn = document.getElementById('submitBtn');
                emailError.textContent = '';
                emailInput.classList.remove('is-invalid');

                const email = emailInput.value.trim();

                if (email === '') {
                    e.preventDefault();
                    emailError.textContent = 'Please enter your email address.';
                    emailInput.classList.add('is-invalid');
                    return;
                } else if (/\s/.test(email)) {
                    e.preventDefault();
                    emailError.textContent = 'Email cannot contain spaces.';
                    emailInput.classList.add('is-invalid');
                    return;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    e.preventDefault();
                    emailError.textContent = 'Please enter a valid email address.';
                    emailInput.classList.add('is-invalid');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending...';
            });

            document.getElementById('email').addEventListener('input', function() {
                const emailError = document.getElementById('emailError');
                this.classList.remove('is-invalid');
                if (emailError.textContent) emailError.textContent = '';
            });

            // Optional: prevent typing spaces in the email field
            document.getElementById('email').addEventListener('keydown', function(e) {
                if (e.key === ' ') {
                    e.preventDefault(); // Block space key
                }
            });
        </script>


        @vite(['resources/js/admin/pages/alert.js'])
</body>

</html>
