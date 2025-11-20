<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="icon" href="{{ asset('img/express.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/company/auth/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/company/auth/verify-otp.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
    <style>
        button:disabled {
            background: #b0c4e3;
            cursor: not-allowed;
        }

        .back-link a {
            color: #4a6cf7;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    {{-- ✅ Alerts --}}
    @if (session('success'))
        <div id="success-alert" class="success_alert">
            <strong>Success:</strong> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div id="error-alert" class="error_alert">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    <div class="container">
      
        <div class="left">
            <img src="{{ asset('img/company.png') }}" alt="Company Image"
                onerror="this.src='{{ asset('img/company.png') }}'">
        </div>

 
        <div class="right-section">
            <div class="right-side">
                <img class="logo" src="{{ asset('img/logo.png') }}" alt="Logo">

               
                <div class="login-form">
                    <h1 class="welcome-title">Verify OTP</h1>
                    <p class="welcome-desc">Enter the 6-digit OTP sent to your email:
                        <strong>{{ $email }}</strong></p>

                    <p id="timer" style="font-weight:500; color:#176fba; margin-bottom:15px;">
                        OTP expires in <span id="countdown">05:00</span>
                    </p>

                    <form action="{{ route('company.password.verifyOtp') }}" method="POST" id="otpForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="otp" id="otpHidden">

                        <div class="otp-box">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" maxlength="1" required inputmode="numeric" pattern="[0-9]*"
                                    class="otp-field">
                            @endfor
                        </div>

                        <button type="submit" class="verify-button" id="verifyBtn" disabled>Submit</button>
                    </form>

                    <a href="{{ route('company.login') }}" class="back-link">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
    <script>
       
        const inputs = document.querySelectorAll('.otp-field');
        const hiddenOtp = document.getElementById('otpHidden');
        const verifyBtn = document.getElementById('verifyBtn');

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
               
                input.value = input.value.replace(/[^0-9]/g, '');

              
                if (input.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }

              
                const otp = Array.from(inputs).map(i => i.value).join('');
                hiddenOtp.value = otp;

                
                verifyBtn.disabled = otp.length !== 6;
            });

            input.addEventListener('keydown', (e) => {
                
                if (!/^[0-9]$/.test(e.key) && !['Backspace', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e
                        .key)) {
                    e.preventDefault();
                }

                
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

       
        let expiryTimestamp = {{ session('otp_expires_at', session('expires_at') ?? 'null') }};
        const container = document.querySelector('.login-form');

        const resendForm = document.createElement('form');
        resendForm.action = "{{ route('company.password.resendOtp') }}";
        resendForm.method = "POST";
        resendForm.style.display = "none";
        resendForm.style.marginTop = "15px";
        resendForm.innerHTML = `
    @csrf
    <button type="submit" id="resendBtn" style="
        width:100%;
        padding:12px;
        background:#4a6cf7;
        color:white;
        border:none;
        border-radius:10px;
        font-size:14px;
        font-weight:600;
        cursor:pointer;
        transition: all 0.3s ease;
    ">Resend OTP</button>
`;
        container.appendChild(resendForm);

        if (expiryTimestamp) {
            const countdownElement = document.getElementById('countdown');
            const form = document.getElementById('otpForm');

            const timerInterval = setInterval(() => {
                const now = Math.floor(Date.now() / 1000);
                const remaining = expiryTimestamp - now;

                if (remaining <= 0) {
                    clearInterval(timerInterval);
                    countdownElement.textContent = '00:00';
                    verifyBtn.disabled = true;
                    verifyBtn.textContent = 'OTP Expired';
                    verifyBtn.style.background = '#ccc';
                    form.querySelectorAll('input').forEach(i => i.disabled = true);
                    resendForm.style.display = "block";
                    resendForm.style.animation = "fadeIn 0.5s ease";

                    
                    showErrorAlert('Your OTP has expired. Please request a new one.');
                    return;
                }

                const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
                const seconds = String(remaining % 60).padStart(2, '0');
                countdownElement.textContent = `${minutes}:${seconds}`;
            }, 1000);
        }

     
        function showErrorAlert(message) {
            let existingAlert = document.getElementById('error-alert');

            if (!existingAlert) {
                existingAlert = document.createElement('div');
                existingAlert.id = 'error-alert';
                existingAlert.className = 'error_alert';
                document.body.appendChild(existingAlert);
            }

            existingAlert.innerHTML = `<strong>Error:</strong> ${message}`;
            existingAlert.style.display = 'block';
            existingAlert.style.opacity = '1';

           
            setTimeout(() => {
                existingAlert.style.opacity = '0';
                setTimeout(() => existingAlert.style.display = 'none', 500);
            }, 5000);
        }

        const style = document.createElement('style');
        style.innerHTML = `
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}`;
        document.head.appendChild(style);
    </script>



    @vite(['resources/js/admin/pages/alert.js'])
</body>

</html>
