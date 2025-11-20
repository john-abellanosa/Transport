<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Trip;
use App\Models\Admin\DeliveryAttempt;
use App\Models\AdminNotification;
use App\Models\CompanyNotifications;
use Carbon\Carbon;

class DriverTripsController extends Controller
{
    public function updateStatus(Request $request)
    {
        $request->validate([
            'transactionId' => 'required|string',
            'status'        => 'required|string|in:Completed,Cancelled,Backload',
            'remarks'       => 'nullable|string|max:255',
            'reason'        => 'nullable|string|max:255',
            'proof_photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $trip = Trip::where('transactionId', $request->transactionId)->first();

        if (!$trip) {
            return response()->json(['success' => false, 'message' => 'Trip not found']);
        }

        // Handle proof photo upload
        if ($request->hasFile('proof_photo')) {
            $photo = $request->file('proof_photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/proof_photos'), $filename);
            $trip->proof_photo = 'uploads/proof_photos/' . $filename;
        }

        // Count existing delivery attempts for this trip
        $attemptCount = DeliveryAttempt::where('trip_id', $trip->id)->count();
        $nextAttempt = $attemptCount + 1;

        // Get Philippine time now
        $nowPHT = \Carbon\Carbon::now('Asia/Manila');

        // Determine new status logic
        if ($request->status === 'Backload') {
            $trip->remarks = $request->remarks; // keep driver’s input
            $trip->arrival_date = $nowPHT;

            if ($nextAttempt >= 3) {
                $trip->status = 'Cancelled';
            } else {
                $trip->status = 'Backload';
            }
        } elseif ($request->status === 'Completed') {
            $trip->status = 'Completed';
            $trip->arrival_date = $nowPHT;
        } elseif ($request->status === 'Cancelled') {
            $trip->status = 'Cancelled';
            $trip->remarks = $request->reason;
            $trip->arrival_date = $nowPHT;
        }

        // Make sure assigned_date exists in PHT
        if (!$trip->assigned_date) {
            $trip->assigned_date = $nowPHT;
        }

        $trip->save();

        // Record delivery attempt
        DeliveryAttempt::create([
            'trip_id'       => $trip->id,
            'attempt'       => $nextAttempt,
            'schedule_date' => $trip->schedule ?? $nowPHT,
            'date_status'   => $nowPHT,
            'driver'        => $trip->driver ?? (auth()->user()->name ?? 'Unknown Driver'),
            'remarks'       => $trip->remarks ?? $request->remarks,
            'status'        => $trip->status,
            'proof_photo'   => $trip->proof_photo,
            'assigned_date' => $trip->assigned_date, // save assigned_date in delivery attempts
        ]);

        $message = '';
        $title = '';

        if ($trip->status === 'Completed') {
            $title = 'Delivery Completed';
            $message = "Trip {$trip->transactionId} has been successfully delivered to {$trip->destination}.";
        } elseif ($trip->status === 'Cancelled') {
            $title = 'Cancelled Trip';

            if ($nextAttempt >= 3) { 
                $message = "Trip {$trip->transactionId} has been automatically cancelled after 3 unsuccessful delivery attempts.";
            } else { 
                $message = "Trip {$trip->transactionId} has been cancelled by driver {$trip->driver}. Driver's remarks: {$trip->remarks}.";
            }
        } elseif ($trip->status === 'Backload') {
            $title = 'Backloaded Trip';
            $message = "Trip {$trip->transactionId} was not delivered and is now backloaded. Attempt #{$nextAttempt}. Driver's remarks: '{$trip->remarks}'.";
        }

        if ($message) {
            AdminNotification::create([
                'title'      => $title,
                'message'    => $message, 
            ]);

            CompanyNotifications::create([
                'company_name' => $trip->company,
                'title'        => $title,
                'message'      => $message,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $trip->status === 'Cancelled'
                ? 'Trip automatically cancelled after 3 backload attempts.'
                : 'Trip updated and delivery attempt recorded successfully.'
        ]);
    }
}
