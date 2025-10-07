// Notification System Module
(function () {
    "use strict";

    let csrfToken;

    // Initialize notification system
    function initializeNotifications(token) {
        csrfToken = token;
        loadNotifications();
        setupEventListeners();
    }

    // Render notification
    function renderNotification(message) {
        const container = document.getElementById("notificationContainerInner");
        const notif = document.createElement("div");
        notif.className =
            "flex items-center justify-between p-3 bg-gray-100 rounded-lg shadow border border-gray-200";

        const text = document.createElement("span");
        text.textContent = message;

        const close = document.createElement("button");
        close.innerHTML = "✖";
        close.className =
            "ml-3 text-gray-400 hover:text-gray-600 cursor-pointer text-sm font-bold";
        close.onclick = () => {
            notif.remove();
            updateBadge(-1);
            saveNotifications();
        };

        notif.appendChild(text);
        notif.appendChild(close);
        container.prepend(notif);
    }

    // Update badge count
    function updateBadge(change = 0) {
        const badge = document.getElementById("notificationBadgeCount");
        let count = parseInt(badge.textContent || "0") + change;
        if (count < 0) count = 0;

        if (count === 0) {
            badge.textContent = "";
            badge.style.display = "none";
        } else {
            badge.textContent = count;
            badge.style.display = "inline-block";
        }
    }

    // Save notifications to localStorage
    function saveNotifications() {
        const container = document.getElementById("notificationContainerInner");
        const messages = Array.from(container.querySelectorAll("span")).map(
            (el) => el.textContent
        );
        localStorage.setItem("notifications", JSON.stringify(messages));
    }

    // Load notifications from localStorage
    function loadNotifications() {
        const saved = JSON.parse(localStorage.getItem("notifications") || "[]");
        saved.forEach((msg) => renderNotification(msg));
        updateBadge(saved.length);
    }

    // Reset all notifications
    function resetNotifications() {
        localStorage.removeItem("notifications");
        document.getElementById("notificationContainerInner").innerHTML = "";
        const badge = document.getElementById("notificationBadgeCount");
        badge.textContent = "";
        badge.style.display = "none";
    }

    // Send notification to server
    async function sendNotificationToServer(message) {
        try {
            await fetch("/notifications", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ message }),
            });
        } catch (e) {
            console.error("Gagal kirim notifikasi:", e);
        }
    }

    // Add notification (public API)
    function addNotification(message) {
        renderNotification(message);
        updateBadge(1);
        saveNotifications();
        sendNotificationToServer(message);
    }

    // Setup event listeners
    function setupEventListeners() {
        document
            .getElementById("notificationBell")
            ?.addEventListener("click", () => {
                document
                    .getElementById("notificationSidebar")
                    .classList.toggle("show");
            });

        document
            .getElementById("closeSidebar")
            ?.addEventListener("click", () => {
                document
                    .getElementById("notificationSidebar")
                    .classList.remove("show");
            });
    }

    // Export notification functions
    window.NotificationSystem = {
        init: initializeNotifications,
        add: addNotification,
        reset: resetNotifications,
    };
})();
