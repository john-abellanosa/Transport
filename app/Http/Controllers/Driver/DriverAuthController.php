<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin\Driver;
use App\Mail\PasswordResetOtpMail;
use Carbon\Carbon;

class DriverAuthController extends Controller
{

    public function authenticates(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'username.email' => 'Please enter a valid email address.',
        ]);

        $throttleKey = 'driver|' . $request->ip();
        $maxAttempts = 3;
        $decaySeconds = 60;

        if ($request->query('clear_attempts') == '1') {
            RateLimiter::clear($throttleKey);
            return redirect()->route('driver.login');
        }

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            if ($seconds <= 0) {
                RateLimiter::clear($throttleKey);
            } else {
                $lockoutTime = now()->addSeconds($seconds)->timestamp;
            
                return back()
                    ->withErrors(['lockout' => "Too many login attempts. Please try again in $seconds seconds."])
                    ->withInput($request->only('username'))
                    ->with('lockout_until', $lockoutTime);
            }
        }

        $driver = Driver::whereRaw('BINARY email = ?', [$request->username])->first();

        if (!$driver) {
            RateLimiter::hit($throttleKey, $decaySeconds);
            $currentAttempts = RateLimiter::attempts($throttleKey);
            $attemptsLeft = $maxAttempts - $currentAttempts;
            
            if ($attemptsLeft <= 0) {
                $lockoutTime = now()->addSeconds($decaySeconds)->timestamp;
                return back()
                    ->withErrors(['lockout' => "Too many login attempts. Please try again in $decaySeconds seconds."])
                    ->withInput($request->only('username'))
                    ->with('lockout_until', $lockoutTime);
            }
            
            return redirect()->back()->with('invalid', 'Invalid Credentials');
        }

        // ✅ FIXED: Block inactive drivers (case-insensitive + numeric check)
        if (strtolower($driver->status) === 'inactive' || $driver->status == 0) {
            return back()
                ->with('error', 'This account is no longer active. Please reach out to your company.')
                ->withInput($request->only('username'));
        }

        if (!Hash::check($request->password, $driver->password)) {
            RateLimiter::hit($throttleKey, $decaySeconds);
            $currentAttempts = RateLimiter::attempts($throttleKey);
            $attemptsLeft = $maxAttempts - $currentAttempts;
            
            if ($attemptsLeft <= 0) {
                $lockoutTime = now()->addSeconds($decaySeconds)->timestamp;
                return back()
                    ->withErrors(['lockout' => "Too many login attempts. Please try again in $decaySeconds seconds."])
                    ->withInput($request->only('username'))
                    ->with('lockout_until', $lockoutTime);
            }
            
            return redirect()->back()->with('invalid', 'Invalid Credentials');
        }

        RateLimiter::clear($throttleKey);

        session([
            'driver_id'    => $driver->id,
            'driver_name'  => $driver->name,
            'driver_email' => $driver->email,
        ]);

        if ($driver->is_temporary_password) {
            return redirect()->route('driver.changePassword')
                ->with('success', 'Please create a new password to secure your account.');
        }

        return redirect()->route('driver.dashboard');
    }


    public function changePassword()
    {
        return view('driver.auth.change-password');
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $driver = Driver::findOrFail(session('driver_id'));
        $driver->password = Hash::make($request->password);
        $driver->is_temporary_password = false;
        $driver->save();

        // ✅ Clear session so driver must log in again
        session()->forget(['driver_id', 'driver_name', 'driver_email']);
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('driver.login')
                        ->with('success', 'Password updated successfully! Please log in again.');
    }


    public function showForgotPasswordForm()
    {
        return view('driver.auth.forgot-password');
    }


    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $driver = Driver::where('email', $request->email)->first();

        if (!$driver) {
            return back()->with('error', 'No account found with this email.')->withInput();
        }

        $now = Carbon::now();

        // If OTP still valid, reuse it
        if ($driver->otp_expires_at && $now->lt(Carbon::parse($driver->otp_expires_at))) {
            $otp = $driver->otp_code;
            $expiresAt = Carbon::parse($driver->otp_expires_at);
        } else {
            $otp = rand(100000, 999999);
            $expiresAt = $now->addMinutes(5);

            $driver->update([
                'otp_code' => $otp,
                'otp_expires_at' => $expiresAt,
            ]);

            Mail::to($driver->email)->send(new PasswordResetOtpMail($otp, '5 minutes'));
        }

        // ✅ Ensure Carbon instance before timestamp
        session([
            'otp_email' => $driver->email,
            'otp_expires_at' => Carbon::parse($expiresAt)->timestamp,
        ]);

        return redirect()->route('driver.verifyOtp')
                        ->with('success', 'An OTP has been sent to your email.')
                        ->with('expires_at', Carbon::parse($expiresAt)->timestamp);
    }


    public function resendOtp(Request $request)
    {
        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('driver.login')->with('error', 'Session expired. Please log in again.');
        }

        $driver = Driver::where('email', $email)->first();

        if (!$driver) {
            return redirect()->route('driver.login')->with('error', 'Email not found.');
        }

        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(5);

        $driver->update([
            'otp_code' => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        // ✅ Convert to Carbon instance before getting timestamp
        session(['otp_expires_at' => Carbon::parse($expiresAt)->timestamp]);

        Mail::to($driver->email)->send(new PasswordResetOtpMail($otp, '5 minutes'));

        return back()->with('success', 'A new OTP has been sent to your email.');
    }


    public function showVerifyOtpForm()
    {
        $email = session('otp_email') ?? '';
        return view('driver.auth.verify-otp', compact('email'));
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp'   => 'required|digits:6',
            'email' => 'required|email',
        ]);

        $driver = Driver::where('email', $request->email)
                        ->where('otp_code', $request->otp)
                        ->first();

        if (!$driver) {
            return back()->with('error', 'Invalid OTP. Please check your email');
        }

        if ($driver->otp_expires_at && Carbon::now()->greaterThan($driver->otp_expires_at)) {
            return back()->with('error', 'OTP has expired.');
        }

        // OTP valid → clear it
        $driver->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        session([
            'driver_id' => $driver->id,
            'driver_email' => $driver->email,
            'driver_name' => $driver->name,
        ]);

        return redirect()->route('driver.changePassword')
                         ->with('success', 'OTP verified. You can now change your password.');
    }


    public function logout(Request $request)
    {
        $request->session()->forget(['driver_id', 'driver_name', 'driver_email']);
        $request->session()->regenerateToken();

        return redirect()->route('driver.login');
    }


    public function backLogin(Request $request)
    {
        $request->session()->forget(['driver_id', 'driver_name', 'driver_email']);
        $request->session()->regenerateToken();

        return redirect()->route('driver.login');
    }
}