@extends('admin.layout.layout')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title', 'Admin Panel - Driver')</title>
        <link rel="stylesheet" href="{{ asset('css/admin/pages/driver.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/no-data.css') }}">
    </head>

    <body>
        <div class="admin-driver-wrapper">
            <div class="table-container">
                <div class="table-header">
                    <div class="table-company-dropdown" id="companyDropdown">
                        <button class="company-filter-btn" id="companyDropdownBtn" aria-haspopup="listbox"
                            aria-expanded="false" title="Filter by company">
                            Filter Company Name
                            <i class="fa fa-caret-down" aria-hidden="true" style="margin-left: 5px;"></i>
                        </button>
                        <ul class="company-dropdown-tooltip" id="companyDropdownMenu" role="listbox">
                            <li data-value="all" class="active">All</li>
                            @foreach ($companies as $company)
                                <li data-value="{{ strtolower($company->name) }}">{{ $company->name }} - {{ $company->branch }}</li>
                            @endforeach
                        </ul>
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
                                <th class="right-side-th">Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($drivers as $driver)
                                <tr>
                                    <td>{{ $driver->company->name ?? 'No Company' }} - {{ $driver->company->branch }}</td>
                                    <td>{{ $driver->name }}</td>
                                    <td>{{ $driver->email }}</td>
                                    <td>{{ $driver->number }}</td>
                                    <td>{{ $driver->address }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

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
    </body>

    </html>
@endsection

@vite(['resources/js/admin/pages/search-bar.js'])
@vite(['resources/js/admin/pages/no-data.js'])
@vite(['resources/js/admin/pages/pagination.js'])
@vite(['resources/js/admin/pages/companyFilter.js'])
