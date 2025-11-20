<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="icon" href="{{ asset('img/express.png') }}">
    <link rel="stylesheet" href="{{ asset('css/driver/auth/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/driver/auth/change-password.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    @if (session('success'))
        <div id="success-alert" class="success_alert">
            <strong></strong> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div id="error-alert" class="error_alert">
            <strong></strong> {{ session('error') }}
        </div>
    @endif

    <div class="container">
        <div class="left">
            <img src="{{ asset('img/driver.jpg') }}" alt="Driver">
        </div>

        <div class="right-section">
            <div class="right-side">
                <img class="logo" src="{{ asset('img/logo.png') }}" alt="Express Logo">

                <div class="change-form">
                    <h1 class="welcome-title">Change Password</h1>
                    <p class="welcome-desc">
                        Create a strong and secure password to protect your account.
                    </p>

                    <form method="POST" action="{{ route('driver.updatePassword') }}" id="passwordForm">
                        @csrf

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" id="password" placeholder="Enter new password">
                            <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                            <div class="error-message" id="passwordError"></div>

                            <div class="password-strength">
                                <div class="strength-meter" id="strengthMeter"></div>
                            </div>

                            <div class="password-requirements">
                                <div class="requirement" id="lengthReq"><i class="fas fa-circle"></i> At least 8 characters</div>
                                <div class="requirement" id="uppercaseReq"><i class="fas fa-circle"></i> Contains uppercase letters</div>
                                <div class="requirement" id="numberReq"><i class="fas fa-circle"></i> Contains numbers</div>
                                <div class="requirement" id="specialReq"><i class="fas fa-circle"></i> Contains special characters</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm new password">
                            <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>
                            <div id="confirmMessage" style="font-size: 13px; margin-top: 8px;"></div>
                            <div class="error-message" id="confirmError"></div>
                        </div>

                        <button type="submit" class="send-button">Submit</button>

                        <a href="{{ route('driver.backLogin') }}" class="back-link">Back to Login</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

@vite(['resources/js/driver/auth/change-password.js'])
@vite(['resources/js/admin/pages/alert.js'])