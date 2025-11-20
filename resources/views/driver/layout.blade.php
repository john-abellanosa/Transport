<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/express.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/driver/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/driver/layout/logout-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/driver/layout/notification.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="topbar">
        <button class="menu-toggle" id="menuToggleBtn" aria-label="Open sidebar">
            <i class="fa fa-bars"></i>
        </button>
        <div class="page-title" id="pageTitle">
            @if (Request::is('driver/dashboard'))
                Dashboard
            @elseif(Request::is('driver/trips'))
                Trips
            @elseif(Request::is('driver/history'))
                Records
            @else
                Driver Panel
            @endif
        </div>

        <div class="topbar-icons">
            <button class="notification-btn" id="notificationBtnDriver" aria-label="Notifications">
                <img src="{{ asset('img/notification.png') }}" alt="Notifications" class="notification-icon">
                <span class="notif-badge" id="notifBadgeDriver"></span>
            </button>
        </div>

        <div class="notification-overlay" id="notificationOverlayDriver"></div>

        <!-- Driver Notification Panel -->
        <div class="notification-panel" id="notificationPanelDriver">
            <!-- Panel Header -->
            <div class="panel-header">
                <div class="panel-header-top">
                    <div class="panel-title">Notifications</div>
                    <button class="close-panel" id="closePanelDriver">×</button>
                </div>
                <div class="filter-section">
                    <div class="filter-links">
                        <span class="filter-link active" data-tab="all">All</span>
                        <span class="filter-link" data-tab="unread">Unread</span>
                    </div>
                    <span class="mark-all-link" id="markAllReadDriver">Mark all as read</span>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="notifications-list" id="notificationsListDriver">
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
            <a href="{{ route('driver.dashboard') }}" class="{{ Request::is('driver/dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined card-icon">space_dashboard</span> Dashboard
            </a>

            <a href="{{ route('driver.trips') }}" class="{{ Request::is('driver/trips') ? 'active' : '' }}">
                <span class="material-symbols-outlined card-icon">delivery_truck_speed</span> Trips
            </a>

            <a href="{{ route('driver.history') }}" class="{{ Request::is('driver/history') ? 'active' : '' }}">
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


                <form id="logoutForm" action="{{ route('driver.logout') }}" method="POST"
                    style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-confirm">Logout</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>

@vite(['resources/js/company/layout/toggle-sidebar.js'])
@vite(['resources/js/company/layout/logout-modal.js'])
@vite(['resources/js/driver/pages/notification.js'])
