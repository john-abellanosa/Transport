@extends('admin.layout.layout')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title', 'Admin Panel - Trips')</title>
        <link rel="stylesheet" href="{{ asset('css/admin/pages/trips/trips.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/trips/add-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/trips/view-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
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

        <div class="admin-trips-wrapper">
            <div class="table-container">
                <div class="table-header">
                    <div class="left-section">
                        <div class="search-box" role="search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input type="text" placeholder="Search" aria-label="Search" id="searchInput">
                            <span class="clear-btn" id="clearSearch" title="Clear">&times;</span>
                        </div>

                        <div class="filter-box">
                            <div class="date-filter">
                                <label for="fromDate">From :</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-calendar"></i>
                                    <input type="date" id="fromDate" name="fromDate">
                                </div>
                            </div>

                            <div class="date-filter">
                                <label for="toDate">To :</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-calendar"></i>
                                    <input type="date" id="toDate" name="toDate">
                                </div>
                            </div>

                            <div class="filter-buttons">
                                <button class="filter-btn" id="applyFilter">
                                    <i class="fa fa-filter"></i> Filter
                                </button>
                                <button class="reset-btn" id="resetFilter">
                                    <i class="fa fa-undo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button class="add-btn" type="button" id="openModalBtn">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Add Trips
                        </button>
                        <a href="{{ route('admin.trips.archive') }}" style="text-decoration: none" class="archive-btn"
                            aria-label="View Archive">
                            <i class="fa fa-archive" aria-hidden="true"></i>
                            Archive
                        </a>
                    </div>
                </div>

                <div class="trips-table-scroll">
                    <table class="styled-table" id="dataTable">
                        <thead>
                            <tr>
                                <th class="left-side-th">Transaction ID</th>
                                <th>
                                    <div class="table-company-dropdown" id="companyDropdown">
                                        <button class="company-filter-btn" id="companyDropdownBtn" aria-haspopup="listbox"
                                            aria-expanded="false" title="Filter by company">
                                            COMPANY
                                            <i class="fa fa-caret-down" aria-hidden="true" style="margin-left: 5px;"></i>
                                        </button>
                                        <div class="company-dropdown-tooltip" id="companyDropdownMenu" tabindex="-1">
                                            <ul>
                                                <li tabindex="0" data-value="all">All</li>
                                                @php
                                                    use App\Models\Admin\Company;

                                                    // Fetch both name and branch of active companies
                                                    $companies = Company::where('status', 'active')->get([
                                                        'name',
                                                        'branch',
                                                    ]);
                                                @endphp
                                                @foreach ($companies as $company)
                                                    <li tabindex="0"
                                                        data-value="{{ strtolower($company->name . ' - ' . $company->branch) }}">
                                                        {{ $company->name }} - {{ $company->branch }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </th>
                                <th>Recipient</th>
                                <th>Destination</th>
                                <th>Schedule</th>
                                <th>
                                    <div class="table-status-dropdown" id="statusDropdown">
                                        <button class="status-filter-btn" id="statusDropdownBtn" aria-haspopup="listbox"
                                            aria-expanded="false" title="Filter by status">
                                            STATUS
                                            <i class="fa fa-caret-down" aria-hidden="true" style="margin-left: 5px;"></i>
                                        </button>
                                        <div class="status-dropdown-tooltip" id="statusDropdownMenu" tabindex="-1">
                                            <ul>
                                                <li tabindex="0" data-value="all">All</li>
                                                <li tabindex="0" data-value="pending">Pending</li>
                                                <li tabindex="0" data-value="backload">Backload</li>
                                                <li tabindex="0" data-value="in-transit">In-transit</li>
                                                <li tabindex="0" data-value="completed">Completed</li>
                                                <li tabindex="0" data-value="cancelled">Cancelled</li>
                                            </ul>
                                        </div>
                                    </div>
                                </th>
                                <th class="right-side-th">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trips as $trip)
                                <tr data-trip-id="{{ $trip->id }}" data-trip='{!! json_encode([
                                    'transactionId' => $trip->transactionId,
                                    'deliveryType' => $trip->deliveryType,
                                    'vehicleType' => $trip->vehicleType,
                                    'clientName' => $trip->clientName,
                                    'clientNumber' => $trip->clientNumber,
                                    'address' => $trip->destination,
                                    'municipality' => $trip->municipality,
                                    'company' => $trip->company,
                                    'branch' => $trip->branch,
                                    'driver' => $trip->driver ?? 'N/A',
                                    'distance' => $trip->distance,
                                    'cost' => $trip->cost,
                                    'schedule' => \Carbon\Carbon::parse($trip->schedule)->format('F d, Y \a\t g:ia'), 
                                    'arrival' => $trip->arrival_date ? \Carbon\Carbon::parse($trip->arrival_date)->format('F d, Y \a\t g:ia') : 'N/A',
                                    'remarks' => $trip->remarks ?? 'N/A',
                                    'status' => $trip->status ?? 'Pending',
                                    'proof_photo' => $trip->proof_photo ? asset($trip->proof_photo) : '',
                                ]) !!}'>
                                    <td>{{ $trip->transactionId }}</td>
                                    <td>{{ $trip->company }}</td>
                                    <td>{{ $trip->clientName }}</td>
                                    <td>{{ $trip->municipality }}</td>
                                    <td>{{ \Carbon\Carbon::parse($trip->schedule)->format('F d, Y') }}</td>
                                    <td>
                                        <span class="badge {{ strtolower($trip->status) }}">
                                            {{ ucfirst($trip->status) }}
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="view" title="View" type="button">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        @if (strtolower($trip->status) === 'pending')
                                            <button class="edit" title="Edit" type="button">
                                                <i class="fa fa-pen"></i>
                                            </button>

                                            <button class="delete" title="Delete" type="button">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="noData" class="no-data">
                        <img src="{{ asset('img/no-data.png') }}" alt="No Trips">
                        <p>No Trips Available</p>
                    </div>

                    <div id="noResultsFound" class="no-data" style="display:none;">
                        <img src="{{ asset('img/no-data.png') }}" alt="No Results Found">
                        <p>No Results Found</p>
                    </div>
                </div>

                <div class="pagination">
                    <button class="disabled-button prev"><i class="fa fa-angle-left"></i></button>
                    <button class="active-button next"><i class="fa fa-angle-right"></i></button>
                </div>
            </div>
        </div>

        {{-- Add trip modal --}}
        <form id="tripForm" class="trip-form" method="POST" action="{{ route('trips.store') }}">
            @csrf
            <div id="tripModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add Trip</h2>
                        <span class="close">&times;</span>
                    </div>

                    <div class="company-modal-body">
                        <div class="form-group">
                            <div>
                                <label for="deliveryType">Delivery Type</label>
                                <select id="deliveryType" name="deliveryType">
                                    <option disabled selected value="">Select</option>
                                    <option>Dry</option>
                                    <option>Chilled</option>
                                </select>
                                <small class="input-error" id="deliveryTypeError">Please select a delivery type</small>
                            </div>

                            <div>
                                <label for="vehicleType">Vehicle Type</label>
                                <select id="vehicleType" name="vehicleType">
                                    <option disabled selected value="">Select</option>
                                    <option>4-Wheeler</option>
                                    <option>6-Wheeler</option>
                                </select>
                                <small class="input-error" id="vehicleTypeError">Please select a vehicle type</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-group-inside">
                                <label for="clientName">Client Name</label>
                                <input id="clientName" name="clientName" type="text" placeholder="Enter client name"
                                    maxlength="25">
                                <small class="input-error" id="clientNameError">Client name cannot be empty or spaces
                                    only</small>
                            </div>

                            <div class="form-group-inside">
                                <label for="clientNumber">Client Number</label>
                                <div class="input-with-prefix">
                                    <span class="prefix">+63</span>
                                    <input id="clientNumber" name="clientNumber" type="text" placeholder="9123456789"
                                        maxlength="10">
                                </div>
                                <small class="input-error" id="clientNumberError">Must be 10 digits (e.g.,
                                    9123456789)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-group-inside">
                                <label for="destination">Destination</label>
                                <input id="destination" name="destination" type="text"
                                    placeholder="Enter destination">
                                <small class="input-error" id="destinationError">Destination cannot be empty or spaces
                                    only</small>
                            </div>

                            <div class="form-group-inside">
                                <label for="municipality">Municipality</label>
                                <select id="municipality" name="municipality" autocomplete="off">
                                    <option disabled selected value="">Select Municipality</option>
                                    @foreach ($municipalities as $municipality)
                                        <option value="{{ $municipality }}">{{ $municipality }}</option>
                                    @endforeach
                                </select>

                                <small class="input-error" id="municipalityError">
                                    Municipality cannot be empty or spaces only
                                </small>

                            </div>

                        </div>

                        <div class="form-group-full">
                            <label for="company">Company</label>
                            <select id="company" name="company">
                                <option disabled selected value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option
                                        value="{{ $company->name }}{{ $company->branch ? ' - ' . $company->branch : '' }}">
                                        {{ $company->name }}{{ $company->branch ? ' - ' . $company->branch : '' }}
                                    </option>
                                @endforeach
                            </select>

                            <small class="input-error" id="companyError">Please select a company</small>
                        </div>

                        <div class="form-group">
                            <div class="form-group-inside">
                                <label for="cost">Cost (₱)</label>
                                <input id="cost" name="cost" type="number" placeholder="Enter cost">
                                <small class="input-error" id="costError">Cost cannot be blank</small>
                            </div>
                            <div class="form-group-full">
                                <label for="schedule">Schedule</label>
                                <input id="schedule" name="schedule" type="date" min="{{ date('Y-m-d') }}">
                                <small class="input-error" id="scheduleError">Schedule is required</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="cancel">Cancel</button>
                        <button type="submit" class="add">Submit</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Confirmation Modal -->
        <div id="confirmDuplicateModal" class="modal">
            <div class="modal-content">
                <div class="modal-header-exist">
                    <h3>Trip Already Exists</h3>
                </div>

                <div class="company-modal-body">
                    <p>A trip with the same details already exists. Do you still want to continue adding this trip?</p>

                    <div class="transaction-info bordered">
                        <strong>Transaction ID(s):</strong>
                        <div class="transaction-list" id="duplicateTransactionIds"></div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" id="cancelDuplicate" class="cancel">No, Cancel</button>
                    <button type="button" id="proceedDuplicate" class="add">Yes, Continue</button>
                </div>
            </div>
        </div>

        <!-- Archived Trip Modal -->
        <div id="archivedTripModal" class="modal">
            <div class="modal-content">
                <div class="modal-header-exist">
                    <h3>Archived Trip Found</h3>
                </div>
                <div class="company-modal-body">
                    <p>
                        A trip with the same details is currently <strong>Archived</strong>. 
                        Maybe you want to restore it or just continue anyway?
                    </p>

                    <div class="transaction-info bordered">
                        <strong>Transaction ID(s):</strong>
                        <div class="transaction-list" id="archivedTransactionIds"></div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" id="cancelArchived" class="cancel">Cancel</button> 
                    <button type="button" id="continueArchived" class="add">Continue Anyway</button>
                </div>
            </div>
        </div>

        <!-- Trip Already Exists Modal -->
        <div id="existsTripModal" class="modal">
            <div class="modal-content">
                <div class="modal-header-exist">
                    <h3>Trip Already Exists</h3>
                </div>
                <div class="company-modal-body">
                    <p>
                        A trip with the same details already exists in the <strong><span id="existingStatus"></span></strong>  list. 
                        Do you still want to continue adding this trip?
                    </p>

                    <div class="transaction-info bordered">
                        <strong>Transaction ID(s):</strong>
                        <div class="transaction-list" id="existsTransactionIds"></div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" id="cancelExists" class="cancel">Cancel</button>
                    <button type="button" id="continueExists" class="add">Continue Anyway</button>
                </div>
            </div>
        </div>

        <!-- Archived + Pending Trip Modal -->
        <div id="archivedPendingModal" class="modal">
            <div class="modal-content">
                <div class="modal-header-exist">
                    <h3>Trip Already Exists</h3>
                </div>
                <div class="company-modal-body">
                    <p>
                        A trip with the same details exists in both the <strong>Archived</strong> and <strong>Pending</strong> lists.
                        Do you still want to continue adding this trip?
                    </p>

                    <div class="transaction-section bordered">
                        <div class="transaction-group">
                            <div class="transaction-label">Archived Transaction ID(s):</div>
                            <div class="transaction-list" id="archivedIdsList"></div>
                        </div>

                        <div class="transaction-group">
                            <div class="transaction-label">Pending Transaction ID(s):</div>
                            <div class="transaction-list" id="pendingIdsList"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" id="cancelArchivedPending" class="cancel">Cancel</button>
                    <button type="button" id="continueArchivedPending" class="add">Continue Anyway</button>
                </div>
            </div>
        </div>

        <script>
            window.appConfig = {
                checkTripUrl: "{{ route('admin.checkTripExists') }}",
                csrf: "{{ csrf_token() }}"
            };
        </script>
        <script src="{{ asset('js/trip.js') }}"></script>

        <!-- Edit Trip Modal -->
        <form id="editTripForm" class="trip-form" method="POST">
            @csrf
            @method('PUT')
            <div id="editTripModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Edit Trip</h2>
                        <span class="close">&times;</span>
                    </div>

                    <!-- Hidden Trip ID -->
                    <input type="hidden" id="editTripId" name="id">

                    <div class="company-modal-body">
                        <div class="form-group">
                            <div>
                                <label>Delivery Type</label>
                                <select id="editDeliveryType" name="deliveryType">
                                    <option disabled selected value="">Select</option>
                                    <option value="Dry">Dry</option>
                                    <option value="Chilled">Chilled</option>
                                </select>
                                <small class="input-error" id="editDeliveryTypeError">Please select a delivery
                                    type</small>
                            </div>

                            <div>
                                <label>Vehicle Type</label>
                                <select id="editVehicleType" name="vehicleType">
                                    <option disabled selected value="">Select</option>
                                    <option value="4-Wheeler">4-Wheeler</option>
                                    <option value="6-Wheeler">6-Wheeler</option>
                                </select>
                                <small class="input-error" id="editVehicleTypeError">Please select a vehicle type</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-group-inside">
                                <label>Client Name</label>
                                <input id="editClientName" name="clientName" type="text" maxlength="25"
                                    placeholder="Enter client name">
                                <small class="input-error" id="editClientNameError">Client name cannot be empty or spaces
                                    only</small>
                            </div>

                            <div class="form-group-inside">
                                <label>Client Number</label>
                                <div class="input-with-prefix">
                                    <span class="prefix">+63</span>
                                    <input id="editClientNumber" name="clientNumber" type="text" maxlength="10"
                                        placeholder="9123456789">
                                </div>
                                <small class="input-error" id="editClientNumberError">Must be 10 digits (e.g.,
                                    9123456789)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-group-inside">
                                <label>Destination</label>
                                <input id="editDestination" name="destination" type="text"
                                    placeholder="Enter destination">
                                <small class="input-error" id="editDestinationError">Destination cannot be empty or spaces
                                    only</small>
                            </div>

                            <div class="form-group-inside">
                                <label for="editMunicipality">Municipality</label>
                                <select id="editMunicipality" name="municipality" autocomplete="off">
                                    <option disabled selected value="">Select Municipality</option>
                                    @foreach ($municipalities as $municipality)
                                        <option value="{{ $municipality }}">{{ $municipality }}</option>
                                    @endforeach
                                </select>
                                <small class="input-error" id="editMunicipalityError">
                                    Municipality cannot be empty or spaces only
                                </small>
                            </div>

                        </div>

                        <div class="form-group-full">
                            <label for="editCompany">Company</label>
                            <select id="editCompany" name="company" required>
                                <option disabled value="">Select Company</option>
                                @foreach ($companies as $company)
                                    @php
                                        // Combine company and branch if branch exists
                                        $displayName = $company->name;
                                        if ($company->branch) {
                                            $displayName .= ' - ' . $company->branch;
                                        }
                                    @endphp
                                    <option value="{{ $displayName }}"
                                        {{ isset($trip) && $trip->company == $displayName ? 'selected' : '' }}>
                                        {{ $displayName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            <div class="form-group-inside">
                                <label>Cost</label>
                                <label for="editCost">Cost</label>
                                <input type="text" id="editCost" name="cost" value="{{ $trip->cost ?? '' }}">
                                <small class="input-error" id="editCostError">Cost cannot be blank</small>
                            </div>
                            <div class="form-group-full">
                                <label>Schedule</label>
                                <input id="editSchedule" name="schedule" type="date">
                                <small class="input-error" id="editScheduleError">Schedule is required</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="cancel">Cancel</button>
                        <button type="submit" class="edit">Save changes</button>
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
                    <p>Are you sure you want to delete this trip? This action cannot be undone.</p>
                </div>
                <div class="delete-modal-actions">
                    <button type="button" class="delete-cancel">Cancel</button>
                    <button type="button" class="delete-confirm">Remove</button>
                </div>
            </div>
        </div>

        {{-- View Modal --}}
        <div class="custom-modal" id="viewTripModal">
            <div class="custom-modal-content">
                <div class="custom-modal-header">
                    Trip Details
                    <span class="custom-close-btn" id="closeViewTripModal">&times;</span>
                </div>
                <div class="custom-modal-body" id="viewTripModalBody">
                    <!-- Dynamic content -->
                </div>
            </div>
        </div>

    </body>

    </html>
@endsection
 
@vite(['resources/js/admin/pages/trips/add-trip-modal.js'])
@vite(['resources/js/admin/pages/trips/edit-trip-modal.js'])
@vite(['resources/js/admin/pages/trips/delete-trip.js'])
@vite(['resources/js/admin/pages/trips/view-trip-modal.js'])
@vite(['resources/js/admin/pages/no-data.js'])
@vite(['resources/js/admin/pages/trips/trips.js'])
@vite(['resources/js/admin/pages/pagination.js']) 
@vite(['resources/js/admin/pages/alert.js'])
