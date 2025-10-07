// Main VRP Application
(function () {
    "use strict";

    let locations = {};
    let csrfToken = "";

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
                // Display saved routes
                if (window.RouteTableUI) {
                    window.RouteTableUI.display(savedRoutes, locations);
                }

                const stats = window.VRPAlgorithm.computeStats(savedRoutes);
                window.VRPAlgorithm.displayStats(stats);

                await window.VRPMap.displayVisualization(
                    savedRoutes,
                    locations
                );
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

    // Run optimization
    async function runOptimization() {
        if (!window.VRPAlgorithm) return;

        const routes = await window.VRPAlgorithm.run();

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
    };

    // Make functions globally available for onclick handlers
    window.runOptimization = runOptimization;
    window.resetOptimization = resetOptimization;
    window.exportResults = exportResults;
})();
