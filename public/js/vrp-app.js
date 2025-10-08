// Main VRP Application
(function () {
    "use strict";

    let locations = {};
    let csrfToken = "";
    let allRoutes = []; // Store all computed routes

    // Initialize application
    async function init() {
        // Get CSRF token
        csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        // Set truck values from database
        const truckCount = parseInt(
            document.getElementById("truckCount").value
        );
        const truckCapacity = parseInt(
            document.getElementById("truckCapacity").value
        );

        // Initialize notification system
        if (window.NotificationSystem) {
            window.NotificationSystem.init(csrfToken);
        }

        // Initialize map
        if (window.VRPMap && !isMapInitialized()) {
            window.VRPMap.initialize(locations);
        }

        // Initialize VRP algorithm
        if (window.VRPAlgorithm) {
            const savedRoutes = window.VRPAlgorithm.init(locations, csrfToken);

            if (savedRoutes) {
                allRoutes = savedRoutes;

                // Populate vehicle selector
                populateVehicleSelector(savedRoutes);

                // Don't display routes automatically - wait for user selection
                // Display saved routes
                // if (window.RouteTableUI) {
                //     window.RouteTableUI.display(savedRoutes, locations);
                // }

                const stats = window.VRPAlgorithm.computeStats(savedRoutes);
                window.VRPAlgorithm.displayStats(stats);

                // await window.VRPMap.displayVisualization(
                //     savedRoutes,
                //     locations
                // );
            }
        }

        // Initialize Lucide icons
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    // Check if map is already initialized
    function isMapInitialized() {
        return (
            document.querySelector("#mapContainer .leaflet-container") !== null
        );
    }

    // Populate vehicle selector dropdown
    function populateVehicleSelector(routes) {
        const selector = document.getElementById("vehicleSelector");
        const container = document.getElementById("vehicleSelectorContainer");

        if (!selector || !routes || routes.length === 0) return;

        // Clear existing options except "All"
        selector.innerHTML =
            '<option value="all">🚛 Tampilkan Semua Armada</option>';

        // Add option for each vehicle
        routes.forEach((route, index) => {
            const option = document.createElement("option");
            option.value = index;
            const tpsCount = route.tpsVisited ? route.tpsVisited.length : 0;
            const distance = route.totalDistance
                ? route.totalDistance.toFixed(1)
                : "0";
            option.textContent = `${route.vehicle} - ${tpsCount} TPS (${distance} km)`;
            selector.appendChild(option);
        });

        // Show the selector
        container.classList.remove("hidden");

        // Re-initialize lucide icons
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    // Filter routes by selected vehicle
    async function filterByVehicle(selectedValue) {
        if (!allRoutes || allRoutes.length === 0) return;

        let filteredRoutes = [];

        if (selectedValue === "all") {
            filteredRoutes = allRoutes;
        } else {
            const index = parseInt(selectedValue);
            filteredRoutes = [allRoutes[index]];
        }

        // Show visual feedback that filtering is happening
        const selector = document.getElementById("vehicleSelector");
        if (selector) {
            selector.disabled = true;
        }

        try {
            // Display filtered routes on map (with loading indicator)
            if (window.VRPMap) {
                await window.VRPMap.displayVisualization(
                    filteredRoutes,
                    locations
                );
            }

            // Display filtered routes in table
            if (window.RouteTableUI) {
                window.RouteTableUI.display(filteredRoutes, locations);
            }

            // Update statistics
            const stats = window.VRPAlgorithm.computeStats(filteredRoutes);
            window.VRPAlgorithm.displayStats(stats);
        } finally {
            // Re-enable selector
            if (selector) {
                selector.disabled = false;
            }
        }
    }

    // Run optimization
    async function runOptimization() {
        if (!window.VRPAlgorithm) return;

        const routes = await window.VRPAlgorithm.run();
        allRoutes = routes;

        // Populate vehicle selector with new routes
        populateVehicleSelector(routes);

        // Don't auto-display - wait for user to select vehicle
        // Reset selector to default
        const selector = document.getElementById("vehicleSelector");
        if (selector) {
            selector.value = "all";
        }

        // Display all routes by default after calculation
        if (window.VRPMap) {
            await window.VRPMap.displayVisualization(routes, locations);
        }

        if (window.RouteTableUI) {
            window.RouteTableUI.display(routes, locations);
        }

        const stats = window.VRPAlgorithm.computeStats(routes);
        window.VRPAlgorithm.displayStats(stats);
    }

    // Reset optimization
    function resetOptimization() {
        if (window.VRPAlgorithm) {
            window.VRPAlgorithm.reset();
        }

        allRoutes = [];

        // Clear route cache
        if (window.VRPMap) {
            window.VRPMap.clearCache();
        }

        // Hide vehicle selector
        const container = document.getElementById("vehicleSelectorContainer");
        if (container) {
            container.classList.add("hidden");
        }
    }

    // Export results
    function exportResults() {
        if (window.VRPAlgorithm) {
            window.VRPAlgorithm.export();
        }
    }

    // Set locations data
    function setLocations(data) {
        locations = data;
    }

    // Initialize on page load
    window.addEventListener("load", init);

    // Expose public API
    window.VRPApp = {
        init,
        setLocations,
        runOptimization,
        resetOptimization,
        exportResults,
        filterByVehicle,
    };

    // Make functions globally available for onclick handlers
    window.runOptimization = runOptimization;
    window.resetOptimization = resetOptimization;
    window.exportResults = exportResults;
})();
