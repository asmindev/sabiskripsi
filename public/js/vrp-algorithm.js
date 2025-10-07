// VRP Algorithm & Route Management Module
(function () {
    "use strict";

    let routesData = [];
    let locations = {};
    let csrfToken;

    // Local Storage helpers
    function saveState() {
        localStorage.setItem("vrpState", JSON.stringify(routesData));
    }

    function loadState() {
        const data = localStorage.getItem("vrpState");
        return data ? JSON.parse(data) : null;
    }

    function clearState() {
        localStorage.removeItem("vrpState");
    }

    // Initialize VRP
    function initializeVRP(locationData, token) {
        locations = locationData;
        csrfToken = token;

        // Load saved state
        const savedData = loadState();
        if (savedData) {
            routesData = savedData;
            return savedData;
        }
        return null;
    }

    // Run optimization
    async function runOptimization() {
        showAlgorithmStatus();

        const response = await fetch("/api/run-vrp", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({}),
        });

        const data = await response.json();
        routesData = data.routes.map((route) => {
            if (!route.tpsStatus) {
                route.tpsStatus = {};
                route.path.forEach((p) => {
                    if (p !== "depot") route.tpsStatus[p] = "belum";
                });
            }
            return route;
        });

        saveState();
        hideAlgorithmStatus();

        return routesData;
    }

    // Compute statistics
    function computeStatistics(routes) {
        let totalDistance = 0;
        let totalTime = 0;
        routes.forEach((r) => {
            totalDistance += r.totalDistance || 0;
            totalTime += r.totalTime || 0;
        });
        return {
            totalDistance,
            totalTime,
            efficiency: 90,
            computationTime: 2.8,
        };
    }

    // Display statistics
    function displayStatistics(stats) {
        document.getElementById("totalDistance").textContent =
            stats.totalDistance.toFixed(1) + " km";
        document.getElementById("totalTime").textContent =
            Math.round(stats.totalTime) + " menit";
        document.getElementById("efficiency").textContent =
            stats.efficiency + "%";
        document.getElementById("computationTime").textContent =
            stats.computationTime + " detik";
    }

    // Show algorithm status
    function showAlgorithmStatus() {
        const statusDiv = document.getElementById("algorithmStatus");
        const spinner = document.getElementById("loadingSpinner");
        const statusText = document.getElementById("statusText");
        const progressBar = document.getElementById("progressBar");

        statusDiv.classList.remove("hidden");
        spinner.classList.remove("hidden");
        statusText.textContent = "Menjalankan algoritma...";

        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            progressBar.style.width = progress + "%";
            if (progress >= 100) {
                clearInterval(interval);
                statusText.textContent = "Selesai!";
                spinner.classList.add("hidden");
            }
        }, 300);
    }

    // Hide algorithm status
    function hideAlgorithmStatus() {
        setTimeout(() => {
            document.getElementById("algorithmStatus").classList.add("hidden");
        }, 1000);
    }

    // Reset optimization
    function resetOptimization() {
        if (window.VRPMap) {
            window.VRPMap.clearRoutes();
        }

        routesData = [];
        clearState();

        const routeResults = document.getElementById("routeResults");
        routeResults.innerHTML = `
        <div class="text-center py-8">
            <i data-lucide="table" class="h-16 w-16 text-gray-400 mx-auto mb-2"></i>
            <p class="text-gray-500">Hasil perhitungan akan ditampilkan di sini</p>
        </div>
    `;

        document.getElementById("totalDistance").textContent = "-";
        document.getElementById("totalTime").textContent = "-";
        document.getElementById("efficiency").textContent = "-";
        document.getElementById("computationTime").textContent = "-";

        if (window.NotificationSystem) {
            window.NotificationSystem.reset();
        }

        lucide.createIcons();
    }

    // Export results
    function exportResults() {
        const data = {
            timestamp: new Date().toISOString(),
            algorithm: document.getElementById("algorithm").value,
            parameters: {
                truckCount: document.getElementById("truckCount").value,
                capacity: document.getElementById("truckCapacity").value,
                startTime: document.getElementById("startTime").value,
            },
            routes: routesData,
            locations,
        };

        const blob = new Blob([JSON.stringify(data, null, 2)], {
            type: "application/json",
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `optimasi-rute-${
            new Date().toISOString().split("T")[0]
        }.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    // Get current routes data
    function getRoutesData() {
        return routesData;
    }

    // Update route status
    function updateRouteStatus(routeIndex, tpsId, status) {
        if (routesData[routeIndex]) {
            const normalizedStatus = status.includes("sudah")
                ? "sudah"
                : "belum";
            routesData[routeIndex].tpsStatus[tpsId] = normalizedStatus;
            saveState();
            return true;
        }
        return false;
    }

    // Export VRP functions
    window.VRPAlgorithm = {
        init: initializeVRP,
        run: runOptimization,
        reset: resetOptimization,
        export: exportResults,
        getRoutes: getRoutesData,
        updateStatus: updateRouteStatus,
        computeStats: computeStatistics,
        displayStats: displayStatistics,
    };
})();
