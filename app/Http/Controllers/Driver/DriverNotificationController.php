<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverNotifications;  

class DriverNotificationController extends Controller
{
    public function fetch()
    {
        $driverName = session('driver_name'); 

        $notifications = DriverNotifications::where('driver_name', $driverName)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount'   => $notifications->where('status', 'new')->count(),
        ]);
    }

    public function markAsRead(Request $request)
    {
        DriverNotifications::whereIn('id', $request->ids)
            ->update(['status' => 'read']);

        return response()->json(['success' => true]);
    }
}
