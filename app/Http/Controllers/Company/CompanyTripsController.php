<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Trip;
use App\Models\Admin\Driver;
use App\Models\AdminNotification;
use App\Models\DriverNotifications;

class CompanyTripsController extends Controller
{
        public function assignDriver(Request $request)
    {
        $request->validate([
            'trip_id'   => 'required|exists:trips,id',
            'driver_id' => 'required|exists:drivers,id',
        ]);

        $trip = Trip::findOrFail($request->trip_id);
        $driver = Driver::findOrFail($request->driver_id);

        $trip->driver = $driver->name;
        $trip->status = 'In-transit';
        $trip->assigned_date = \Carbon\Carbon::now('Asia/Manila'); // Philippine time
        $trip->save();

        AdminNotification::create([
            'title' => 'New Trip Assigned',
            'message' => "Trip {$trip->transactionId} has been assigned to driver {$driver->name} and is now In Transit.", 
        ]);

        DriverNotifications::create([
            'driver_name' => $driver->name,
            'title' => 'New Trip Assigned',
            'message' => "You have been assigned to Trip {$trip->transactionId}. Please ensure on-time delivery",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Driver {$driver->name} assigned to trip {$trip->transactionId}."
        ]);
    }
}
