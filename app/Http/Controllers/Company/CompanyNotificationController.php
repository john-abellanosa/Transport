<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyNotifications;

class CompanyNotificationController extends Controller
{
    public function fetch_company_notifications(Request $request)
    { 
        $companyName = session('company_name');
 
        $notifications = CompanyNotifications::where('company_name', $companyName)
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = $notifications->where('status', 'new')->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function mark_As_Read(Request $request)
    {
        CompanyNotifications::whereIn('id', $request->ids)->update(['status' => 'read']);
        return response()->json(['success' => true]);
    }
}
