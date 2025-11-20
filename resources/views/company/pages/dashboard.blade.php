@extends('company.layout.layout')

@section('title', 'Company Panel - Dashboard')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title', 'Company Panel - Dashboard')</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="{{ asset('css/company/pages/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/pages/alert.css') }}">
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

        <div class="dashboard-container">
            <div class="cards-grid">
                {{-- TOTAL TRIPS --}}
                <div class="stat-card">
                    <span class="material-symbols-outlined card-icon">local_shipping</span>
                    <div class="card-header">
                        <span>Total trips</span>
                    </div>
                    <div class="card-main">
                        <h3 class="card-value">{{ $totalTrips }}</h3>
                        <span class="status {{ $totalPercent >= 0 ? 'up' : 'down' }}">
                            <i class="fas {{ $totalPercent >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($totalPercent) }}%
                        </span>
                    </div>
                    <p class="card-footer">
                        Last month ({{ $lastMonth->format('F') }}):
                        <span class="highlight">{{ $lastMonthTotal }}</span>
                    </p>
                </div>

                {{-- AVAILABLE / PENDING TRIPS --}}
                <div class="stat-card">
                    <span class="material-symbols-outlined card-icon">pending_actions</span>
                    <div class="card-header">
                        <span>Available trips</span>
                    </div>
                    <div class="card-main">
                        <h3 class="card-value">{{ $availableTrips }}</h3>
                        <span class="status {{ $availablePercent >= 0 ? 'up' : 'down' }}">
                            <i class="fas {{ $availablePercent >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($availablePercent) }}%
                        </span>
                    </div>
                    <p class="card-footer">
                        Last month ({{ $lastMonth->format('F') }}):
                        <span class="highlight">{{ $lastMonthAvailable }}</span>
                    </p>
                </div>

                {{-- BACKLOAD TRIPS --}}
                <div class="stat-card">
                    <span class="material-symbols-outlined card-icon">inventory_2</span>
                    <div class="card-header">
                        <span>Backload trips</span>
                    </div>
                    <div class="card-main">
                        <h3 class="card-value">{{ $backloadTrips }}</h3>

                        @php
                            $isBackloadGood = $backloadPercent < 0;
                        @endphp

                        <span class="status {{ $isBackloadGood ? 'up' : 'down' }}">
                            <i class="fas {{ $isBackloadGood ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($backloadPercent) }}%
                        </span>
                    </div>
                    <p class="card-footer">
                        Last month ({{ $lastMonth->format('F') }}):
                        <span class="highlight">{{ $lastMonthBackload }}</span>
                    </p>
                </div>

                {{-- COMPLETED TRIPS --}}
                <div class="stat-card">
                    <span class="material-symbols-outlined card-icon">assignment_turned_in</span>
                    <div class="card-header">
                        <span>Completed trips</span>
                    </div>
                    <div class="card-main">
                        <h3 class="card-value">{{ $completedTrips }}</h3>
                        <span class="status {{ $completedPercent >= 0 ? 'up' : 'down' }}">
                            <i class="fas {{ $completedPercent >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($completedPercent) }}%
                        </span>
                    </div>
                    <p class="card-footer">
                        Last month ({{ $lastMonth->format('F') }}):
                        <span class="highlight">{{ $lastMonthCompleted }}</span>
                    </p>
                </div>

                {{-- CANCELLED TRIPS --}}
                <div class="stat-card">
                    <span class="material-symbols-outlined card-icon">cancel</span>
                    <div class="card-header">
                        <span>Cancelled trips</span>
                    </div>
                    <div class="card-main">
                        <h3 class="card-value">{{ $cancelledTripsCount }}</h3>

                        @php
                            $isCancelledGood = $cancelledPercent < 0;
                        @endphp

                        <span class="status {{ $isCancelledGood ? 'up' : 'down' }}">
                            <i class="fas {{ $isCancelledGood ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($cancelledPercent) }}%
                        </span>
                    </div>
                    <p class="card-footer">
                        Last month ({{ $lastMonth->format('F') }}):
                        <span class="highlight">{{ $lastMonthCancelled }}</span>
                    </p>
                </div>
            </div>


            <div class="chart-section">
                <div class="chart-box">
                    <div class="chart-header">
                        <h3>Trucking Company Performance</h3>
                        <div class="chart-legend">
                            <div><span class="dot green"></span> Completed trips</div>
                            <div><span class="dot red"></span> Cancelled trips</div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="tripsChart" data-completed='@json($completedData)'
                            data-cancelled='@json($cancelledData)'>
                        </canvas>
                    </div>

                </div>
            </div>
        </div>

    </body>

    </html>
@endsection

@vite(['resources/js/admin/pages/alert.js'])
@vite(['resources/js/company/pages/dashboard.js'])