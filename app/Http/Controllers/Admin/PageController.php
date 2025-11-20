<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\History;
use App\Models\Admin\Trip;
use App\Models\Admin\MunicipalityCost;
use App\Models\Admin\Driver;
use App\Models\Admin\Company;
use App\Models\Admin\Municipality;
use Carbon\Carbon; 


class PageController extends Controller
{
    
    public function dashboard()
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear  = $now->year;

        // === Current month counts ===
        $backloadTrips = Trip::where('status', 'Backload')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        // === Current month counts ===
        $totalTrips = Trip::where('status', '!=', 'Archived')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        $pendingTrips = Trip::where('status', 'Pending')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        $completedTrips = Trip::where('status', 'Completed')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        $cancelledTrips = Trip::where('status', 'Cancelled')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();


        $lastMonth = $now->copy()->subMonth();
        $lmMonth   = $lastMonth->month;
        $lmYear    = $lastMonth->year;

        // === Last month counts ===
        $lastMonthBackload = Trip::where('status', 'Backload')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        $lastMonthTotal = Trip::where('status', '!=', 'Archived')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        $lastMonthPending = Trip::where('status', 'Pending')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        $lastMonthCompleted = Trip::where('status', 'Completed')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        $lastMonthCancelled = Trip::where('status', 'Cancelled')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        // === Percentage change (safe from division by zero) ===

        $backloadPercent = ($lastMonthBackload == 0)
            ? ($backloadTrips > 0 ? 100 : 0)
            : round((($backloadTrips - $lastMonthBackload) / $lastMonthBackload) * 100, 1);

        $totalPercent = ($lastMonthTotal == 0)
            ? ($totalTrips > 0 ? 100 : 0)
            : round((($totalTrips - $lastMonthTotal) / $lastMonthTotal) * 100, 1);

        $pendingPercent = ($lastMonthPending == 0)
            ? ($pendingTrips > 0 ? 100 : 0)
            : round((($pendingTrips - $lastMonthPending) / $lastMonthPending) * 100, 1);

        $completedPercent = ($lastMonthCompleted == 0)
            ? ($completedTrips > 0 ? 100 : 0)
            : round((($completedTrips - $lastMonthCompleted) / $lastMonthCompleted) * 100, 1);

        $cancelledPercent = ($lastMonthCancelled == 0)
            ? ($cancelledTrips > 0 ? 100 : 0)
            : round((($cancelledTrips - $lastMonthCancelled) / $lastMonthCancelled) * 100, 1);

        // === Chart data (for full year) ===
        $completedByMonth = Trip::selectRaw('MONTH(schedule) as month, COUNT(*) as total')
            ->where('status', 'Completed')
            ->whereYear('schedule', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $cancelledByMonth = Trip::selectRaw('MONTH(schedule) as month, COUNT(*) as total')
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

        // === Pass to view ===
        return view('admin.pages.dashboard', compact(
            'totalTrips', 'pendingTrips', 'completedTrips', 'cancelledTrips', 'backloadTrips',
            'lastMonthTotal', 'lastMonthPending', 'lastMonthCompleted', 'lastMonthCancelled', 'lastMonthBackload',
            'totalPercent', 'pendingPercent', 'completedPercent', 'cancelledPercent', 'backloadPercent',
            'completedData', 'cancelledData',
            'lastMonth'
        ));

    }


        public function company()
    {
        $companies = Company::where('status', 'active')->get();
        $usedMunicipalities = $companies->pluck('municipality')->toArray();

        $cities = Municipality::where('type', 'City')->get();
        $municipalities = Municipality::where('type', 'Municipality')->get();

        return view('admin.pages.company', compact('companies', 'cities', 'municipalities', 'usedMunicipalities'));
    }


        public function companyList()
    {
        $companies = Company::where('status', 'active')->get();
        return response()->json($companies);
    }


        public function driver()
    {
        $drivers = Driver::with('company')
            ->orderBy('created_at', 'desc')
            ->get();

        $companies = Company::orderBy('name')->get();

        return view('admin.pages.driver', compact('drivers', 'companies'));
    }


        public function trips()
    {
        $startOfMonth = Carbon::now()->startOfMonth(); 
        $today = Carbon::today();

        $trips = Trip::where(function ($query) use ($startOfMonth) {
                    $query->whereIn('status', ['Pending', 'Backload', 'In-transit'])
                        ->orWhere(function ($sub) use ($startOfMonth) {
                            $sub->whereIn('status', ['Completed', 'Cancelled'])
                                ->where('schedule', '>=', $startOfMonth);
                        });
                })
                ->orderByRaw("CASE 
                                WHEN status = 'Pending' THEN 0 
                                WHEN status = 'Backload' THEN 1
                                WHEN status = 'In-transit' THEN 2
                                WHEN status = 'Completed' THEN 3
                                WHEN status = 'Cancelled' THEN 4 
                            END")
                ->orderBy('id', 'desc')
                ->get();

        $trips->each(function($trip) use ($today) {
            $trip->arrival_passed = $trip->arrival_date 
                ? Carbon::parse($trip->arrival_date)->lt($today) 
                : false;
        });

        $companies = Company::where('status', 'active')->get();
        $municipalities = $companies->pluck('municipality')->unique();

        return view('admin.pages.trips', compact('trips', 'companies', 'municipalities'));
    }



        public function history(Request $request)
    {
        $histories = Trip::whereIn('status', ['completed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pages.history', compact('histories'));
    }

        public function getDelivery_Attempts($transactionId)
    {
        $attempts = \App\Models\Admin\DeliveryAttempt::whereHas('trip', function ($q) use ($transactionId) {
            $q->where('transactionId', $transactionId);
        })
        ->orderBy('created_at', 'asc')
        ->get(['attempt', 'schedule_date', 'date_status', 'driver', 'remarks', 'status', 'proof_photo', 'assigned_date']);

        return response()->json($attempts);
    }
}