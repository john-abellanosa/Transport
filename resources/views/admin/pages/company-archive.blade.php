@extends('admin.layout.layout')

@section('title', 'Admin Login - Company')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="{{ asset('css/admin/pages/company/archive-company.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
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

        <div class="admin-company-wrapper">
            <div class="table-container">
                <div class="table-header">
                    <div class="search-box" role="search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input type="text" placeholder="Search" aria-label="Search" id="searchInput">
                        <span class="clear-btn" id="clearSearch" title="Clear">&times;</span>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('admin.company') }}" class="archive-btn" aria-label="View Archive">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>
                            Back
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
                            @foreach ($archivedCompanies as $company)
                                <tr data-id="{{ $company->id }}" data-name="{{ $company->name }}"
                                    data-email="{{ $company->email }}" data-address="{{ $company->address }}"
                                    data-owner="{{ $company->owner }}" data-number="{{ $company->contact }}"
                                    data-municipality="{{ $company->municipality }}">
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->email }}</td>
                                    <td>{{ $company->address }}</td>
                                    <td>{{ $company->owner }}</td>
                                    <td>+63 {{ $company->contact }}</td>
                                    <td>{{ $company->municipality }}</td>
                                    <td class="actions">
                                        <form action="{{ route('admin.companies.restore', $company->id) }}" method="POST"
                                            class="restoreForm" style="display:inline;">
                                            @csrf
                                            <button class="restoreBtn" title="Restore" type="button"
                                                data-id="{{ $company->id }}">
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
                        <p>No Company Available</p>
                    </div>
                </div>

                <div class="pagination">
                    <button class="disabled-button prev"><i class="fa fa-angle-left"></i></button>
                    <button class="active-button next"><i class="fa fa-angle-right"></i></button>
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

@vite(['resources/js/admin/pages/no-data.js'])
@vite(['resources/js/admin/pages/pagination.js'])
@vite(['resources/js/admin/pages/search-bar.js'])
@vite(['resources/js/admin/pages/alert.js'])
@vite(['resources/js/admin/pages/company/restore-company.js'])
