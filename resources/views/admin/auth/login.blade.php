@section('title', 'Admin Login')

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/express.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/auth/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
</head>

<body> 

    <div class="lockout-overlay" id="lockoutOverlay">
        <div class="lockout-container">
            <div class="lockout-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h2 class="lockout-title">Too Many Attempts</h2>
            <p class="lockout-message">
                Your account has been temporarily locked due to multiple failed login attempts. Please wait.
            </p>
            <div class="countdown-display">
                <div class="countdown-timer" id="countdownTimer">60</div>
                <div class="countdown-label">Seconds Remaining</div>
            </div>
        </div>
    </div>
 

    <div class="container" id="loginContainer">
        <div class="left">
            <img src="{{ asset('img/truck.png') }}" alt="Truck">
        </div>

        <div class="right-section">
            <div class="right-side">
                <img class="logo" src="{{ asset('img/logo.png') }}" alt="Express Logo">

                <div class="login-form">
                    <h1 class="welcome-title">Welcome Admin</h1>
                    <p class="welcome-desc">Log in to manage your system.</p>

                    @if (session('error'))
                        <div class="inline-error" id="inlineError">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
                        @csrf

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" value="{{ old('username') }}"
                                placeholder="Enter your username" class="{{ $errors->has('username') ? 'error' : '' }}" autocomplete="username"> 

                            <div class="error-message" id="usernameError">
                                @if ($errors->has('username') && !$errors->has('lockout'))
                                    {{ $errors->first('username') }}
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" placeholder="Enter your password"
                                class="{{ $errors->has('password') ? 'error' : '' }}" autocomplete="current-password">
                            <div class="error-message" id="passwordError">
                                @if ($errors->has('password') && !$errors->has('lockout'))
                                    {{ $errors->first('password') }}
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-options">
                            <div class="show-password">
                                <input type="checkbox" id="showPassword">
                                <label for="showPassword">Show password</label>
                            </div>
                        </div>

                        <button type="submit" class="login-button" id="loginButton">Log in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const inlineError = document.getElementById("inlineError");
            if (inlineError) { 
                const hadPreviousError = sessionStorage.getItem("admin_error_shown");

                if (hadPreviousError) { 
                    inlineError.classList.add("blink");

                    inlineError.addEventListener("animationend", () => {
                        inlineError.classList.remove("blink");
                    });
                }
 
                sessionStorage.setItem("admin_error_shown", "true");
            }
        });
    </script>

    <script> 
        @if(session('lockout_until'))
            const serverLockoutUntil = {{ session('lockout_until') }};
            localStorage.setItem('admin_lockout_until', serverLockoutUntil);
        @endif
    </script>

</body>

</html>

@vite(['resources/js/admin/login/validation.js'])
@vite(['resources/js/admin/pages/alert.js'])