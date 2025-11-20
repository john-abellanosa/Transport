<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminNotification;

class AdminNotificationController extends Controller
{
        public function fetch()
    {
        $notifications = AdminNotification::orderBy('created_at', 'desc')->get();

        $unreadCount = $notifications->where('status', 'new')->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function markAsRead(Request $request)
    {
        AdminNotification::whereIn('id', $request->ids)->update(['status' => 'read']);
        return response()->json(['success' => true]);
    }
}
