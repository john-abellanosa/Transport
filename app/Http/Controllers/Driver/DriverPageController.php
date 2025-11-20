<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Driver;
use App\Models\Admin\Company;
use App\Models\Admin\Trip; 
use App\Models\Admin\DeliveryAttempt; 
use Carbon\Carbon;

class DriverPageController extends Controller
{
    public function login () 
    {
        return view('driver.auth.login');
    }
    public function dashboard()
    {
        $driverName = session('driver_name');
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear  = $now->year;

       
        $totalTrips = Trip::where('driver', $driverName)
            ->where('status', '!=', 'Archived')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        $completedTrips = Trip::where('driver', $driverName)
            ->where('status', 'Completed')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        $cancelledTrips = Trip::where('driver', $driverName)
            ->where('status', 'Cancelled')
            ->whereMonth('schedule', $currentMonth)
            ->whereYear('schedule', $currentYear)
            ->count();

        
        $lastMonth = $now->copy()->subMonth();
        $lmMonth   = $lastMonth->month;
        $lmYear    = $lastMonth->year;

        $lastMonthTotal = Trip::where('driver', $driverName)
            ->where('status', '!=', 'Archived')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        $lastMonthCompleted = Trip::where('driver', $driverName)
            ->where('status', 'Completed')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        $lastMonthCancelled = Trip::where('driver', $driverName)
            ->where('status', 'Cancelled')
            ->whereMonth('schedule', $lmMonth)
            ->whereYear('schedule', $lmYear)
            ->count();

        
        $totalPercent = ($lastMonthTotal == 0)
            ? ($totalTrips > 0 ? 100 : 0)
            : round((($totalTrips - $lastMonthTotal) / $lastMonthTotal) * 100, 1);

        $completedPercent = ($lastMonthCompleted == 0)
            ? ($completedTrips > 0 ? 100 : 0)
            : round((($completedTrips - $lastMonthCompleted) / $lastMonthCompleted) * 100, 1);

        $cancelledPercent = ($lastMonthCancelled == 0)
            ? ($cancelledTrips > 0 ? 100 : 0)
            : round((($cancelledTrips - $lastMonthCancelled) / $lastMonthCancelled) * 100, 1);

        
        $completedByMonth = Trip::selectRaw('MONTH(schedule) as month, COUNT(*) as total')
            ->where('driver', $driverName)
            ->where('status', 'Completed')
            ->whereYear('schedule', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $cancelledByMonth = Trip::selectRaw('MONTH(schedule) as month, COUNT(*) as total')
            ->where('driver', $driverName)
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
        return view('driver.pages.dashboard', compact(
            'totalTrips', 'completedTrips', 'cancelledTrips',
            'lastMonthTotal', 'lastMonthCompleted', 'lastMonthCancelled',
            'totalPercent', 'completedPercent', 'cancelledPercent',
            'completedData', 'cancelledData', 'lastMonth'
        ));
    }


    public function trips()
    {
        $companyName   = session('driver_name'); 
        $startOfMonth  = Carbon::now()->startOfMonth();

        $trips = Trip::where('driver', $companyName)
            ->where(function ($query) use ($startOfMonth) {
                $query->where('status', 'In-transit') 
                    ->orWhere(function ($sub) use ($startOfMonth) {
                        $sub->whereIn('status', ['Completed', 'Cancelled'])
                            ->where('schedule', '>=', $startOfMonth); 
                    });
            })
            ->orderByRaw("CASE 
                            WHEN status = 'In-transit' THEN 0 
                            WHEN status = 'Completed' THEN 1 
                            WHEN status = 'Cancelled' THEN 2 
                        END")
            ->orderBy('id', 'desc')
            ->get();

        $drivers = Driver::where('name', $companyName)->get();

        return view('driver.pages.trips', compact('trips', 'drivers'));
    }


    public function history()
    {
        $companyName = session('driver_name');


        $trips = Trip::where('driver', $companyName)
            ->whereIn('status', ['Completed', 'Cancelled'])
            ->orderBy('schedule', 'desc')
            ->get();

        $driver = Driver::where('name', $companyName)->first();

        return view('driver.pages.history', compact('trips', 'driver'));
    }

    public function getDeliveryAttemptsDriver($transactionId)
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
