<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin\Company;
use App\Mail\CompanyOtpMail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class CompanyAuthController extends Controller
{
    public function login()
    {
        return view('company.auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'username.email' => 'The Email field must be a valid email address.',
        ]);

        // Use IP-based throttle key to count all failed attempts from this IP
        $throttleKey = 'company|' . $request->ip();
        $maxAttempts  = 3;
        $decaySeconds = 60;

        // Clear attempts if timer expired naturally
        if ($request->query('clear_attempts') == '1') {
            RateLimiter::clear($throttleKey);
            return redirect()->route('company.login');
        }

        // Check if rate limited
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // If lockout has expired on server, clear it
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

        // Fetch company by email
        $company = Company::whereRaw('BINARY email = ?', [$request->username])->first();

        if (!$company) {
            // Count this as a failed attempt
            RateLimiter::hit($throttleKey, $decaySeconds);
            $currentAttempts = RateLimiter::attempts($throttleKey);
            $attemptsLeft = $maxAttempts - $currentAttempts;
            
            // Check if this was the last attempt
            if ($attemptsLeft <= 0) {
                $lockoutTime = now()->addSeconds($decaySeconds)->timestamp;
                return back()
                    ->withErrors(['lockout' => "Too many login attempts. Please try again in $decaySeconds seconds."])
                    ->withInput($request->only('username'))
                    ->with('lockout_until', $lockoutTime);
            }
            
            return redirect()->back()->with('invalid', 'Invalid Credentials');
        }

        // ✅ FIXED: Block inactive companies (case-insensitive + numeric check)
        if (strtolower($company->status) === 'inactive' || $company->status == 0) {
            return back()
                ->with('error', 'This account is no longer active. Please contact the Admin.')
                ->withInput($request->only('username'));
        }

        // Validate password
        if (!Hash::check($request->password, $company->password)) {
            // Count this as a failed attempt
            RateLimiter::hit($throttleKey, $decaySeconds);
            $currentAttempts = RateLimiter::attempts($throttleKey);
            $attemptsLeft = $maxAttempts - $currentAttempts;
            
            // Check if this was the last attempt
            if ($attemptsLeft <= 0) {
                $lockoutTime = now()->addSeconds($decaySeconds)->timestamp;
                return back()
                    ->withErrors(['lockout' => "Too many login attempts. Please try again in $decaySeconds seconds."])
                    ->withInput($request->only('username'))
                    ->with('lockout_until', $lockoutTime);
            }
            
            return redirect()->back()->with('invalid', 'Invalid Credentials');
        }

        // Login success - clear all attempts
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $fullCompanyName = $company->name;
        if (!empty($company->branch)) {
            $fullCompanyName .= ' - ' . $company->branch;
        }

        session([
            'company_id'    => $company->id,
            'company_name'  => $fullCompanyName,
            'company_email' => $company->email,
        ]);

        // Check if temporary password
        if ($company->is_temporary_password) {
            return redirect()->route('company.changePassword')
                ->with('success', 'Please create a new password to secure your account.');
        }

        return redirect()->route('company.dashboard');
    }




    public function changePassword()
    {
        return view('company.auth.change-password');
    }

   public function updatePassword(Request $request)
{
    $request->validate([
        'password' => 'required|string|min:8|confirmed',
    ]);

    $company = Company::findOrFail(session('company_id'));
    $company->password = bcrypt($request->password);
    $company->is_temporary_password = false;
    $company->save();

    // ✅ Clear session so company must log in again
    session()->forget(['company_id', 'company_name', 'company_email']);
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('company.login')
                     ->with('success', 'Password updated successfully! Please log in again.');
}


    public function forgotPassword()
    {
        return view('company.auth.forgot-password');
    }

public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
  ], [
        'email.email' => 'Please enter a valid email address.',
    ]);
    $company = Company::where('email', $request->email)->first();
    if (!$company) {
        return back()->with('error', 'No account found with this email.')->withInput();
    }


    if ($company->status !== 'active') {
        return back()->with('error', 'Your account is inactive. Please contact support.');
    }

    // Check if OTP already exists and not expired
    $now = now();
    if ($company->otp_expires_at && $now->lt($company->otp_expires_at)) {
    
        $otp = $company->otp;
        $expiresAt = $company->otp_expires_at;
    } else {
       
        $otp = rand(100000, 999999);
        $expiresAt = $now->addMinutes(5);

        $company->otp = $otp;
        $company->otp_expires_at = $expiresAt;
        $company->save();

        Mail::to($company->email)->send(new CompanyOtpMail($otp, $company->name));
    }

  
    session([
        'email' => $company->email,
        'otp_expires_at' => $expiresAt->timestamp,
    ]);

    return redirect()->route('company.otpForm')->with([
        'success' => 'An OTP has been sent to your email.',
        'expires_at' => $expiresAt->timestamp
    ]);
}


public function resendOtp(Request $request)
{
    $email = session('email');

    if (!$email) {
        return redirect()->route('company.login')->with('error', 'Session expired. Please log in again.');
    }

    $company = Company::where('email', $email)->first();

    if (!$company) {
        return redirect()->route('company.login')->with('error', 'Email not found.');
    }

    $otp = rand(100000, 999999);


    $company->otp = $otp;
    $company->otp_expires_at = now()->addMinutes(5);
    $company->save();

    session(['otp_expires_at' => $company->otp_expires_at->timestamp]);

    Mail::to($company->email)->send(new CompanyOtpMail($otp, $company->name));

    return back()->with('success', 'A new OTP has been sent to your email.');
}


    public function showOtpForm()
    {
        $email = session('email') ?? '';
        return view('company.auth.verify-otp', compact('email'));
    }

 public function verifyOtp(Request $request)
{
    $request->validate([
        'otp'   => 'required|digits:6',
        'email' => 'required|email',
    ]);

    $company = Company::where('email', $request->email)
                      ->where('otp', $request->otp)
                      ->first();

    if (!$company) {
        return back()->with('error', 'Invalid OTP. Please check your email');
    }

    // Ensure otp_expires_at is treated as Carbon
    if ($company->otp_expires_at && $company->otp_expires_at->isPast()) {
        return back()->with('error', 'OTP has expired.');
    }

    // OTP is valid → clear OTP fields
    $company->otp = null;
    $company->otp_expires_at = null;
    $company->save();

    // Set session so company can change password
    session([
        'company_id'    => $company->id,
        'company_email' => $company->email,
        'company_name'  => $company->name . (!empty($company->branch) ? ' - '.$company->branch : ''),
    ]);

    return redirect()->route('company.changePassword')
                     ->with('success', 'OTP verified. You can now change your password.');
}


    public function logout(Request $request)
    {
        $request->session()->forget(['company_id', 'company_name', 'company_email']);
        $request->session()->regenerateToken();

        return redirect()->route('company.login');
    }
        public function CompanyBackLogin(Request $request)
    {
        $request->session()->forget(['company_id', 'company_name', 'company_email']);
        $request->session()->regenerateToken();

        return redirect()->route('company.login')
                         ->with('success');
    }
}
