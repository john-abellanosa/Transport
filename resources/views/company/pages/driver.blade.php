@extends('company.layout.layout')

@section('title', 'Company Panel - Driver')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title', 'Company Panel - Driver')</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/driver/driver.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/no-data.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/driver/add-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/driver/edit-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/delete-modal.css') }}">
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

        <div class="admin-driver-wrapper">
            <div class="table-container">
                <div class="table-header">
                    <div class="search-box" role="search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input type="text" placeholder="Search" aria-label="Search" id="searchInput">
                        <span class="clear-btn" id="clearSearch" title="Clear">&times;</span>
                    </div>

                    <div class="action-buttons">
                        <button class="add-btn" id="addDriverBtn">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Add Driver
                        </button>

                        <a href="{{ route('company.driver.archive') }}" class="archive-btn" aria-label="View Archive">
                            <i class="fa fa-archive" aria-hidden="true"></i>
                            Archive
                        </a>
                    </div>
                </div>

                <div class="driver-table-scroll">
                    <table class="styled-table" id="dataTable">
                        <thead>
                            <tr>
                                <th class="left-side-th">Company Name</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Number</th>
                                <th>Address</th>
                                <th class="right-side-th">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($drivers as $driver)
                                <tr>
                                    <td>{{ $driver->company->name ?? 'No Company' }}</td>
                                    <td>{{ $driver->name }}</td>
                                    <td>{{ $driver->email }}</td>
                                    <td>+63 {{ $driver->number }}</td>
                                    <td>{{ $driver->address }}</td>
                                    <td class="action-buttons">
                                        <button type="button" class="edit-btn" data-id="{{ $driver->id }}"
                                            data-name="{{ $driver->name }}" data-email="{{ $driver->email }}"
                                            data-number="{{ $driver->number }}" data-address="{{ $driver->address }}">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        <button type="button" class="delete" title="Delete"
                                            data-id="{{ $driver->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div id="noData" class="no-data">
                        <img src="{{ asset('img/no-data.png') }}" alt="No Company">
                        <p>No Driver Available</p>
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


        {{-- Add Modal --}}
        <div id="driverAddModal" class="driver-modal">
            <div class="driver-modal-content">
                <span class="driver-close-btn">&times;</span>
                <h2>Add Driver</h2>
                <form id="driverAddForm" method="POST" action="{{ route('drivers.store') }}">
                    @csrf

                    <div class="form-group">
                        <label for="driverName">Name</label>
                        <input type="text" name="name" id="driverName" placeholder="Enter driver name"
                            maxlength="50">
                        <small class="input-error" id="driverNameError">Name cannot be empty</small>
                    </div>

                    <div class="form-group">
                        <label for="driverEmail">Email</label>
                        <input type="text" name="email" id="driverEmail" placeholder="Enter driver email"
                            maxlength="50">
                        <small class="input-error" id="driverEmailError">Enter a valid email</small>
                    </div>

                    <div class="form-group">
                        <label for="driverNumber">Owner Number</label>
                        <div class="input-with-prefix">
                            <span class="prefix">+63</span>
                            <input id="driverNumber" name="number" type="text" placeholder="9123456789"
                                maxlength="10">
                        </div>
                        <small class="input-error" id="driverNumberError">Must be 10 digits (e.g.,
                            9123456789)</small>
                    </div>

                    <div class="form-group">
                        <label for="driverAddress">Address</label>
                        <input type="text" name="address" id="driverAddress"
                            placeholder="Enter driver address" maxlength="100">
                        <small class="input-error" id="driverAddressError">Address cannot be empty</small>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="cancel-btn">Cancel</button>
                        <button type="submit" class="submit-btn">Add</button>
                    </div>
                </form>
            </div>
        </div>


        {{-- Edit Modal --}}
        <div id="driverEditModal" class="driver-edit-modal">
            <div class="driver-edit-modal-content">
                <span class="driver-edit-close-btn">&times;</span>
                <h2>Edit Driver</h2>
                <form id="driverEditForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="editDriverName">Name</label>
                        <input type="text" name="name" id="editDriverName" maxlength="50">
                        <small class="input-error" id="editDriverNameError">Name cannot be empty</small>
                    </div>

                    <div class="form-group">
                        <label for="editDriverEmail">Email</label>
                        <input type="text" name="email" id="editDriverEmail" maxlength="50">
                        <small class="input-error" id="editDriverEmailError">Enter a valid email</small>
                    </div>

                    <div class="form-group">
                        <label for="editDriverNumber">Owner Number</label>
                        <div class="input-with-prefixs">
                            <span class="prefixs">+63</span>
                            <input id="editDriverNumber" name="number" type="text" maxlength="10">
                        </div>
                        <small class="input-error" id="editDriverNumberError">
                            Must be 10 digits (e.g., 9123456789)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="editDriverAddress">Address</label>
                        <input type="text" name="address" id="editDriverAddress" maxlength="100">
                        <small class="input-error" id="editDriverAddressError">Address cannot be empty</small>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="cancel-edit-btn">Cancel</button>
                        <button type="submit" class="submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>


        {{-- Delete Modal --}}
        <div id="deleteModal" class="delete-modal">
            <div class="delete-modal-box">
                <div class="delete-modal-header">
                    <h2>Confirm Delete</h2>
                </div>
                <div class="delete-modal-body">
                    <p>Are you sure you want to archive this driver? You can restore it later from the archive page.
                    </p>
                </div>
                <div class="delete-modal-actions">
                    <button type="button" class="delete-cancel">Cancel</button>
                    <button type="button" class="delete-confirm">Delete</button>
                </div>
            </div>
        </div>

    </body>

    </html>
@endsection


@vite(['resources/js/admin/pages/alert.js'])
@vite(['resources/js/company/pages/no-data.js'])
@vite(['resources/js/company/pages/pagination.js'])
@vite(['resources/js/admin/pages/search-bar.js'])
@vite(['resources/js/company/pages/driver/add-driver.js'])
@vite(['resources/js/company/pages/driver/edit-driver.js'])
@vite(['resources/js/company/pages/driver/delete-driver.js'])
