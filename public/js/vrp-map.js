// VRP Map Visualization Module
(function () {
    "use strict";

    let map;
    let routeLayerGroup;
    let markersLayerGroup;

    // Helper function to find location by ID (handles both 'tps8' and '8' formats)
    function findLocationById(id, locations) {
        if (id === "depot") return locations.depot;

        // Try direct match first
        let found = locations.tps.find((t) => t.id == id);
        if (found) return found;

        // If not found and id starts with 'tps', try extracting number
        if (typeof id === "string" && id.startsWith("tps")) {
            const numId = id.replace("tps", "");
            found = locations.tps.find((t) => t.id == numId);
        }

        return found;
    }

    // Initialize map
    function initializeMap(locations) {
        map = L.map("mapContainer").setView([-4.0, 122.5], 13);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "© OpenStreetMap contributors",
        }).addTo(map);

        routeLayerGroup = L.layerGroup().addTo(map);
        markersLayerGroup = L.layerGroup().addTo(map);

        addInitialMarkers(locations);
    }

    // Add initial markers
    function addInitialMarkers(locations) {
        // Depot marker
        const depotIcon = L.divIcon({
            className: "depot-marker",
            html: '<div style="background-color: #ef4444; border: 2px solid #fff; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">D</div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10],
        });

        L.marker([locations.depot.lat, locations.depot.lng], {
            icon: depotIcon,
        })
            .bindPopup(`<b>${locations.depot.name}</b><br>Depot Utama`)
            .addTo(markersLayerGroup);

        // TPS markers
        locations.tps.forEach((tps) => {
            const tpsIcon = L.divIcon({
                className: "tps-marker",
                html: `<div style="background-color: #10b981; border: 2px solid #fff; border-radius: 50%; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 10px;">${tps.demand}</div>`,
                iconSize: [16, 16],
                iconAnchor: [8, 8],
            });

            L.marker([tps.lat, tps.lng], { icon: tpsIcon })
                .bindPopup(`<b>${tps.name}</b><br>Demand: ${tps.demand} unit`)
                .addTo(markersLayerGroup);
        });
    }

    // Get route from OSRM
    async function getRouteFromOSRM(start, end) {
        const url = `https://router.project-osrm.org/route/v1/driving/${start.lng},${start.lat};${end.lng},${end.lat}?overview=full&geometries=geojson`;
        const res = await fetch(url);
        const data = await res.json();
        if (data.routes && data.routes.length > 0) {
            return data.routes[0].geometry.coordinates.map((c) => [c[1], c[0]]);
        }
        return [];
    }

    // Display map visualization
    async function displayMapVisualization(routes, locations) {
        if (!routeLayerGroup) return;

        routeLayerGroup.clearLayers();
        const colors = ["#3b82f6", "#10b981", "#f59e0b", "#ef4444", "#8b5cf6"];

        for (let index = 0; index < routes.length; index++) {
            const route = routes[index];
            const color = colors[index % colors.length];
            let routeCoordinates = [];

            for (let i = 0; i < route.path.length - 1; i++) {
                const startId = route.path[i];
                const endId = route.path[i + 1];

                const start = findLocationById(startId, locations);
                const end = findLocationById(endId, locations);

                if (!start || !end) {
                    console.warn(
                        `Location not found: start=${startId}, end=${endId}`
                    );
                    continue;
                }

                const segment = await getRouteFromOSRM(start, end);
                routeCoordinates = routeCoordinates.concat(segment);
            }

            const routeLine = L.polyline(routeCoordinates, {
                color: color,
                weight: 3,
                opacity: 0.8,
            }).addTo(routeLayerGroup);

            routeLine.bindPopup(`
            <b>${route.vehicle}</b><br>
            TPS: ${route.tpsVisited.length}<br>
            Jarak: ${route.totalDistance.toFixed(1)} km<br>
            Waktu: ${Math.round(route.totalTime)} menit
        `);

            // Add numbered markers
            route.tpsVisited.forEach((tps, tpsIndex) => {
                const marker = L.circleMarker([tps.lat, tps.lng], {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.8,
                    radius: 8,
                }).addTo(routeLayerGroup);
                marker.bindPopup(
                    `<b>${route.vehicle}</b><br>Stop ${tpsIndex + 1}: ${
                        tps.name
                    }`
                );
            });
        }
    }

    // Clear route layers
    function clearRouteLayer() {
        if (routeLayerGroup) routeLayerGroup.clearLayers();
    }

    // Export map functions
    window.VRPMap = {
        initialize: initializeMap,
        displayVisualization: displayMapVisualization,
        clearRoutes: clearRouteLayer,
        findLocation: findLocationById,
    };
})();
