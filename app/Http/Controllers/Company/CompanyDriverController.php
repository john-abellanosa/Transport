<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Driver;
use App\Models\Admin\Company;
use App\Models\AdminNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendDriverDefaultPasswordMail;


class CompanyDriverController extends Controller
{

    public function store(Request $request)
    {
        $companyId = session('company_id');
        if (!$companyId) {
            return redirect()->back()->withErrors('Company not found. Please login again.');
        }

        // Validate only against active drivers
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Driver::where('name', $value)->where('status', 'active')->exists()) {
                        $fail('This name already exists for an active driver.');
                    }
                },
            ],
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (Driver::where('email', $value)->where('status', 'active')->exists()) {
                        $fail('This email already exists for an active driver.');
                    }
                },
            ],
            'number' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if (Driver::where('number', $value)->where('status', 'active')->exists()) {
                        $fail('This number already exists for an active driver.');
                    }
                },
            ],
            'address' => 'required|string|max:255',
        ]);

        $defaultPassword = Str::random(8);

        $driver = Driver::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'address' => $request->address,
            'password' => bcrypt($defaultPassword),
            'is_temporary_password' => true,
            'status' => 'active', // make sure new driver is active by default
        ]);

        // NOtification 
        $company = Company::find($companyId);

        AdminNotification::create([
            'title' => 'New Driver Added',
            'message' => "{$company->name} - {$company->branch} added a new driver: {$driver->name}",
        ]);

        // Send email
        try {
            Mail::to($driver->email)->send(
                new SendDriverDefaultPasswordMail($driver->name, $driver->email, $defaultPassword)
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => 'Driver added successfully but email failed.',
                'error' => $e->getMessage()
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'Added successfully.']);
        }

        return redirect()->back()->with('success', 'Added successfully.');
    }

    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        // Validate only against active drivers (excluding current driver)
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($driver) {
                    if (Driver::where('name', $value)
                            ->where('status', 'active')
                            ->where('id', '!=', $driver->id)
                            ->exists()) {
                        $fail('This name already exists for an active driver.');
                    }
                },
            ],
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($driver) {
                    if (Driver::where('email', $value)
                            ->where('status', 'active')
                            ->where('id', '!=', $driver->id)
                            ->exists()) {
                        $fail('This email already exists for an active driver.');
                    }
                },
            ],
            'number' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($driver) {
                    if (Driver::where('number', $value)
                            ->where('status', 'active')
                            ->where('id', '!=', $driver->id)
                            ->exists()) {
                        $fail('This number already exists for an active driver.');
                    }
                },
            ],
            'address' => 'required|string|max:255',
        ]);

        $driver->update([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'address' => $request->address,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Driver updated successfully!']);
        }

        return redirect()->back()->with('success', 'Updated successfully.');
    }


        // Show archived drivers (status = inactive)
        public function driverArchive()
        {
            $archivedDrivers = Driver::where('status', 'inactive')->get();

            return view('company.pages.driver-archive', compact('archivedDrivers'));
        }
        public function restoreDriver($id)
        {
            try {
                $driver = Driver::findOrFail($id);

                // Check name duplication
                $existingName = Driver::where('name', $driver->name)
                    ->where('status', 'active')
                    ->first();
                if ($existingName) {
                    return redirect()->back()->with('error', 'Cannot restore: driver name already exists.');
                }

                // Check email duplication
                $existingEmail = Driver::where('email', $driver->email)
                    ->where('status', 'active')
                    ->first();
                if ($existingEmail) {
                    return redirect()->back()->with('error', 'Cannot restore: driver email already exists.');
                }

                // Restore driver
                $driver->status = 'active';
                $driver->save();

                return redirect()->back()->with('success', 'Driver restored successfully!');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to restore driver.');
            }
        }

        // ===== Destroy (for Delete icon) =====
        public function destroy($id)
        {
            $driver = Driver::findOrFail($id);


            $driver->status = 'inactive';
            $driver->save();

            return redirect()->back()->with('success', 'Driver removed successfully.');
        }

}