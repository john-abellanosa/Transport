document.addEventListener("DOMContentLoaded", () => {
    const notificationBtn = document.getElementById("notificationBtn");
    const notificationPanel = document.getElementById("notificationPanel");
    const notificationOverlay = document.getElementById("notificationOverlay");
    const closePanel = document.getElementById("closePanel");
    const notifBadge = document.getElementById("notifBadge");
    const markAllRead = document.getElementById("markAllRead");
    const notificationsList = document.getElementById("notificationsList");

    let panelOpen = false;
    let notifications = [];

    // Fetch notifications from server
    function loadNotifications() {
        fetch("/admin/notifications")
            .then(res => res.json())
            .then(data => {
                notifications = data.notifications;
                updateBadge(data.unreadCount);
                renderNotifications();
            });
    }

    function formatTimestamp(datetime) {
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
    function renderNotifications() {
        notificationsList.innerHTML = '';
        if (notifications.length === 0) {
            notificationsList.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-title">No notifications</div>
                    <div class="empty-state-message">You're all caught up!</div>
                </div>
            `;
            return;
        }

        notifications.forEach(n => {
            const item = document.createElement('div');
            item.className = `notification-item ${n.status === 'new' ? 'unread' : ''}`;
            item.dataset.id = n.id;
            item.innerHTML = `
                <div class="notification-title">${n.title}</div>
                <div class="notification-message">${n.message}</div>
                <div class="notification-datetime">${formatTimestamp(n.created_at)}</div>
            `;
            notificationsList.appendChild(item);

            // Mark single notification as read on click
            item.addEventListener("click", () => {
                if (n.status === 'new') {
                    n.status = 'read';
                    item.classList.remove('unread');
                    updateBadge();
                    fetch('/admin/notifications/mark-read', {
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
    function updateBadge(count = null) {
        const unreadCount = count !== null ? count : notifications.filter(n => n.status === 'new').length;
        notifBadge.textContent = unreadCount > 0 ? unreadCount : '';
        notifBadge.style.display = unreadCount > 0 ? 'flex' : 'none';
    }

    // Panel open/close
    notificationBtn.addEventListener("click", () => {
        notificationPanel.classList.add("open");
        notificationOverlay.classList.add("active");
        panelOpen = true;
        document.body.style.overflow = "hidden";
    });

    function closeNotificationPanel() {
        notificationPanel.classList.remove("open");
        notificationOverlay.classList.remove("active");
        panelOpen = false;
        document.body.style.overflow = "";
        // Mark all visible notifications as read
        const newIds = notifications.filter(n => n.status === 'new').map(n => n.id);
        if (newIds.length) {
            fetch('/admin/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: newIds })
            }).then(() => {
                notifications.forEach(n => n.status = 'read');
                renderNotifications();
                updateBadge();
            });
        }
    }

    closePanel.addEventListener("click", closeNotificationPanel);
    notificationOverlay.addEventListener("click", closeNotificationPanel);
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && panelOpen) closeNotificationPanel();
    });

    // Filter (all/unread)
    document.querySelectorAll(".filter-link").forEach(link => {
        link.addEventListener("click", function () {
            document.querySelectorAll(".filter-link").forEach(l => l.classList.remove("active"));
            this.classList.add("active");
            const tab = this.dataset.tab;
            document.querySelectorAll(".notification-item").forEach(item => {
                const n = notifications.find(n => n.id == item.dataset.id);
                item.style.display = (tab === 'all' || n.status === 'new') ? 'block' : 'none';
            });
        });
    });

    // Mark all read button
    markAllRead.addEventListener("click", () => {
        const newIds = notifications.filter(n => n.status === 'new').map(n => n.id);
        if (newIds.length) {
            fetch('/admin/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: newIds })
            }).then(() => {
                notifications.forEach(n => n.status = 'read');
                renderNotifications();
                updateBadge();
            });
        }
    });

    // Initial load
    loadNotifications();
});