<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/express.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/admin/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/layout/notification.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/layout/logout-modal.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="topbar">
        <button class="menu-toggle" id="menuToggleBtn" aria-label="Open sidebar">
            <i class="fa fa-bars"></i>
        </button>

        <div class="page-title" id="pageTitle">
            @if (Request::is('admin/dashboard'))
                Dashboard
            @elseif(Request::is('admin/company'))
                Company
            @elseif(Request::is('admin/driver'))
                Drivers
            @elseif(Request::is('admin/trips'))
                Trips
            @elseif(Request::is('admin/history'))
                Records
            @elseif(Request::is('admin/companies/archive'))
                Archived Companies
            @elseif(Request::is('admin/trips/archive'))
                Archived Trips
            @else
                Admin Panel
            @endif
        </div>

        <div class="topbar-icons">
            <button class="notification-btn" id="notificationBtn" aria-label="Notifications">
                <img src="{{ asset('img/notification.png') }}" alt="Notifications" class="notification-icon">
                <span class="notif-badge" id="notifBadge"></span>
            </button>
        </div>

        <div class="notification-overlay" id="notificationOverlay"></div>

        <!-- Notification Panel -->
        <div class="notification-panel" id="notificationPanel">
            <!-- Panel Header -->
            <div class="panel-header">
                <div class="panel-header-top">
                    <div class="panel-title">Notifications</div>
                    <button class="close-panel" id="closePanel">×</button>
                </div>
                <div class="filter-section">
                    <div class="filter-links">
                        <span class="filter-link active" data-tab="all">All</span>
                        <span class="filter-link" data-tab="unread">Unread</span>
                    </div>
                    <span class="mark-all-link" id="markAllRead">Mark all as read</span>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="notifications-list" id="notificationsList">
                <p class="loading-text">Loading notifications...</p>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" onclick="closeSidebar()" id="sidebarOverlay"></div>

    <div class="sidebar" id="sidebar">
        <button class="sidebar-close" id="sidebarCloseBtn" aria-label="Close sidebar">
            <i class="fa fa-xmark"></i>
        </button>

        <div class="logo">
            <img src="{{ asset('img/logo.png') }}" alt="">
        </div>

        <div class="section">
            <div class="menu">MENU</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined card-icon">dashboard</span> Dashboard
            </a>

            <a href="{{ route('admin.company') }}" class="{{ Request::is('admin/company') ? 'active' : '' }}">
                <span class="material-symbols-outlined card-icon">apartment</span> Company
            </a>

            <a href="{{ route('admin.driver') }}" class="{{ Request::is('admin/driver') ? 'active' : '' }}">
                <span class="material-symbols-outlined card-icon">local_shipping</span> Driver
            </a>

            <a href="{{ route('admin.trips') }}" class="{{ Request::is('admin/trips') ? 'active' : '' }}">
                <span class="material-symbols-outlined card-icon">delivery_truck_speed</span> Trips
            </a>

            <a href="{{ route('admin.history') }}" class="{{ Request::is('admin/history') ? 'active' : '' }}">
                <span class="material-symbols-outlined card-icon">history</span> Records
            </a>
        </div>

        <div class="settings">
            <a href="#" class="logout-link">
                <span class="material-symbols-outlined card-icon">logout</span> Logout
            </a>
        </div>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div id="logoutModal" class="logout-modal">
        <div class="logout-modal-box">
            <div class="logout-modal-header">
                <h2>Confirm</h2>
            </div>
            <div class="logout-modal-body">
                <p>Are you sure you want to Logout?</p>
            </div>
            <div class="logout-modal-actions">
                <button type="button" class="logout-cancel">Cancel</button>

                <!-- ✅ Logout form -->
                <form id="logoutForm" action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="logout-confirm">Logout</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>

@vite(['resources/js/admin/layout/toggle-sidebar.js'])
@vite(['resources/js/admin/layout/logout-modal.js'])
@vite(['resources/js/admin/layout/notification.js'])
