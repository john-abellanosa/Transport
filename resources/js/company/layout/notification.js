document.addEventListener("DOMContentLoaded", () => {
    const notificationBtnCompany = document.getElementById("notificationBtnCompany");
    const notificationPanelCompany = document.getElementById("notificationPanelCompany");
    const notificationOverlayCompany = document.getElementById("notificationOverlayCompany");
    const closePanelCompany = document.getElementById("closePanelCompany");
    const notifBadgeCompany = document.getElementById("notifBadgeCompany");
    const markAllReadCompany = document.getElementById("markAllReadCompany");
    const notificationsListCompany = document.getElementById("notificationsListCompany");

    let panelOpenCompany = false;
    let companyNotifications = [];

    // Fetch notifications from server
    function loadCompanyNotifications() {
        fetch("/company/notifications/fetch")
            .then(res => res.json())
            .then(data => {
                companyNotifications = data.notifications;
                updateCompanyBadge(data.unreadCount);
                renderCompanyNotifications();
            });
    }

    function formatCompanyTimestamp(datetime) {
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit', 
            hour12: true 
        };
        return new Date(datetime).toLocaleString('en-US', options).replace(',', ' at');
    }

    // Render notifications
    function renderCompanyNotifications() {
        notificationsListCompany.innerHTML = '';
        if (companyNotifications.length === 0) {
            notificationsListCompany.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-title">No notifications</div>
                    <div class="empty-state-message">You're all caught up!</div>
                </div>
            `;
            return;
        }

        companyNotifications.forEach(n => {
            const item = document.createElement('div');
            item.className = `notification-item ${n.status === 'new' ? 'unread' : ''}`;
            item.dataset.id = n.id;
            item.innerHTML = `
                <div class="notification-title">${n.title}</div>
                <div class="notification-message">${n.message}</div>
                <div class="notification-datetime">${formatCompanyTimestamp(n.created_at)}</div>
            `;
            notificationsListCompany.appendChild(item);

            // Mark single notification as read on click
            item.addEventListener("click", () => {
                if (n.status === 'new') {
                    n.status = 'read';
                    item.classList.remove('unread');
                    updateCompanyBadge();
                    fetch('/company/notifications/mark-as-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ids: [n.id] })
                    });
                }
            });
        });
    }

    // Update badge
    function updateCompanyBadge(count = null) {
        const unreadCount = count !== null ? count : companyNotifications.filter(n => n.status === 'new').length;
        notifBadgeCompany.textContent = unreadCount > 0 ? unreadCount : '';
        notifBadgeCompany.style.display = unreadCount > 0 ? 'flex' : 'none';
    }

    // Panel open/close
    notificationBtnCompany.addEventListener("click", () => {
        notificationPanelCompany.classList.add("open");
        notificationOverlayCompany.classList.add("active");
        panelOpenCompany = true;
        document.body.style.overflow = "hidden";
    });

    function closeCompanyNotificationPanel() {
        notificationPanelCompany.classList.remove("open");
        notificationOverlayCompany.classList.remove("active");
        panelOpenCompany = false;
        document.body.style.overflow = "";
        // Mark all visible notifications as read
        const newIds = companyNotifications.filter(n => n.status === 'new').map(n => n.id);
        if (newIds.length) {
            fetch('/company/notifications/mark-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: newIds })
            }).then(() => {
                companyNotifications.forEach(n => n.status = 'read');
                renderCompanyNotifications();
                updateCompanyBadge();
            });
        }
    }

    closePanelCompany.addEventListener("click", closeCompanyNotificationPanel);
    notificationOverlayCompany.addEventListener("click", closeCompanyNotificationPanel);
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && panelOpenCompany) closeCompanyNotificationPanel();
    });

    // Filter (all/unread)
    document.querySelectorAll("#notificationPanelCompany .filter-link").forEach(link => {
        link.addEventListener("click", function () {
            document.querySelectorAll("#notificationPanelCompany .filter-link").forEach(l => l.classList.remove("active"));
            this.classList.add("active");
            const tab = this.dataset.tab;
            document.querySelectorAll("#notificationsListCompany .notification-item").forEach(item => {
                const n = companyNotifications.find(n => n.id == item.dataset.id);
                item.style.display = (tab === 'all' || n.status === 'new') ? 'block' : 'none';
            });
        });
    });

    // Mark all read button
    markAllReadCompany.addEventListener("click", () => {
        const newIds = companyNotifications.filter(n => n.status === 'new').map(n => n.id);
        if (newIds.length) {
            fetch('/company/notifications/mark-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: newIds })
            }).then(() => {
                companyNotifications.forEach(n => n.status = 'read');
                renderCompanyNotifications();
                updateCompanyBadge();
            });
        }
    });

    // Initial load
    loadCompanyNotifications();
});