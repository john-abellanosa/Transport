@extends('driver.layout')

@section('title', 'Driver Panel - Trips')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title', 'Driver Panel - Trips')</title>
        <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/no-data.css') }}">
        <link rel="stylesheet" href="{{ asset('css/driver/pages/trips/trips.css') }}">
        <link rel="stylesheet" href="{{ asset('css/driver/pages/trips/view-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/driver/pages/trips/confirmation-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/driver/pages/trips/cancel-modal.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="driver-trips-update-url" content="{{ route('driver.trips.updateStatus') }}">
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
                                    <td>{{ $trip->clientNumber }}</td>
                                    <td>{{ $trip->destination }}</td>
                                    <td>{{ \Carbon\Carbon::parse($trip->schedule)->format('F d, Y') }}</td>
                                    <td>
                                        <span class="badge {{ strtolower($trip->status) }}">{{ $trip->status }}</span>
                                    </td> 
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

                                        @php 
                                            $today = \Carbon\Carbon::now('Asia/Manila')->startOfDay(); 
                                            $tripSchedule = \Carbon\Carbon::parse($trip->schedule)->setTimezone('Asia/Manila')->startOfDay();
                                        @endphp
 
                                        @if (!in_array($trip->status, ['Completed', 'Cancelled']))
                                            @if ($tripSchedule->lte($today))
                                                <button class="complete" title="Complete"
                                                    onclick="showModal('{{ $trip->transactionId }}')">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            @endif
                                            
                                            <button class="cancel" title="Cancel"
                                                onclick="openMainModal('{{ $trip->transactionId }}')">
                                                <i class="fas fa-times-circle"></i>
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

        <!-- ✅ Confirmation Modal -->
        <div class="modal-overlay" id="modalOverlay">
            <div class="modal">
                <div class="main-modal-header">
                    <div class="modal-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="main-modal-title">Confirm Completion</h3>
                </div>
                <div class="main-modal-body">
                    <p class="main-modal-message">
                        Are you sure you want to mark this trip as complete? This action cannot be undone.
                    </p>
                    <div class="modal-actions">
                        <button class="btn btn-secondary" onclick="hideModal()">Cancel</button>
                        <button class="btn btn-primary" onclick="showPhotoModal()">Continue</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Photo Upload Modal -->
        <div class="modal-overlay" id="photoModalOverlay">
            <div class="modal">
                <div class="modal-header">
                    <div class="modal-icon photo-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h3 class="modal-title">Upload Proof of Completion</h3>
                    <p id="photoModalTransaction" class="transaction-text"></p>
                </div>
                <div class="modal-body">
                    <p class="modal-message">
                        Please upload a photo as proof of trip completion.
                    </p>

                    <div class="photo-options">
                        <button class="photo-option-btn" onclick="openCamera()">
                            <div class="option-icon">
                                <i class="fas fa-camera"></i>
                            </div>
                            <span>Take Photo</span>
                        </button>
                        <button class="photo-option-btn" onclick="openGallery()">
                            <div class="option-icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <span>Choose from Gallery</span>
                        </button>
                    </div>

                    <div class="photo-preview-container" id="photoPreviewContainer" style="display: none;">
                        <div class="photo-preview">
                            <img id="previewImage" src="" alt="Preview">
                            <button class="remove-photo" onclick="removePhoto()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <input type="file" id="cameraInput" accept="image/*" capture style="display: none;">
                    <input type="file" id="galleryInput" accept="image/*" style="display: none;">

                    <div class="modal-actions">
                        <button class="btn btn-secondary" onclick="hidePhotoModal()">Back</button>
                        <button class="btn btn-primary" id="submitBtn" onclick="confirmComplete()" disabled>
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cancel modal  --}}
        <div class="modal-overlay" id="mainModalOverlay">
            <div class="modal">
                <div class="main-modal-header">
                    <div class="modal-icon cancel-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3 class="main-modal-title">Cancel Trip</h3>

                    <button class="modal-close-btn" onclick="hideMainModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="main-modal-body">
                    <p class="main-modal-message">
                        What would you like to do with this trip?
                    </p>
                    <div class="modal-actions">
                        <button class="btn btn-backload" onclick="showBackloadModal()">
                            Backload
                        </button>
                        <button class="btn btn-cancel" onclick="showCancelModal()">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backload Modal -->
        <div class="modal-overlay" id="backloadModalOverlay">
            <div class="modal form-modal">
                <div class="modal-header">
                    <div class="modal-icon backload-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h3 class="modal-title">Backload Trip</h3>
                    <p id="backloadModalTransactionId" class="modal-transaction-id"></p>
                </div>
                <div class="modal-body">
                    <p class="modal-message">
                        Please provide remarks for backloading this trip. This will help with future processing.
                    </p>
                    <div class="form-group">
                        <label for="backloadRemarks" class="form-label">Remarks</label>
                        <textarea id="backloadRemarks" class="form-input" placeholder="Enter your remarks for backloading this trip..."
                            required></textarea>
                        <div id="backloadError" class="error-message"></div>
                    </div>

                    <!-- Photo Upload Section -->
                    <div class="form-group">
                        <label class="form-label">Proof Photo</label>
                        <div class="photo-options">
                            <button class="photo-option-btn" type="button" onclick="openBackloadCamera()">
                                <div class="option-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <span>Take Photo</span>
                            </button>
                            <button class="photo-option-btn" type="button" onclick="openBackloadGallery()">
                                <div class="option-icon">
                                    <i class="fas fa-images"></i>
                                </div>
                                <span>Choose from Gallery</span>
                            </button>
                        </div>

                        <div class="photo-preview-container" id="backloadPhotoPreviewContainer" style="display: none;">
                            <div class="photo-preview">
                                <img id="backloadPreviewImage" src="" alt="Preview">
                                <button class="remove-photo" type="button" onclick="removeBackloadPhoto()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <input type="file" id="backloadCameraInput" accept="image/*" capture style="display: none;">
                        <input type="file" id="backloadGalleryInput" accept="image/*" style="display: none;">
                    </div>

                    <div class="modal-actions">
                        <button class="btn btn-secondary" onclick="hideBackloadModal()">
                            Cancel
                        </button>
                        <button class="btn btn-backload" onclick="submitBackload()">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Reason Modal -->
        <div class="modal-overlay" id="cancelModalOverlay">
            <div class="modal form-modal">
                <div class="modal-header">
                    <div class="modal-icon cancel-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3 class="modal-title">Cancel Trip</h3>
                    <p id="cancelModalTransactionId" class="modal-transaction-id"></p>
                </div>
                <div class="modal-body">
                    <p class="modal-message">
                        Please provide a reason for cancelling this trip. This information is required for record keeping.
                    </p>
                    <div class="form-group">
                        <label for="cancelReason" class="form-label">Cancel Reason</label>
                        <textarea id="cancelReason" class="form-input" placeholder="Enter the reason for cancelling this trip..." required></textarea>
                        <div id="cancelError" class="error-message"></div>
                    </div>

                    <!-- Photo Upload Section -->
                    <div class="form-group">
                        <label class="form-label">Proof Photo</label>
                        <div class="photo-options">
                            <button class="photo-option-btn" type="button" onclick="openCancelCamera()">
                                <div class="option-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <span>Take Photo</span>
                            </button>
                            <button class="photo-option-btn" type="button" onclick="openCancelGallery()">
                                <div class="option-icon">
                                    <i class="fas fa-images"></i>
                                </div>
                                <span>Choose from Gallery</span>
                            </button>
                        </div>

                        <div class="photo-preview-container" id="cancelPhotoPreviewContainer" style="display: none;">
                            <div class="photo-preview">
                                <img id="cancelPreviewImage" src="" alt="Preview">
                                <button class="remove-photo" type="button" onclick="removeCancelPhoto()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <input type="file" id="cancelCameraInput" accept="image/*" capture style="display: none;">
                        <input type="file" id="cancelGalleryInput" accept="image/*" style="display: none;">
                    </div>

                    <div class="modal-actions">
                        <button class="btn btn-secondary" onclick="hideCancelModal()">
                            Cancel
                        </button>
                        <button class="btn btn-cancel" onclick="submitCancel()">
                            Submit
                        </button>
                    </div>
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
                    {{-- Content  --}}
                </div>
            </div>
        </div>

    </body>

    </html>
@endsection
 
@vite(['resources/js/company/pages/trips/trips.js'])
@vite(['resources/js/admin/pages/pagination.js'])
@vite(['resources/js/admin/pages/no-data.js'])
@vite(['resources/js/driver/pages/trips/confirmation-modal.js'])
@vite(['resources/js/driver/pages/trips/cancel-modal.js'])
@vite(['resources/js/driver/pages/trips/view-modal.js'])
@vite(['resources/js/admin/pages/alert.js'])
