<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Admin\Trip;
use App\Models\Admin\MunicipalityCost;
use App\Models\CompanyNotifications;

class TripsController extends Controller
{
        public function store(Request $request)
    {
        $request->validate([
            'deliveryType' => 'required',
            'vehicleType'  => 'required',
            'clientName'   => 'required|string|max:25',
            'clientNumber' => 'required|digits:10',
            'destination'  => 'required',
            'municipality' => 'required',
            'company'      => 'required',
            'cost'         => 'required|numeric',
            'schedule'     => 'required|date',
            'distance'     => 'nullable|numeric',
        ]);

        try {
            // Parse the schedule from request
            $date = \Carbon\Carbon::parse($request->schedule)
                ->setTimezone('Asia/Manila') // set to Philippine time
                ->format('Y-m-d');

            // Get current time in Philippine time
            $currentTime = \Carbon\Carbon::now('Asia/Manila')->format('H:i:s');

            $scheduleWithTime = $date . ' ' . $currentTime;

            $trip = Trip::create([
                'transactionId' => $this->generateTransactionID(),
                'deliveryType'  => $request->deliveryType,
                'vehicleType'   => $request->vehicleType,
                'clientName'    => $request->clientName,
                'clientNumber'  => $request->clientNumber,
                'destination'   => $request->destination,
                'municipality'  => $request->municipality,
                'company'       => $request->company,
                'distance'      => $request->distance ?? null,
                'cost'          => $request->cost,
                'schedule'      => $scheduleWithTime,
            ]);

            CompanyNotifications::create([
                'company_name' => $request->company,   
                'title'        => 'New Trip Assigned',
                'message'      => "Trip {$trip->transactionId} has been assigned to your company. Please assign a driver immediately to ensure on-time delivery.", 
            ]);

            return redirect()->back()->with('success', 'Trip added successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to add trip: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add trip. Please try again.');
        }
    }



        private function generateTransactionID()
    {
        do { 
            $numbers = str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);
 
            $letters = '';
            for ($i = 0; $i < 4; $i++) {
                $letters .= chr(mt_rand(65, 90));  
            }
 
            $id = str_shuffle($numbers . $letters);

        } while (Trip::where('transactionId', $id)->exists());

        return $id;
    }


        public function updateTrip(Request $request, $id)
    {
        $request->validate([
            'deliveryType' => 'required|string',
            'vehicleType'  => 'required|string',
            'clientName'   => 'required|string|max:25',
            'clientNumber' => 'required|digits:10',
            'destination'  => 'required|string',
            'municipality' => 'required|string',
            'company'      => 'required|string',
            'cost'         => 'required|numeric',
            'schedule'     => 'required|date',
        ]);

        try {
            $trip = Trip::findOrFail($id);
            $trip->update($request->only([
                'deliveryType','vehicleType','clientName','clientNumber',
                'destination','municipality','company','cost','schedule'
            ]));

            return redirect()->back()->with('success', 'Trip updated successfully');
        } catch (\Exception $e) {
            \Log::error('Trip update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Update failed, please try again.');
        }
    }
 
        public function destroy($id)
    {
        try {
            $trip = Trip::findOrFail($id); 
            $trip->status = 'Archived';
            $trip->save();

            CompanyNotifications::create([
                'company_name' => $trip->company,   
                'title'        => 'Trip Archived',
                'message'      => "We're Sorry, the trip {$trip->transactionId} that is asigned to your company has been archived due to unforeseen circumstances",
            ]);

            return response()->json(['success' => 'Trips removed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to archive trips. Please try again.'], 500);
        }
    }



        public function getMunicipalityCost(Request $request)
    {
        $municipality = $request->query('municipality');

        $company = \DB::table('companies')
            ->where('municipality', $municipality)
            ->first();

        if ($company) {
            return response()->json([
                'success' => true,
                'company_name' => $company->name . ($company->branch ? ' - ' . $company->branch : ''),
                'cost' => $company->cost,
            ]);
        } else {
            return response()->json(['success' => false]);
        }
    }


    
        public function archive()
    {
        // Get inactive companies
        $archivedTrips = Trip::where('status', 'Archived')->get();
        return view('admin.pages.trips-archive', compact('archivedTrips'));
    }

        public function restore($id)
    {
        $trips = Trip::findOrFail($id);
        $trips->status = 'Pending';
        $trips->save();

        CompanyNotifications::create([
            'company_name' => $trips->company,   
            'title'        => 'Trip Restored',
            'message'      => "Trip {$trips->transactionId} has been restored. Please assign a driver immediately to ensure on-time delivery.", 
        ]);

        return redirect()->back()->with('success', 'Trip restored successfully!');
    }


        public function checkTripExists(Request $request)
    {
        $matchingTrips = Trip::where([
            'deliveryType' => $request->deliveryType,
            'vehicleType' => $request->vehicleType,
            'clientName' => $request->clientName,
            'clientNumber' => $request->clientNumber,
            'destination' => $request->destination,
            'municipality' => $request->municipality,
            'company' => $request->company,
        ])
        ->whereDate('schedule', $request->schedule)
        ->get();

        $statuses = $matchingTrips->pluck('status')->unique()->toArray();

        // Separate IDs by status
        $archivedIds = $matchingTrips->where('status', 'Archived')->pluck('transactionId')->toArray();
        $pendingIds = $matchingTrips->where('status', 'Pending')->pluck('transactionId')->toArray();

        if (!empty($archivedIds) && !empty($pendingIds)) {
            return response()->json([
                'existsSameDay' => true,
                'status' => 'Archived+Pending',
                'archivedTransactionIds' => $archivedIds,
                'pendingTransactionIds' => $pendingIds,
            ]);
        } elseif (!empty($archivedIds)) {
            return response()->json([
                'existsSameDay' => true,
                'status' => 'Archived',
                'transactionIds' => $archivedIds,
            ]);
        } elseif (!empty($pendingIds)) {
            return response()->json([
                'existsSameDay' => true,
                'status' => 'Pending',
                'transactionIds' => $pendingIds,
            ]);
        }

        return response()->json(['existsSameDay' => false]);
    }
}
