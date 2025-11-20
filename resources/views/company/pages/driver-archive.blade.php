@extends('company.layout.layout')

@section('title', 'Company Panel - Driver')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/driver/driver.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/driver/archive-driver.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/no-data.css') }}">
        <link rel="stylesheet" href="{{ asset('css/company/pages/restore.css') }}">
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
                        <a href="{{ route('company.driver') }}" class="archive-btn" aria-label="View Archive">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>
                            Back
                        </a>
                    </div>
                </div>

                <div class="company-table-scroll">
                    <table class="styled-table" id="dataTable">
                        <thead>
                            <tr>
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
                            @foreach ($archivedDrivers as $drivers)
                                <tr data-id="{{ $drivers->id }}" data-name="{{ $drivers->name }}"
                                    data-email="{{ $drivers->email }}" data-address="{{ $drivers->address }}"
                                    data-owner="{{ $drivers->owner }}" data-number="{{ $drivers->contact }}"
                                    data-municipality="{{ $drivers->municipality }}">
                                    <td>{{ $drivers->company->name ?? 'No Company' }}</td>
                                    <td>{{ $drivers->name }}</td>
                                    <td>{{ $drivers->email }}</td>
                                    <td>+63 {{ $drivers->number }}</td>
                                    <td>{{ $drivers->address }}</td>
                                    <td class="actions">
                                        <form action="{{ route('company.driver.restore', $drivers->id) }}" method="POST"
                                            class="restoreForm" style="display:inline;">
                                            @csrf
                                            <button class="restoreBtn" title="Restore" type="button">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                        </form>
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
                        <p>No Driver Available</p>
                    </div>
                </div>

                <div class="pagination">
                    <button class="disabled-button prev"><i class="fa fa-angle-left"></i></button>
                    <button class="active-button next"><i class="fa fa-angle-right"></i></button>
                </div>
            </div>
        </div>

        <div id="restoreModal" class="restore-modal">
            <div class="restore-modal-box">
                <div class="restore-modal-header">
                    <h2>Confirm Restoration</h2>
                </div>

                <div class="restore-modal-body">
                    <p>Are you sure you want to restore this driver? It will become active again in the system.</p>
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

@vite(['resources/js/admin/pages/alert.js'])
@vite(['resources/js/company/pages/no-data.js'])
@vite(['resources/js/company/pages/pagination.js'])
@vite(['resources/js/admin/pages/search-bar.js'])
@vite(['resources/js/company/pages/driver/restore-driver.js'])