@extends('layouts.app')

@section('title', 'Dashboard Admin - Petas Sistem Manajemen Truk Sampah')
@section('content')





{{--
<!DOCTYPE html>
<html lang="id"> --}}

{{--

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Sistem Manajemen Truk Sampah - Kendari</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script> --}}

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

    <!-- Leaflet Routing Machine -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.js">
    </script>

    <!-- Lucide Icons -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script> --}}
    {{-- <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script> --}}

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .leaflet-control-container .leaflet-routing-container-hide {
            display: none;
        }

        .truck-marker {
            background: #3B82F6;
            width: 30px;
            height: 30px;
            display: block;
            left: -15px;
            top: -15px;
            position: relative;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .tps-marker {
            background: #10B981;
            width: 20px;
            height: 20px;
            display: block;
            left: -10px;
            top: -10px;
            position: relative;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        }

        .completed-tps {
            background: #6B7280 !important;
        }

        .sidebar-toggle {
            transition: transform 0.3s ease;
        }

        .sidebar-toggle.collapsed {
            transform: translateX(-320px);
        }
    </style>
    {{--
</head> --}}

{{--

<body class="h-screen overflow-hidden bg-gray-100"> --}}
    <!-- Header -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <div class="bg-white shadow-lg border-b border-gray-200 z-50 relative">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <button id="sidebarToggle"
                            class="p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                            <i data-lucide="menu" class="h-6 w-6"></i>
                        </button>
                        <div class="flex items-center space-x-2">
                            <i data-lucide="truck" class="h-8 w-8 text-indigo-600"></i>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">Peta TPS Sampah</h1>
                                <p class="text-sm text-gray-500">Kendari</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <i data-lucide="calendar" class="h-4 w-4"></i>
                            <span id="currentDate"></span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <i data-lucide="clock" class="h-4 w-4"></i>
                            <span id="currentTime"></span>
                        </div>
                        {{-- <div class="flex items-center space-x-2">
                            <i data-lucide="user" class="h-5 w-5 text-gray-400"></i>
                            <span class="text-sm font-medium text-gray-700">Admin</span>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="flex h-[calc(100vh-64px)]">


            <!-- Map Container -->
            <div class="flex-1 relative overflow-y-auto">
                <div id="mapContainer" class="w-full h-full"></div>

                <!-- Floating Info Panel -->
                <div id="infoPanel" class="absolute top-4 right-4 bg-white rounded-lg shadow-lg p-4 max-w-sm hidden">
                    <div class="flex justify-between items-start mb-2">
                        <h3 id="infoPanelTitle" class="font-semibold text-gray-900"></h3>
                        <button id="closeInfoPanel" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>
                    <div id="infoPanelContent" class="text-sm text-gray-700">
                        <!-- Content akan diisi dengan JavaScript -->
                    </div>
                </div>

                <!-- Map Loading Indicator -->
                <div id="mapLoading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4">
                        </div>
                        <p class="text-gray-600">Memuat peta...</p>
                    </div>
                </div>
            </div>


            <!-- Sidebar -->
            <div id="sidebar" class="sidebar-toggle w-80 bg-white shadow-lg border-r border-gray-200 overflow-y-auto">
                <div class="p-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Kontrol Peta</h2>

                    <!-- Filter Controls -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
                            <div class="space-y-2">
                                {{-- <label class="flex items-center">
                                    <input type="checkbox" id="showTrucks" checked
                                        class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">Truk Sampah</span>
                                    <span id="truckCount"
                                        class="ml-auto bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">3</span>
                                </label> --}}
                                <label class="flex items-center">
                                    <input type="checkbox" id="showTPS" checked
                                        class="rounded border-gray-300 text-green-600">
                                    <span class="ml-2 text-sm text-gray-700">TPS Aktif</span>
                                    <span id="tpsCount"
                                        class="ml-auto bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">15</span>
                                </label>
                                {{-- <label class="flex items-center">
                                    <input type="checkbox" id="showRoutes"
                                        class="rounded border-gray-300 text-purple-600">
                                    <span class="ml-2 text-sm text-gray-700">Rute Aktif</span>
                                </label> --}}
                                <label class="flex items-center">
                                    <input type="checkbox" id="showDepots" checked
                                        class="rounded border-gray-300 text-purple-600">
                                    <span class="ml-2 text-sm text-gray-700">Tampilkan Depots</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Truck List -->
                    {{-- <div class="space-y-3">
                        <h3 class="text-md font-medium text-gray-900">Daftar Truk</h3>

                        <div id="truckList" class="space-y-2">
                            <!-- Akan diisi dengan JavaScript -->
                        </div>
                    </div> --}}

                    <!-- Map Controls -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-md font-medium text-gray-900 mb-3">Kontrol Peta</h3>
                        <div class="space-y-2">
                            <button id="centerMap"
                                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                <i data-lucide="target" class="h-4 w-4 inline mr-2"></i>
                                Pusat ke Kendari
                            </button>
                            <button id="fitAllMarkers"
                                class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-sm">
                                <i data-lucide="maximize-2" class="h-4 w-4 inline mr-2"></i>
                                Tampilkan Semua
                            </button>
                            {{-- <button id="clearRoutes"
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors text-sm">
                                <i data-lucide="x" class="h-4 w-4 inline mr-2"></i>
                                Hapus Rute
                            </button> --}}
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-md font-medium text-gray-900 mb-3">Legenda</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-gray-700">Tps</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-gray-700">Depots</span>
                            </div>
                            {{-- <div class="flex items-center">
                                <div class="w-3 h-3 bg-gray-500 rounded-full mr-3"></div>
                                <span class="text-gray-700">TPS Sudah Dikunjungi</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-1 bg-purple-500 mr-3"></div>
                                <span class="text-gray-700">Rute Perjalanan</span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let map;
        let routeLayerGroup;
        let markersLayerGroup;
        let trucksLayerGroup;
        let currentRouting = null;
        let tpsData = [];
        let depotsData = [];
        // Initialize data from server
        const serverTpsData = @json($tpsData);
        const serverDepoData = @json($depoData);
        const serverArmadaData = @json($armadaData);

        // Convert server data to map format
        tpsData = serverTpsData.map(item => ({
            id: item.id,
            name: item.nama,
            position: [parseFloat(item.latitude), parseFloat(item.longitude)],
            status: item.status === 'Aktif' ? 'pending' : 'completed',
            alamat: item.alamat
        }));

        depotsData = serverDepoData.map(item => ({
            id: item.id,
            name: item.nama,
            position: [parseFloat(item.latitude), parseFloat(item.longitude)],
            alamat: item.alamat
        }));


        // Initialize map
        function initializeMap() {
            // Hide loading indicator
            document.getElementById('mapLoading').style.display = 'none';

            // Initialize the map centered on Kendari
            map = L.map('mapContainer').setView([-4.0, 122.5], 13);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Create layer groups
            routeLayerGroup = L.layerGroup().addTo(map);
            markersLayerGroup = L.layerGroup().addTo(map);
            trucksLayerGroup = L.layerGroup().addTo(map);

            // Add initial markers - TPS dan Depot
            addInitialMarkers();
        }

        function addInitialMarkers() {
            // Add TPS markers
            tpsData.forEach(tps => {
                addTPSMarker(tps);
            });

            // Add Depot markers
            depotsData.forEach(depot => {
                addDepotMarker(depot);
            });
        }

        function addTruckMarker(truck) {
            const statusColors = {
                'Dalam Perjalanan': '#3B82F6',
                'Siap Berangkat': '#10B981',
                'Maintenance': '#F59E0B'
            };

            const truckIcon = L.divIcon({
                className: 'truck-marker',
                html: `<div style="background: ${statusColors[truck.status]}; width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                       <i data-lucide="truck" style="color: white; width: 16px; height: 16px;"></i>
                       </div>`,
                iconSize: [30, 30]
            });

            const marker = L.marker(truck.position, {
                    icon: truckIcon
                })
                .bindPopup(`
                    <div class="p-2">
                        <h3 class="font-semibold text-gray-900">${truck.name}</h3>
                        <p class="text-sm text-gray-600">Driver: ${truck.driver}</p>
                        <p class="text-sm text-gray-600">Status: <span class="font-medium">${truck.status}</span></p>
                        <p class="text-sm text-gray-600">Kecepatan: ${truck.speed}</p>
                        <p class="text-sm text-gray-600">Bahan Bakar: ${truck.fuel}%</p>
                        <p class="text-xs text-gray-500 mt-1">Update: ${truck.lastUpdate}</p>
                        <button onclick="showTruckRoute('${truck.id}')" class="mt-2 bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                            Tampilkan Rute
                        </button>
                    </div>
                `)
                .addTo(trucksLayerGroup);

            // Add click handler
            marker.on('click', () => {
                showTruckInfo(truck);
            });
        }

        function addTPSMarker(tps) {
            const tpsIcon = L.divIcon({
                className: `tps-marker ${tps.status === 'completed' ? 'completed-tps' : ''}`,
                html: '<div style="width: 100%; height: 100%; border-radius: 50%;"></div>',
                iconSize: [20, 20]
            });

            const marker = L.marker(tps.position, {
                    icon: tpsIcon
                })
                .bindPopup(`
                    <div class="p-2">
                        <h3 class="font-semibold text-gray-900">${tps.name}</h3>
                        <p class="text-sm text-gray-600">ID: ${tps.id}</p>
                        <p class="text-sm text-gray-600">Status:
                            <span class="font-medium ${tps.status === 'completed' ? 'text-green-600' : 'text-orange-600'}">
                                ${tps.status === 'completed' ? 'Sudah Dikunjungi' : 'Belum Dikunjungi'}
                            </span>
                        </p>
                    </div>
                `)
                .addTo(markersLayerGroup);
        }

        function addDepotMarker(depot) {
            const depotIcon = L.divIcon({
                className: 'depot-marker',
                html: '<div style="width: 100%; height: 100%; background: #38bdf8; border-radius: 50%; border: 2px solid white;"></div>',
                iconSize: [20, 20]
            });

            const marker = L.marker(depot.position, {
                    icon: depotIcon
                })
                .bindPopup(`
            <div class="p-2">
                <h3 class="font-semibold text-gray-900">${depot.name}</h3>
                <p class="text-sm text-gray-600">ID: ${depot.id}</p>
                <p class="text-sm text-gray-600">Alamat: ${depot.alamat}</p>
            </div>
        `)
                .addTo(markersLayerGroup);
        }

       function showTruckInfo(truck) {
            const infoPanel = document.getElementById('infoPanel');
            const title = document.getElementById('infoPanelTitle');
            const content = document.getElementById('infoPanelContent');

            title.textContent = truck.name;
            content.innerHTML = `
                <div class="space-y-2">
                    <p><span class="font-medium">Driver:</span> ${truck.driver}</p>
                    <p><span class="font-medium">Status:</span> ${truck.status}</p>
                    <p><span class="font-medium">Kecepatan:</span> ${truck.speed}</p>
                    <p><span class="font-medium">Bahan Bakar:</span> ${truck.fuel}%</p>
                    <p><span class="font-medium">Update Terakhir:</span> ${truck.lastUpdate}</p>
                    <button onclick="showTruckRoute('${truck.id}')" class="mt-3 w-full bg-blue-600 text-white py-2 px-3 rounded text-sm hover:bg-blue-700">
                        Tampilkan Rute
                    </button>
                </div>
            `;

            infoPanel.classList.remove('hidden');
        }

        function showTruckRoute(truckId) {
            const truck = trucksData.find(t => t.id === truckId);
            if (!truck) return;

            // Clear existing routes
            clearRoutes();

            // Get some TPS for demo route
            const routeTPS = tpsData.filter(tps => tps.status === 'pending').slice(0, 3);

            if (routeTPS.length > 0) {
                const waypoints = [
                    L.latLng(truck.position[0], truck.position[1]),
                    ...routeTPS.map(tps => L.latLng(tps.position[0], tps.position[1]))
                ];

                currentRouting = L.Routing.control({
                    waypoints: waypoints,
                    routeWhileDragging: false,
                    createMarker: function() {
                        return null;
                    }, // Don't create default markers
                    lineOptions: {
                        styles: [{
                            color: '#8B5CF6',
                            weight: 4,
                            opacity: 0.7
                        }]
                    }
                }).addTo(routeLayerGroup);

                // Hide the routing instructions panel
                setTimeout(() => {
                    const routingContainer = document.querySelector('.leaflet-routing-container');
                    if (routingContainer) {
                        routingContainer.style.display = 'none';
                    }
                }, 100);
            }
        }

        function clearRoutes() {
            routeLayerGroup.clearLayers();
            if (currentRouting) {
                currentRouting = null;
            }
        }

        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID');
        }

        // Event listeners
        document.getElementById('sidebarToggle').addEventListener('click', () => {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        });

        // document.getElementById('showTrucks').addEventListener('change', (e) => {
        //     if (e.target.checked) {
        //         map.addLayer(trucksLayerGroup);
        //     } else {
        //         map.removeLayer(trucksLayerGroup);
        //     }
        // });

        document.getElementById('showTPS').addEventListener('change', (e) => {
            if (e.target.checked) {
                map.addLayer(markersLayerGroup);
            } else {
                map.removeLayer(markersLayerGroup);
            }
        });
        // document.getElementById('showDepots').addEventListener('change', (e) => {
        //     if (e.target.checked) {
        //         map.addLayer(markersLayerGroup);
        //     } else {
        //         map.removeLayer(markersLayerGroup);
        //     }
        // });

        // document.getElementById('showRoutes').addEventListener('change', (e) => {
        //     if (!e.target.checked) {
        //         clearRoutes();
        //     }
        // });

        document.getElementById('showDepots').addEventListener('change', (e) => {
            if (e.target.checked) {
                // Add depot markers if not already added
                depotsData.forEach(depot => {
                    // Check if depot marker already exists
                    let markerExists = false;
                    map.eachLayer(layer => {
                        if (layer instanceof L.Marker &&
                            layer.options.icon?.options.className === 'depot-marker' &&
                            layer.getLatLng().equals(L.latLng(depot.position))) {
                            markerExists = true;
                        }
                    });
                    if (!markerExists) {
                        addDepotMarker(depot);
                    }
                });
            } else {
                // Remove depot markers
                map.eachLayer(layer => {
                    if (layer instanceof L.Marker && layer.options.icon?.options.className === 'depot-marker') {
                        map.removeLayer(layer);
                    }
                });
            }
        });

        document.getElementById('centerMap').addEventListener('click', () => {
            map.setView([-4.0, 122.5], 13);
        });

        document.getElementById('fitAllMarkers').addEventListener('click', () => {
            const allMarkers = [...depotsData.map(t => t.position), ...tpsData.map(t => t.position)];
            if (allMarkers.length > 0) {
                const group = new L.featureGroup(allMarkers.map(pos => L.marker(pos)));
                map.fitBounds(group.getBounds().pad(0.1));
            }
        });

        // document.getElementById('clearRoutes').addEventListener('click', clearRoutes);

        document.getElementById('closeInfoPanel').addEventListener('click', () => {
            document.getElementById('infoPanel').classList.add('hidden');
        });

        // Initialize everything
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                initializeMap();
                // initializeMarkers(); // Removed - already called in initializeMap()

                lucide.createIcons();
            }, 500);

            updateTime();
            setInterval(updateTime, 1000);
        });

        function updateDateTime() {
        const now = new Date();

        // Format tanggal (contoh: 23 Agustus 2025)
        const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const formattedDate = now.toLocaleDateString('id-ID', optionsDate);

        // Format waktu (contoh: 14:35:07)
        const formattedTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        // Masukkan ke elemen
        document.getElementById("currentDate").textContent = formattedDate;
        document.getElementById("currentTime").textContent = formattedTime;
    }

    // Update pertama kali saat halaman dimuat
    updateDateTime();

    // Update setiap 1 detik
    setInterval(updateDateTime, 1000);
    </script>
    @endsection
    {{--
</body>

</html> --}}
