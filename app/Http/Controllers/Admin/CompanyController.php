<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Company;
use App\Models\Admin\MunicipalityCost;
use App\Mail\SendDefaultPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Admin\Municipality;

class CompanyController extends Controller
{
        public function checkName(Request $request)
    {
        $name = $request->input('name');

        // Only check active companies
        $exists = Company::where('name', $name)
            ->where('status', 'active')
            ->exists();

        return response()->json(['exists' => $exists]);
    }

       public function checkMainCompany(Request $request)
    {
        $name = $request->input('name');
        $threshold = 80; // similarity in percent
        $companies = Company::pluck('name'); // all main company names

        foreach ($companies as $existingName) {
            similar_text(strtolower($name), strtolower($existingName), $percent);
            if ($percent >= $threshold) {
                return response()->json([
                    'exists' => true,
                    'similar_name' => $existingName,
                    'similarity' => $percent
                ]);
            }
        }

        return response()->json(['exists' => false]);
    }

        public function checkEmail(Request $request)
    {
        $email = $request->input('email');

        // Only check active companies
        $exists = Company::where('email', $email)
            ->where('status', 'active')
            ->exists();

        return response()->json(['exists' => $exists]);
    }

        public function checkContact(Request $request)
    {
        $contact = $request->input('contact');

        // Only check active companies
        $exists = Company::where('contact', $contact)
            ->where('status', 'active')
            ->exists();

        return response()->json(['exists' => $exists]);
    }

       public function checkMunicipality(Request $request)
    {
        $municipality = $request->input('municipality');

        // Only check active companies
        $exists = Company::where('municipality', $municipality)
            ->where('status', 'active')
            ->exists();

        return response()->json(['exists' => $exists]);
    }

        public function store(Request $request)
    {
        $isBranch = $request->boolean('has_branch') || $request->boolean('is_branch');

        // Basic validation (no unique rule here yet)
        $rules = [
            'name'         => 'required|string|max:50',
            'email'        => 'required|email',
            'address'      => 'required|string|max:100',
            'owner'        => 'required|string|max:50',
            'contact'      => 'required|string|size:10',
            'municipality' => 'required|string|max:50',
            'cost'         => 'required|numeric|min:0',
        ];

        $request->validate($rules);

        // Check if an active company already has the same name, email, contact, or municipality
        $duplicate = Company::where('status', 'active')
            ->where(function ($query) use ($request) {
                $query->where('name', $request->name)
                    ->orWhere('email', $request->email)
                    ->orWhere('contact', $request->contact)
                    ->orWhere('municipality', $request->municipality);
            })
            ->first();

        if ($duplicate) {
        // Only block duplicate name if not a branch
        if (!$request->boolean('has_branch') && $duplicate->name === $request->name) {
            return response()->json(['error' => 'Cannot add: company name already exists in an active company.']);
        }

        // Always block duplicates for email, contact, and municipality
        if ($duplicate->email === $request->email) {
            return response()->json(['error' => 'Cannot add: email already exists in an active company.']);
        }
        if ($duplicate->contact === $request->contact) {
            return response()->json(['error' => 'Cannot add: contact number already exists in an active company.']);
        }
        if ($duplicate->municipality === $request->municipality) {
            return response()->json(['error' => 'Cannot add: municipality already exists in an active company.']);
        }
        }


        // Use inputted branch name or default to "Main Branch"
        $branchName = $request->filled('branch') ? $request->branch : 'Main Branch';

        $defaultPassword = Str::random(8);

        // Create the new company (status = active by default)
        $company = Company::create([
            'name'                  => $request->name,
            'branch'                => $branchName,
            'email'                 => $request->email,
            'address'               => $request->address,
            'owner'                 => $request->owner,
            'contact'               => $request->contact,
            'municipality'          => $request->municipality,
            'cost'                  => $request->cost,
            'password'              => bcrypt($defaultPassword),
            'is_temporary_password' => true,
            'status'                => 'active',
        ]);


        // Send email with default password
        try {
            Mail::to($company->email)->send(
                new SendDefaultPasswordMail($company->name, $company->email, $defaultPassword)
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => 'Company added successfully but email failed to send.',
                'error'   => $e->getMessage(),
            ]);
        }

        return response()->json(['success' => 'Added successfully'], 200);
    }


        public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $rules = [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('companies', 'name')
                    ->ignore($company->id)
                    ->where(function ($query) use ($request) {
                        $query->where('status', 'active')
                            ->where('branch', $request->branch); // 👈 Only check duplicate if same branch
                    }),
            ],
            'branch' => 'required|string|max:50',
            'email' => [
                'required',
                'email',
                Rule::unique('companies', 'email')
                    ->ignore($company->id)
                    ->where(function ($query) {
                        $query->where('status', 'active');
                    }),
            ],
            'address' => 'required|string|max:100',
            'owner' => 'required|string|max:50',
            'contact' => [
                'required',
                'string',
                'size:10',
                Rule::unique('companies', 'contact')
                    ->ignore($company->id)
                    ->where(function ($query) {
                        $query->where('status', 'active');
                    }),
            ],
            'municipality' => [
                'required',
                'string',
                'max:50',
                Rule::unique('companies', 'municipality')
                    ->ignore($company->id)
                    ->where(function ($query) {
                        $query->where('status', 'active');
                    }),
            ],
            'cost' => 'required|numeric|min:0',
        ];

        $validated = $request->validate($rules, [
            'name.unique'         => 'Company name already exists for this branch.',
            'email.unique'        => 'Email already exists.',
            'contact.unique'      => 'Contact number already exists.',
            'municipality.unique' => 'Municipality already exists.',
        ]);

        $company->update($validated);

        return response()->json(['success' => 'Updated successfully.']);
    }

        public function archive()
    {
        // Get inactive companies
        $archivedCompanies = Company::where('status', 'inactive')->get();
        return view('admin.pages.company-archive', compact('archivedCompanies'));
    }



        public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);

            $company->status = 'inactive';
            $company->save();

            return response()->json(['success' => 'Company removed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to archive company. Please try again.'], 500);
        }
    }

    // Restore company (status active + reinsert municipality cost)
        public function restore($id)
    {
        try {
            $company = Company::findOrFail($id);

            // Check company name and branch
            $existingName = Company::where('name', $company->name)
                ->where('branch', $company->branch)
                ->where('status', 'active')
                ->first();
            if ($existingName) {
                return redirect()->back()->with('error', 'Cannot restore: company with the same name and branch already exists.');
            }

            // Check email
            $existingEmail = Company::where('email', $company->email)
                ->where('status', 'active')
                ->first();
            if ($existingEmail) {
                return redirect()->back()->with('error', 'Cannot restore: email already exists.');
            }

            // Check contact
            $existingContact = Company::where('contact', $company->contact)
                ->where('status', 'active')
                ->first();
            if ($existingContact) {
                return redirect()->back()->with('error', 'Cannot restore: contact number already exists.');
            }

            // Check municipality
            $existingMunicipality = Company::where('municipality', $company->municipality)
                ->where('status', 'active')
                ->first();
            if ($existingMunicipality) {
                return redirect()->back()->with('error', 'Cannot restore: municipality already exists.');
            }

            // Restore company
            $company->status = 'active';
            $company->save();

            return redirect()->back()->with('success', 'Company restored successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore company.');
        }
    }
}