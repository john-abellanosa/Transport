<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Admin\Driver;
use App\Models\Admin\Company;
use App\Models\Admin\Trip;
use App\Models\Admin\DeliveryAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanyPageController extends Controller
{

    public function dashboard()
    {
        $companyName = session('company_name');
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear  = $now->year;

        $backloadTrips = Trip::where('company', $companyName)
            ->where('status', 'Backload')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        $totalTrips = Trip::where('company', $companyName)
            ->where('status', '!=', 'Archived')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        $availableTrips = Trip::where('company', $companyName)
            ->where('status', 'Pending')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        $completedTrips = Trip::where('company', $companyName)
            ->where('status', 'Completed')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        $cancelledTripsCount = Trip::where('company', $companyName)
            ->where('status', 'Cancelled')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        // === Last month counts ===
        $lastMonth = $now->copy()->subMonth();
        $lmMonth   = $lastMonth->month;
        $lmYear    = $lastMonth->year;

        $lastMonthBackload = Trip::where('company', $companyName)
            ->where('status', 'Backload')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        $lastMonthTotal = Trip::where('company', $companyName)
            ->where('status', '!=', 'Archived')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        $lastMonthAvailable = Trip::where('company', $companyName)
            ->where('status', 'Pending')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        $lastMonthCompleted = Trip::where('company', $companyName)
            ->where('status', 'Completed')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        $lastMonthCancelled = Trip::where('company', $companyName)
            ->where('status', 'Cancelled')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        // === Percentage change (safe from division by zero, mirror Admin behavior) ===
        $backloadPercent = ($lastMonthBackload == 0)
            ? ($backloadTrips > 0 ? 100 : 0)
            : round((($backloadTrips - $lastMonthBackload) / $lastMonthBackload) * 100, 1);

        $totalPercent = ($lastMonthTotal == 0)
            ? ($totalTrips > 0 ? 100 : 0)
            : round((($totalTrips - $lastMonthTotal) / $lastMonthTotal) * 100, 1);

        $availablePercent = ($lastMonthAvailable == 0)
            ? ($availableTrips > 0 ? 100 : 0)
            : round((($availableTrips - $lastMonthAvailable) / $lastMonthAvailable) * 100, 1);

        $completedPercent = ($lastMonthCompleted == 0)
            ? ($completedTrips > 0 ? 100 : 0)
            : round((($completedTrips - $lastMonthCompleted) / $lastMonthCompleted) * 100, 1);

        $cancelledPercent = ($lastMonthCancelled == 0)
            ? ($cancelledTripsCount > 0 ? 100 : 0)
            : round((($cancelledTripsCount - $lastMonthCancelled) / $lastMonthCancelled) * 100, 1);

        // === Chart data (for full year, company-scoped) ===
        $completedByMonth = Trip::selectRaw('MONTH(schedule) as month, COUNT(*) as total')
            ->where('company', $companyName)
            ->where('status', 'Completed')
            ->whereYear('schedule', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $cancelledByMonth = Trip::selectRaw('MONTH(schedule) as month, COUNT(*) as total')
            ->where('company', $companyName)
            ->where('status', 'Cancelled')
            ->whereYear('schedule', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $months = range(1, 12);
        $completedData = [];
        $cancelledData = [];

        foreach ($months as $month) {
            $completedData[] = $completedByMonth[$month] ?? 0;
            $cancelledData[] = $cancelledByMonth[$month] ?? 0;
        }

        // === Return to view ===
        return view('company.pages.dashboard', compact(
            'totalTrips', 'availableTrips', 'completedTrips', 'cancelledTripsCount', 'backloadTrips',
            'lastMonthTotal', 'lastMonthAvailable', 'lastMonthCompleted', 'lastMonthCancelled', 'lastMonthBackload',
            'totalPercent', 'availablePercent', 'completedPercent', 'cancelledPercent', 'backloadPercent',
            'completedData', 'cancelledData', 'lastMonth'
        ));
    }



    public function history()
    {
        $companyName = session('company_name');

        $trips = Trip::where('company', $companyName)
            ->whereIn('status', ['Completed', 'Cancelled'])
            ->orderBy('id', 'desc')
            ->get();

        return view('company.pages.history', compact('trips'));
    }

 public function driver()
{
    $companyId = session('company_id');

    $drivers = Driver::with('company')
        ->where('company_id', $companyId)
        ->where('status', 'active') 
        ->latest()
        ->get();

    $companies = Company::where('id', $companyId)->get();

    return view('company.pages.driver', compact('drivers', 'companies'));
}


    public function trips()
    {
        $companyName = session('company_name');
        $startOfMonth = Carbon::now()->startOfMonth();

        $trips = Trip::where('company', $companyName)
            ->where(function ($query) use ($startOfMonth) {
                $query->whereIn('status', ['Pending', 'Backload', 'In-transit'])
                      ->orWhere(function ($sub) use ($startOfMonth) {
                          $sub->whereIn('status', ['Completed', 'Cancelled'])
                              ->where('schedule', '>=', $startOfMonth);
                      });
            })
            ->orderByRaw("
                CASE 
                    WHEN status = 'Pending' THEN 0
                    WHEN status = 'Backload' THEN 1
                    WHEN status = 'In-transit' THEN 2
                    WHEN status = 'Completed' THEN 3
                    WHEN status = 'Cancelled' THEN 4
                    ELSE 4
                END
            ")
            ->orderByDesc('id')
            ->get();

        $drivers = Driver::where('company_id', session('company_id'))
        ->where('status', 'Active')
        ->get();


        return view('company.pages.trips', compact('trips', 'drivers'));
    }


    public function getDeliveryAttempts($transactionId)
    {
        $attempts = DeliveryAttempt::whereHas('trip', function ($query) use ($transactionId) {
                $query->where('transactionId', $transactionId);
            })
            ->orderBy('created_at', 'asc')
            ->get([
                'attempt', 
                'schedule_date', 
                'date_status', 
                'driver', 
                'remarks', 
                'status', 
                'proof_photo', 
                'assigned_date'
            ]);

        return response()->json($attempts);
    }
}