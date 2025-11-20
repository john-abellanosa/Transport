@extends('admin.layout.layout')
@section('title', 'Admin Panel - Company')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="{{ asset('css/admin/pages/company/company.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/company/add-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/delete-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/no-data.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    <body>

        @if (session('success'))
            <div id="success-alert" class="success_alert">
                <strong></strong> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div id="error-alert" class="error_alert">
                <strong></strong> {{ session('error') }}
            </div>
        @endif

        <div class="admin-company-wrapper">
            <div class="table-container">
                <div class="table-header">
                    <div class="search-box" role="search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input type="text" placeholder="Search" aria-label="Search" id="searchInput">
                        <span class="clear-btn" id="clearSearch" title="Clear">&times;</span>
                    </div>

                    <div class="action-buttons">
                        <button class="add-btn" type="button" id="openModalBtn">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Add Company
                        </button>

                        <a href="{{ route('admin.companies.archive') }}" class="archive-btn" aria-label="View Archive">
                            <i class="fa fa-archive" aria-hidden="true"></i>
                            Archive
                        </a>

                    </div>
                </div>

                <div class="company-table-scroll">
                    <table class="styled-table" id="dataTable">
                        <thead>
                            <tr>
                                <th class="left-side-th">Company Name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Owner</th>
                                <th>Contact</th>
                                <th>Municipality</th>
                                <th class="right-side-th">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($companies as $company)
                                <tr data-id="{{ $company->id }}" data-name="{{ $company->name }}"
                                    data-branch="{{ $company->branch }}" data-email="{{ $company->email }}"
                                    data-address="{{ $company->address }}" data-owner="{{ $company->owner }}"
                                    data-number="{{ $company->contact }}" data-municipality="{{ $company->municipality }}"
                                    data-cost="{{ $company->cost }}">
                                    <td>{{ $company->name }} - {{ $company->branch }}</td>
                                    <td>{{ $company->email }}</td>
                                    <td>{{ $company->address }}</td>
                                    <td>{{ $company->owner }}</td>
                                    <td>+63 {{ $company->contact }}</td>
                                    <td>{{ $company->municipality }}</td>
                                    <td class="actions">
                                        <button class="editCompanyBtn" title="Edit" type="button">
                                            <i class="fa fa-pen"></i>
                                        </button>
                                        <button class="delete" title="Remove" type="button">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div id="noResultsFound" class="no-data" style="display:none;">
                        <img src="{{ asset('img/no-data.png') }}" alt="No Results Found">
                        <p>No Results Found</p>
                    </div>

                    <div id="noData" class="no-data">
                        <img src="{{ asset('img/no-data.png') }}" alt="No Company">
                        <p>No Company Available</p>
                    </div>
                </div>

                <div class="pagination">
                    <button class="disabled-button prev"><i class="fa fa-angle-left"></i></button>
                    <button class="active-button next"><i class="fa fa-angle-right"></i></button>
                </div>
            </div>
        </div>

        {{-- Add Modal  --}}
        <form id="addCompanyForm" class="company-form" autocomplete="off">
            @csrf
            <div id="addCompanyModal" class="company-modal">
                <div class="company-modal-content">
                    <div class="company-modal-header">
                        <h2>Add Company</h2>
                        <span class="company-modal-close">&times;</span>
                    </div>

                    <div class="company-modal-body">
                        <div class="form-group-full">
                            <label for="companyName">Company Name</label>
                            <input id="companyName" name="name" type="text" placeholder="Enter company name"
                                maxlength="50">
                            <small class="input-error" id="companyNameError"></small>
                        </div>

                        <!-- Question for branch -->
                        <div class="form-group-full">
                            <label>Is this company a branch?</label>
                            <div class="radio-group">
                                <label><input type="radio" name="hasBranch" value="yes"> Yes</label>
                                <label><input type="radio" name="hasBranch" value="no"> No</label>
                            </div>
                        </div>

                        <!-- Branch name input -->
                        <div class="form-group-full">
                            <label for="branchName">Branch Name</label>
                            <input id="branchName" name="branch" type="text" placeholder="Enter branch name"
                                disabled style="background: #f3f4f6;">
                            <small class="input-error" id="branchNameError"></small>
                        </div>

                        <div class="form-group-full">
                            <label for="companyEmail">Company Email</label>
                            <input id="companyEmail" name="email" type="text" placeholder="Enter company email">
                            <small class="input-error" id="companyEmailError"></small>
                        </div>

                        <div class="form-group-full">
                            <label for="companyAddress">Company Address</label>
                            <input id="companyAddress" name="address" type="text" placeholder="Enter address"
                                maxlength="100">
                            <small class="input-error" id="companyAddressError"></small>
                        </div>

                        <div class="form-group-full">
                            <label for="companyOwner">Company Owner</label>
                            <input id="companyOwner" name="owner" type="text" placeholder="Enter owner name"
                                maxlength="50">
                            <small class="input-error" id="companyOwnerError"></small>
                        </div>

                        <div class="form-group-full">
                            <label for="ownerNumber">Owner Number</label>
                            <div class="input-with-prefix">
                                <span class="prefix">+63</span>
                                <input id="ownerNumber" name="contact" type="text" placeholder="9123456789"
                                    maxlength="10">
                            </div>
                            <small class="input-error" id="ownerNumberError"></small>
                        </div>

                        <div class="form-group-full">
                            <div class="form-group-row">
                                <div class="form-group-half">
                                    <label for="companyMunicipality">Municipality / City</label>
                                    <select id="companyMunicipality" name="municipality" class="form-select">
                                        <option value="" disabled selected>Select Municipality / City</option>

                                        <optgroup label="Cities">
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->name }}"
                                                    @if (in_array($city->name, $usedMunicipalities)) disabled @endif>
                                                    {{ $city->name }}
                                                    @if (in_array($city->name, $usedMunicipalities))
                                                    @endif
                                                </option>
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="Municipalities">
                                            @foreach ($municipalities as $municipality)
                                                <option value="{{ $municipality->name }}"
                                                    @if (in_array($municipality->name, $usedMunicipalities)) disabled @endif>
                                                    {{ $municipality->name }}
                                                    @if (in_array($municipality->name, $usedMunicipalities))   
                                                    @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>

                                    <small class="input-error" id="companyMunicipalityError"></small>
                                </div>

                                <div class="form-group-half">
                                    <label for="municipalityCost">Cost</label>
                                    <input type="number" id="municipalityCost" name="cost" placeholder="Enter cost"
                                        min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                                    <small class="input-error" id="municipalityCostError"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="company-modal-actions">
                        <button type="button" class="cancel">Cancel</button>
                        <button type="submit" class="add">Submit</button>
                    </div>
                </div>
            </div>
        </form>


        {{-- Edit modal --}}
        <form id="editCompanyForm" class="company-form">
            <div id="editCompanyModal" class="company-modal">
                <div class="company-modal-content">
                    <div class="company-modal-header">
                        <h2>Edit Company</h2>
                        <span class="company-modal-close">&times;</span>
                    </div>

                    <!-- Hidden field for company ID -->
                    <input type="hidden" id="editCompanyId" name="id" value="">

                    <div class="company-modal-body">
                        <div class="form-group-full">
                            <label for="editCompanyName">Company Name</label>
                            <input id="editCompanyName" name="name" type="text" placeholder="Enter company name"
                                maxlength="50">
                            <small class="input-error" id="editCompanyNameError">Company name cannot be empty or spaces
                                only</small>
                        </div>

                        <div class="form-group-full">
                            <label for="editCompanyBranch">Branch Name</label>
                            <input id="editCompanyBranch" name="branch" type="text" placeholder="Enter branch name"
                                maxlength="50">
                            <small class="input-error" id="editCompanyBranchError">Branch name cannot be empty or spaces
                                only</small>
                        </div>

                        <div class="form-group-full">
                            <label for="editCompanyEmail">Company Email</label>
                            <input id="editCompanyEmail" name="email" type="email"
                                placeholder="Enter company email">
                            <small class="input-error" id="editCompanyEmailError">Invalid or empty email address</small>
                        </div>

                        <div class="form-group-full">
                            <label for="editCompanyAddress">Company Address</label>
                            <input id="editCompanyAddress" name="address" type="text" placeholder="Enter address"
                                maxlength="100">
                            <small class="input-error" id="editCompanyAddressError">Address cannot be empty or spaces
                                only</small>
                        </div>

                        <div class="form-group-full">
                            <label for="editCompanyOwner">Company Owner</label>
                            <input id="editCompanyOwner" name="owner" type="text" placeholder="Enter owner name"
                                maxlength="50">
                            <small class="input-error" id="editCompanyOwnerError">Owner name cannot be empty or spaces
                                only</small>
                        </div>

                        <div class="form-group-full">
                            <label for="editOwnerNumber">Owner Number</label>
                            <div class="input-with-prefix">
                                <span class="prefix">+63</span>
                                <input id="editOwnerNumber" name="contact" type="text" placeholder="9123456789"
                                    maxlength="10">
                            </div>
                            <small class="input-error" id="editOwnerNumberError">Must be 10 digits (e.g.,
                                9123456789)</small>
                        </div>


                        <div class="form-group-full">
                            <div class="form-group-row">
                                <div class="form-group-half">
                                    <label for="editCompanyMunicipality">Municipality / City</label>
                                    <select id="editCompanyMunicipality" name="municipality" class="form-select">
                                        <option value="" disabled>Select Municipality / City</option>

                                        <optgroup label="Cities">
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->name }}"
                                                    @if (in_array($city->name, $usedMunicipalities)) disabled @endif>
                                                    {{ $city->name }}
                                                    @if (in_array($city->name, $usedMunicipalities))
                                                    @endif
                                                </option>
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="Municipalities">
                                            @foreach ($municipalities as $municipality)
                                                <option value="{{ $municipality->name }}"
                                                    @if (in_array($municipality->name, $usedMunicipalities)) disabled @endif>
                                                    {{ $municipality->name }}
                                                    @if (in_array($municipality->name, $usedMunicipalities))
                                                    @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>

                                    <small class="input-error" id="editCompanyMunicipalityError">
                                        Municipality or City cannot be empty
                                    </small>

                                </div>
                                <div class="form-group-half">
                                    <label for="editCompanyCost">Cost</label>
                                    <input id="editCompanyCost" name="cost" type="number" placeholder="Enter cost"
                                        min="0" step="0.01">
                                    <small class="input-error" id="editCompanyCostError">Cost must be a valid
                                        number</small>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="company-modal-actions">
                        <button type="button" class="cancel">Cancel</button>
                        <button type="submit" class="add">Save changes</button>
                    </div>
                </div>
            </div>
        </form>



        {{-- Delete modal --}}
        <div id="deleteModal" class="delete-modal">
            <div class="delete-modal-box">
                <div class="delete-modal-header">
                    <h2>Confirm</h2>
                </div>

                <div class="delete-modal-body">
                    <p>Are you sure you want to archive this company? You can restore it later from the archive page.</p>
                </div>

                <div class="delete-modal-actions">
                    <button type="button" class="delete-cancel">Cancel</button>
                    <button type="button" class="delete-confirm">Remove</button>
                </div>
            </div>
        </div>
        
        <script>
            window.companiesList = @json($companies ?? []);
        </script>

    </body>

    </html>
@endsection

@vite(['resources/js/admin/pages/company/add-company-modal.js'])
@vite(['resources/js/admin/pages/company/edit-company-modal.js'])
@vite(['resources/js/admin/pages/company/delete-company.js'])
@vite(['resources/js/admin/pages/no-data.js'])
@vite(['resources/js/admin/pages/pagination.js'])
@vite(['resources/js/admin/pages/search-bar.js'])
@vite(['resources/js/admin/pages/alert.js'])
