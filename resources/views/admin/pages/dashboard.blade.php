@extends('admin.layout.layout')

@section('title', 'Admin Panel - Dashboard')
@section('topbar_title', 'Dashboard Overview')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title', 'Admin Panel - Dashboard')</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="{{ asset('css/admin/pages/dashboard.css') }}">
    </head>

    <body>

        <div class="dashboard-container">
            <div class="cards-grid">
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
                        Last month ({{ $lastMonth->format('F') }}): <span class="highlight">{{ $lastMonthTotal }}</span>
                    </p>
                </div>

                <div class="stat-card">
                    <span class="material-symbols-outlined card-icon">pending_actions</span>
                    <div class="card-header">
                        <span>Pending trips</span>
                    </div>
                    <div class="card-main">
                        <h3 class="card-value">{{ $pendingTrips }}</h3>
                        <span class="status {{ $pendingPercent >= 0 ? 'up' : 'down' }}">
                            <i class="fas {{ $pendingPercent >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($pendingPercent) }}%
                        </span>
                    </div>
                    <p class="card-footer">
                        Last month ({{ $lastMonth->format('F') }}): <span class="highlight">{{ $lastMonthPending }}</span>
                    </p>
                </div>

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
                        Last month ({{ $lastMonth->format('F') }}): <span
                            class="highlight">{{ $lastMonthCompleted }}</span>
                    </p>
                </div>

                <div class="stat-card">
                    <span class="material-symbols-outlined card-icon">cancel</span>
                    <div class="card-header">
                        <span>Cancelled trips</span>
                    </div>
                    <div class="card-main">
                        <h3 class="card-value">{{ $cancelledTrips }}</h3>

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
                            data-cancelled='@json($cancelledData)'></canvas>
                    </div>
                </div>
            </div>
        </div>

    </body>

    </html>
@endsection

@vite(['resources/js/admin/pages/dashboard.js'])