document.addEventListener("DOMContentLoaded", () => {
    const notificationBtnDriver = document.getElementById("notificationBtnDriver");
    const notificationPanelDriver = document.getElementById("notificationPanelDriver");
    const notificationOverlayDriver = document.getElementById("notificationOverlayDriver");
    const closePanelDriver = document.getElementById("closePanelDriver");
    const notifBadgeDriver = document.getElementById("notifBadgeDriver");
    const markAllReadDriver = document.getElementById("markAllReadDriver");
    const notificationsListDriver = document.getElementById("notificationsListDriver");
    const filterLinksDriver = document.querySelectorAll("#notificationPanelDriver .filter-link");

    let driverNotifications = [];
    let panelOpenDriver = false;

    // Fetch notifications from server
    function loadDriverNotifications() {
        fetch("/driver/notifications/fetch")
            .then(res => res.json())
            .then(data => {
                driverNotifications = data.notifications;
                updateBadgeDriver(data.unreadCount);
                renderDriverNotifications();
            });
    }

    function formatDriverTimestamp(datetime) {
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
    function renderDriverNotifications() {
        notificationsListDriver.innerHTML = "";

        if (driverNotifications.length === 0) {
            notificationsListDriver.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-title">No notifications</div>
                    <div class="empty-state-message">You're all caught up!</div>
                </div>`;
            return;
        }

        driverNotifications.forEach(n => {
            const item = document.createElement("div");
            item.className = `notification-item ${n.status === "new" ? "unread" : ""}`;
            item.dataset.id = n.id;

            item.innerHTML = `
                <div class="notification-title">${n.title}</div>
                <div class="notification-message">${n.message}</div>
                <div class="notification-datetime">${formatDriverTimestamp(n.created_at)}</div>
            `;

            notificationsListDriver.appendChild(item);

            // Mark individual read
            item.addEventListener("click", () => {
                if (n.status === "new") {
                    n.status = "read";
                    item.classList.remove("unread");
                    updateBadgeDriver();

                    fetch("/driver/notifications/mark-read", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ids: [n.id] })
                    });
                }
            });
        });
    }

    // Badge update
    function updateBadgeDriver(count = null) {
        const unread = count !== null ? count : driverNotifications.filter(n => n.status === "new").length;

        notifBadgeDriver.textContent = unread > 0 ? unread : "";
        notifBadgeDriver.style.display = unread > 0 ? "flex" : "none";
    }

    // Open panel
    notificationBtnDriver.addEventListener("click", () => {
        notificationPanelDriver.classList.add("open");
        notificationOverlayDriver.classList.add("active");
        panelOpenDriver = true;
        document.body.style.overflow = "hidden";
    });

    // Close panel — mark all as read
    function closeNotificationPanelDriver() {
        notificationPanelDriver.classList.remove("open");
        notificationOverlayDriver.classList.remove("active");
        panelOpenDriver = false;
        document.body.style.overflow = "";

        const unreadIds = driverNotifications.filter(n => n.status === "new").map(n => n.id);
        if (unreadIds.length > 0) {
            fetch("/driver/notifications/mark-read", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: unreadIds })
            }).then(() => {
                driverNotifications.forEach(n => (n.status = "read"));
                updateBadgeDriver();
                renderDriverNotifications();
            });
        }
    }

    closePanelDriver.addEventListener("click", closeNotificationPanelDriver);
    notificationOverlayDriver.addEventListener("click", closeNotificationPanelDriver);

    // Mark all read button
    markAllReadDriver.addEventListener("click", () => {
        const unreadIds = driverNotifications.filter(n => n.status === "new").map(n => n.id);

        unreadIds.forEach(id => {
            const n = driverNotifications.find(x => x.id === id);
            n.status = "read";
        });

        fetch("/driver/notifications/mark-read", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ids: unreadIds })
        }).then(() => {
            renderDriverNotifications();
            updateBadgeDriver();
        });
    });

    // Filters
    filterLinksDriver.forEach(link => {
        link.addEventListener("click", function () {
            filterLinksDriver.forEach(l => l.classList.remove("active"));
            this.classList.add("active");

            const tab = this.dataset.tab;

            document.querySelectorAll("#notificationsListDriver .notification-item")
                .forEach(item => {
                    const notif = driverNotifications.find(n => n.id == item.dataset.id);
                    item.style.display =
                        tab === "all" || notif.status === "new" ? "block" : "none";
                });
        });
    });

    // Initial load
    loadDriverNotifications();
});