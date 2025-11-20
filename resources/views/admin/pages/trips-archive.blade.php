@extends('admin.layout.layout')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title', 'Admin Panel - Trips')</title>
        <link rel="stylesheet" href="{{ asset('css/admin/pages/trips/archive-trips.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/trips/view-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/delete-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/no-data.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/restore.css') }}">
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
                        <a href="{{ route('admin.trips') }}" style="text-decoration: none" class="archive-btn"
                            aria-label="View Archive">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>
                            Back
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
                                <th class="right-side-th">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($archivedTrips as $trip)
                                <tr data-trip-id="{{ $trip->id }}" data-trip='{!! json_encode([
                                    'transactionId' => $trip->transactionId,
                                    'deliveryType' => $trip->deliveryType,
                                    'vehicleType' => $trip->vehicleType,
                                    'clientName' => $trip->clientName,
                                    'clientNumber' => $trip->clientNumber,
                                    'address' => $trip->destination,
                                    'municipality' => $trip->municipality,
                                    'company' => $trip->company,
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

                                    <td class="actions">
                                        <button class="view" title="View" type="button">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        @if (\Carbon\Carbon::parse($trip->schedule)->isFuture() || \Carbon\Carbon::parse($trip->schedule)->isToday())
                                            <form action="{{ route('admin.trips.restore', $trip->id) }}" method="POST"
                                                class="restoreForm" style="display:inline;">
                                                @csrf
                                                <button class="restoreBtn" title="Restore" type="button"
                                                    data-id="{{ $trip->id }}">
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="noData" class="no-data">
                        <img src="{{ asset('img/no-data.png') }}" alt="No Company">
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

        {{-- Restore Modal --}}
        <div id="restoreModal" class="restore-modal">
            <div class="restore-modal-box">
                <div class="restore-modal-header">
                    <h2>Confirm Restoration</h2>
                </div>

                <div class="restore-modal-body">
                    <p>Are you sure you want to restore this company? It will become active again in the system.</p>
                </div>

                <div class="restore-modal-actions">
                    <button type="button" class="restore-cancel">Cancel</button>
                    <button type="button" class="restore-confirm">Restore</button>
                </div>
            </div>
        </div>

    </body>

    </html>
@endsection

@vite(['resources/js/admin/pages/trips/archived_trips.js'])
@vite(['resources/js/admin/pages/trips/delete-trip.js'])
@vite(['resources/js/admin/pages/trips/view-trip-modal.js'])
@vite(['resources/js/admin/pages/no-data.js']) 
@vite(['resources/js/admin/pages/pagination.js']) 
@vite(['resources/js/admin/pages/alert.js'])
@vite(['resources/js/admin/pages/company/restore-company.js'])
