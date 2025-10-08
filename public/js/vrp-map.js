// VRP Map Visualization Module
(function () {
    "use strict";

    let map;
    let routeLayerGroup;
    let markersLayerGroup;
    let routeCache = {}; // Cache for OSRM routes

    // Helper function to find location by ID (handles both 'tps8' and '8' formats)
    function findLocationById(id, locations) {
        if (id === "depot") return locations.depot; // Legacy support
        if (id === "depotStart") return locations.depotStart;
        if (id === "depotEnd") return locations.depotEnd;

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

    // Show loading overlay
    function showLoadingOverlay() {
        const overlay = document.getElementById("mapLoadingOverlay");
        if (overlay) {
            overlay.style.display = "flex";
        }
    }

    // Hide loading overlay
    function hideLoadingOverlay() {
        const overlay = document.getElementById("mapLoadingOverlay");
        if (overlay) {
            overlay.style.display = "none";
        }
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
        // Depot Start marker (Depot Awal)
        if (locations.depotStart) {
            const depotStartIcon = L.divIcon({
                className: "depot-marker",
                html: '<div style="background-color: #3b82f6; border: 2px solid #fff; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">S</div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10],
            });

            L.marker([locations.depotStart.lat, locations.depotStart.lng], {
                icon: depotStartIcon,
            })
                .bindPopup(
                    `<b>${locations.depotStart.name}</b><br>Depot Awal (Start)`
                )
                .addTo(markersLayerGroup);
        }

        // Depot End marker (TPA/Tempat Pembuangan Akhir)
        if (locations.depotEnd) {
            const depotEndIcon = L.divIcon({
                className: "depot-marker",
                html: '<div style="background-color: #ef4444; border: 2px solid #fff; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">E</div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10],
            });

            L.marker([locations.depotEnd.lat, locations.depotEnd.lng], {
                icon: depotEndIcon,
            })
                .bindPopup(
                    `<b>${locations.depotEnd.name}</b><br>TPA (Tempat Pembuangan Akhir)`
                )
                .addTo(markersLayerGroup);
        }

        // Legacy depot support
        if (locations.depot) {
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
        }

        // TPS markers - show all initially (will be filtered when routes are displayed)
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

    // Get route from OSRM with caching
    async function getRouteFromOSRM(start, end) {
        // Create cache key
        const cacheKey = `${start.lat},${start.lng}-${end.lat},${end.lng}`;

        // Check if route is in cache
        if (routeCache[cacheKey]) {
            return routeCache[cacheKey];
        }

        const url = `https://router.project-osrm.org/route/v1/driving/${start.lng},${start.lat};${end.lng},${end.lat}?overview=full&geometries=geojson`;

        try {
            const res = await fetch(url);
            const data = await res.json();

            if (data.routes && data.routes.length > 0) {
                const route = data.routes[0].geometry.coordinates.map((c) => [
                    c[1],
                    c[0],
                ]);
                // Store in cache
                routeCache[cacheKey] = route;
                return route;
            }
        } catch (error) {
            console.error("Error fetching route from OSRM:", error);
        }

        return [];
    }

    // Display map visualization
    async function displayMapVisualization(routes, locations) {
        if (!routeLayerGroup) return;

        // Show loading overlay
        showLoadingOverlay();

        try {
            routeLayerGroup.clearLayers();
            markersLayerGroup.clearLayers(); // Clear all markers first

            const colors = [
                "#3b82f6",
                "#10b981",
                "#f59e0b",
                "#ef4444",
                "#8b5cf6",
            ];

            // Add depot start marker (blue)
            if (locations.depotStart) {
                const depotStartIcon = L.divIcon({
                    className: "depot-marker",
                    html: '<div style="background-color: #3b82f6; border: 2px solid #fff; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">S</div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10],
                });

                L.marker([locations.depotStart.lat, locations.depotStart.lng], {
                    icon: depotStartIcon,
                })
                    .bindPopup(
                        `<b>${locations.depotStart.name}</b><br>Depot Awal (Start)`
                    )
                    .addTo(markersLayerGroup);
            }

            // Add depot end marker (red - TPA)
            if (locations.depotEnd) {
                const depotEndIcon = L.divIcon({
                    className: "depot-marker",
                    html: '<div style="background-color: #ef4444; border: 2px solid #fff; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">E</div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10],
                });

                L.marker([locations.depotEnd.lat, locations.depotEnd.lng], {
                    icon: depotEndIcon,
                })
                    .bindPopup(
                        `<b>${locations.depotEnd.name}</b><br>TPA (Tempat Pembuangan Akhir)`
                    )
                    .addTo(markersLayerGroup);
            }

            // Legacy depot support
            if (locations.depot) {
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
            }

            // If no routes, show all TPS markers
            if (!routes || routes.length === 0) {
                addInitialMarkers(locations);
                hideLoadingOverlay();
                return;
            }

            // Display routes and only TPS that are visited
            for (let index = 0; index < routes.length; index++) {
                const route = routes[index];
                const color = colors[index % colors.length];
                let routeCoordinates = [];

                // Skip empty routes
                if (!route.tpsVisited || route.tpsVisited.length === 0)
                    continue;

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

                if (routeCoordinates.length > 0) {
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
                }

                // Add numbered markers for this route's TPS only
                route.tpsVisited.forEach((tps, tpsIndex) => {
                    // Add numbered marker
                    const marker = L.circleMarker([tps.lat, tps.lng], {
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.8,
                        radius: 10,
                    }).addTo(routeLayerGroup);

                    marker.bindPopup(
                        `<b>${route.vehicle}</b><br>Stop ${tpsIndex + 1}: ${
                            tps.name || tps.nama
                        }<br>Demand: ${tps.demand} unit`
                    );

                    // Add text label with sequence number
                    const labelIcon = L.divIcon({
                        className: "tps-label",
                        html: `<div style="color: white; font-weight: bold; font-size: 10px; text-align: center; margin-top: -5px;">${
                            tpsIndex + 1
                        }</div>`,
                        iconSize: [20, 20],
                        iconAnchor: [10, 10],
                    });

                    L.marker([tps.lat, tps.lng], {
                        icon: labelIcon,
                        interactive: false,
                    }).addTo(routeLayerGroup);
                });
            }
        } catch (error) {
            console.error("Error displaying map visualization:", error);
        } finally {
            // Hide loading overlay
            hideLoadingOverlay();
        }
    }

    // Clear route layers
    function clearRouteLayer() {
        if (routeLayerGroup) routeLayerGroup.clearLayers();
    }

    // Clear cache
    function clearCache() {
        routeCache = {};
    }

    // Export map functions
    window.VRPMap = {
        initialize: initializeMap,
        displayVisualization: displayMapVisualization,
        clearRoutes: clearRouteLayer,
        clearCache: clearCache,
        findLocation: findLocationById,
    };
})();
