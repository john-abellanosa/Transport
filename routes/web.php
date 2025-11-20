<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\TripsController;
use App\Http\Controllers\Admin\AdminNotificationController;

use App\Http\Controllers\Company\CompanyAuthController;
use App\Http\Controllers\Company\CompanyPageController;
use App\Http\Controllers\Company\CompanyDriverController;
use App\Http\Controllers\Company\CompanyTripsController;
use App\Http\Controllers\Company\CompanyNotificationController;

use App\Http\Controllers\Driver\DriverTripsController;
use App\Http\Controllers\Driver\DriverPageController;
use App\Http\Controllers\Driver\DriverAuthController;
use App\Http\Controllers\Driver\DriverNotificationController;

       
    Route::middleware(['redirect.if.authenticated:admin', 'prevent.back'])->group(function () {
        Route::get('/', function () {
        return view('admin.auth.login');
        })->name('admin.auth.login');

        Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
    });


    Route::middleware(['check.session:admin', 'prevent.back'])->group(function () {
        Route::get('/admin/dashboard', [PageController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/company', [PageController::class, 'company'])->name('admin.company');
        Route::get('/admin/driver', [PageController::class, 'driver'])->name('admin.driver');
        Route::get('/admin/trips', [PageController::class, 'trips'])->name('admin.trips');
        Route::get('/admin/history', [PageController::class, 'history'])->name('admin.history');

        // Notification 
        Route::get('/admin/notifications', [AdminNotificationController::class, 'fetch'])->name('notifications.fetch');
        Route::post('/admin/notifications/mark-read', [AdminNotificationController::class, 'markAsRead'])->name('notifications.markRead');

        // Trips 
        Route::post('/trips/store', [TripsController::class, 'store'])->name('trips.store');
        Route::delete('/admin/trips/{id}', [TripsController::class, 'destroy'])->name('trips.destroy');
        Route::put('/admin/trips/{id}', [TripsController::class, 'updateTrip'])->name('trips.update');
       
        Route::get('/admin/municipality-cost', [TripsController::class, 'getMunicipalityCost'])->name('municipality.cost');




        // Archived Trips
        Route::get('/admin/trips/archive', [TripsController::class, 'archive'])->name('admin.trips.archive');
        Route::post('/admin/trips/{id}/restore', [TripsController::class, 'restore'])->name('admin.trips.restore');
        Route::post('/admin/trips/check', [TripsController::class, 'checkTripExists'])->name('admin.checkTripExists'); 
       

        Route::prefix('admin')->name('admin.')->group(function () {
                Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
                Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');
                Route::put('companies/{id}', [CompanyController::class, 'update'])->name('companies.update');
                Route::delete('companies/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy');

                // Duplicate checks
                Route::post('companies/check-municipality', [CompanyController::class, 'checkMunicipality'])->name('companies.checkMunicipality');
                Route::post('companies/check-name', [CompanyController::class, 'checkName'])->name('companies.checkName');
                Route::post('companies/check-main', [CompanyController::class, 'checkMainCompany'])->name('companies.checkMain');
                Route::post('companies/check-email', [CompanyController::class, 'checkEmail'])->name('companies.checkEmail');
                Route::post('companies/check-contact', [CompanyController::class, 'checkContact'])->name('companies.checkContact');

                Route::get('delivery-attempts/{transactionId}', [PageController::class, 'getDelivery_Attempts'])->name('admin.delivery.attempts');
                
                // Archived companies
                Route::get('companies/archive', [CompanyController::class, 'archive'])->name('companies.archive');
                Route::post('companies/{id}/restore', [CompanyController::class, 'restore'])->name('companies.restore');
            });

            Route::get('/admin/companies/list', [CompanyController::class, 'companyList'])->name('admin.companies.list');

            // Logout
            Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
        });

    Route::prefix('company')->group(function () {
        Route::middleware(['redirect.if.authenticated:company', 'prevent.back'])->group(function () {
            Route::get('login', [CompanyAuthController::class, 'login'])->name('company.login');
            Route::post('login', [CompanyAuthController::class, 'authenticate'])->name('company.authenticate');

            // Forgot password
            Route::get('forgot-password', [CompanyAuthController::class, 'forgotPassword'])->name('company.password.forgot');
            Route::post('forgot-password', [CompanyAuthController::class, 'sendOtp'])->name('company.password.sendOtp');

            // OTP form page
            Route::get('verify-otp', [CompanyAuthController::class, 'showOtpForm'])->name('company.otpForm');
            Route::post('verify-otp', [CompanyAuthController::class, 'verifyOtp'])->name('company.password.verifyOtp');

            // ✅ New: Resend OTP route
            Route::post('resend-otp', [CompanyAuthController::class, 'resendOtp'])->name('company.password.resendOtp');
        });

        Route::middleware(['check.session:company', 'prevent.back'])->group(function () {
            Route::get('dashboard', [CompanyPageController::class, 'dashboard'])->name('company.dashboard');
            Route::get('driver', [CompanyPageController::class, 'driver'])->name('company.driver');
            Route::get('trips', [CompanyPageController::class, 'trips'])->name('company.trips');
            Route::get('history', [CompanyPageController::class, 'history'])->name('company.history');
            Route::get('delivery-attempts/{transactionId}', [CompanyPageController::class, 'getDeliveryAttempts'])
                ->name('company.delivery.attempts');

            Route::get('/notifications/fetch', [CompanyNotificationController::class, 'fetch_company_notifications']);
            Route::post('/notifications/mark-as-read', [CompanyNotificationController::class, 'mark_As_Read']);

            Route::post('drivers/store', [CompanyDriverController::class, 'store'])->name('drivers.store');
            Route::put('drivers/{id}', [CompanyDriverController::class, 'update'])->name('drivers.update');
            Route::delete('drivers/{id}', [CompanyDriverController::class, 'destroy'])->name('drivers.destroy');
            Route::get('archive/driver', [CompanyDriverController::class, 'driverArchive'])->name('company.driver.archive');
            Route::post('driver/restore/{id}', [CompanyDriverController::class, 'restoreDriver'])->name('company.driver.restore');


            Route::post('trips/assign-driver', [CompanyTripsController::class, 'assignDriver'])->name('company.trips.assignDriver');

            Route::get('change-password', [CompanyAuthController::class, 'changePassword'])->name('company.changePassword');
            Route::post('update-password', [CompanyAuthController::class, 'updatePassword'])->name('company.updatePassword');

            Route::get('back', [CompanyAuthController::class, 'CompanyBackLogin'])->name('company.backLogin');
            Route::post('logout', [CompanyAuthController::class, 'logout'])->name('company.logout');
        });
    });


    Route::middleware(['redirect.if.authenticated:driver', 'prevent.back'])->group(function () {
        Route::get('/driver/login', [DriverPageController::class, 'login'])->name('driver.login');
        Route::post('/driver/login', [DriverAuthController::class, 'authenticates'])->name('driver.authenticate');
    });

    Route::middleware(['check.session:driver', 'prevent.back'])->group(function () {
        Route::get('/driver/dashboard', [DriverPageController::class, 'dashboard'])->name('driver.dashboard');
        Route::get('/driver/trips', [DriverPageController::class, 'trips'])->name('driver.trips');
        Route::get('/driver/history', [DriverPageController::class, 'history'])->name('driver.history');
        Route::get('/driver/delivery-attempts/{transactionId}', [DriverPageController::class, 'getDeliveryAttemptsDriver'])
        ->name('driver.delivery.attempts');

        // Notification 
        Route::get('/driver/notifications/fetch', [DriverNotificationController::class, 'fetch']);
        Route::post('/driver/notifications/mark-read', [DriverNotificationController::class, 'markAsRead']);

        // Update trip status
        Route::post('/driver/trips/update-status', [DriverTripsController::class, 'updateStatus'])->name('driver.trips.updateStatus');

        // Password management
        Route::get('/driver/change-password', [DriverAuthController::class, 'changePassword'])->name('driver.changePassword');
        Route::post('/driver/update-password', [DriverAuthController::class, 'updatePassword'])->name('driver.updatePassword');

        // Logout
        Route::post('/driver/logout', [DriverAuthController::class, 'logout'])->name('driver.logout');
    });

    Route::get('/driver/forgot-password', [DriverAuthController::class, 'showForgotPasswordForm'])->name('driver.forgotPassword');
    Route::post('/driver/send-otp', [DriverAuthController::class, 'sendOtp'])->name('driver.sendOtp');
    Route::get('/driver/verify-otp', [DriverAuthController::class, 'showVerifyOtpForm'])->name('driver.verifyOtp');
    Route::post('/driver/verify-otp', [DriverAuthController::class, 'verifyOtp'])->name('driver.verifyOtp.submit');
    Route::post('/driver/resend-otp', [DriverAuthController::class, 'resendOtp'])->name('driver.resendOtp');
    Route::get('/driver/back', [DriverAuthController::class, 'backLogin'])->name('driver.backLogin');

