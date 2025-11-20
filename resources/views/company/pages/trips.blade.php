@extends('company.layout.layout')

@section('title', 'Company Panel - Trips')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title', 'Company Panel - Trips')</title>
        <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/no-data.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/trips/trips.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/trips/assign-driver.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/trips/view-modal.css') }}">
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
        <div class="company-trips-wrapper">
            <div class="table-container">
                <div class="table-header">
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

                <div class="trips-table-scroll">
                    <table class="styled-table" id="dataTable">
                        <thead>
                            <tr>
                                <th class="left-side-th">Transaction ID</th>
                                <th>Recipient</th>
                                <th>Client Number</th>
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
                                <tr>
                                    <td>{{ $trip->transactionId }}</td>
                                    <td>{{ $trip->clientName }}</td>
                                    <td>+63 {{ $trip->clientNumber }}</td>
                                    <td>{{ $trip->destination }}</td>
                                    <td>{{ \Carbon\Carbon::parse($trip->schedule)->format('F d, Y') }}</td>
                                    <td>
                                        <span class="badge {{ strtolower($trip->status) }}">{{ $trip->status }}</span>
                                    <td class="actions">
                                        <button class="view" data-transaction="{{ $trip->transactionId }}"
                                            data-status="{{ $trip->status }}"
                                            data-delivery-type="{{ $trip->deliveryType }}"
                                            data-vehicle="{{ $trip->vehicleType }}"
                                            data-cost="₱{{ number_format($trip->cost, 2) }}"
                                            data-client="{{ $trip->clientName }}" data-contact="{{ $trip->clientNumber }}"
                                            data-address="{{ $trip->destination }}"
                                            data-municipality="{{ $trip->municipality }}"
                                            data-schedule="{{ $trip->schedule ? \Carbon\Carbon::parse($trip->schedule)->format('F d, Y \a\t g:ia') : 'N/A' }}"
                                            data-assigned_date="{{ $trip->assigned_date ? \Carbon\Carbon::parse($trip->assigned_date)->format('F j, Y \a\t g:ia') : '' }}"
                                            data-arrival="{{ $trip->arrival_date ? \Carbon\Carbon::parse($trip->arrival_date)->format('F d, Y \a\t g:ia') : 'N/A' }}"
                                            data-driver="{{ $trip->driver ?? 'Not assigned' }}"
                                            data-remarks="{{ $trip->remarks ?? 'No remarks' }}"
                                            data-proof-photo="{{ $trip->proof_photo ? asset($trip->proof_photo) : '' }}">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        @if (!in_array($trip->status, ['Completed', 'Cancelled']))
                                            <button class="assign {{ $trip->status == 'In-transit' ? 'yellow-btn' : '' }}"
                                                data-id="{{ $trip->id }}"
                                                title="{{ $trip->status == 'In-transit' ? 'Re-assign' : 'Assign' }}">
                                                <i class="fa-solid fa-truck"></i>
                                            </button>
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

        {{-- Assign Driver Modal --}}
        <div class="assign-modal" id="assignDriverModal">
            <div class="assign-modal-content">
                <div class="assign-modal-header">
                    Assign a Driver
                    <span class="assign-close-btn" id="closeAssignDriverModal">&times;</span>
                </div>
                <div class="assign-modal-body">
                    <form id="assignDriverForm">
                        <input type="hidden" name="trip_id" id="assignTripId">

                        <div class="form-group">
                            <label for="driverSelect">Available Drivers</label>
                            <select id="driverSelect" name="driver_id" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                            <div id="driverError" class="error-message"></div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="cancel-btn" id="cancelAssignBtn">Cancel</button>
                            <button type="submit" class="submit-btn">Assign</button>
                        </div>
                    </form>
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

                </div>
            </div>
        </div>

    </body>

    </html>
@endsection

@vite(['resources/js/company/pages/trips/view-modal.js'])
@vite(['resources/js/company/pages/trips/trips.js'])
@vite(['resources/js/company/pages/trips/assign-driver.js']) 
@vite(['resources/js/admin/pages/pagination.js']) 
@vite(['resources/js/admin/pages/alert.js'])
@vite(['resources/js/admin/pages/no-data.js']) 