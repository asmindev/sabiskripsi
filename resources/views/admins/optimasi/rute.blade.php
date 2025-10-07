@extends('layouts.app')

@section('title', 'Dashboard Admin - Optimasi Rute Sistem Manajemen Truk Sampah')
@section('content')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .map-container {
        width: 100%;
        height: 400px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .loading-spinner {
        border: 4px solid #f3f4f6;
        border-top: 4px solid #3b82f6;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Custom marker styles */
    .depot-marker {
        background-color: #ef4444;
        border: 2px solid #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .tps-marker {
        background-color: #10b981;
        border: 2px solid #fff;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .route-colors {
        color: #3b82f6;
    }

    /* Popup styling */
    .leaflet-popup-content {
        font-family: 'Inter', sans-serif;
    }

    #notificationSidebar {
        right: -320px;
    }

    #notificationSidebar.show {
        right: 1rem;
    }
</style>
@endpush
{{-- </head> --}}

<!-- Header -->
<div class="flex w-full flex-col overflow-hidden">

    <!-- Header -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <i data-lucide="route" class="h-8 w-8 text-indigo-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Optimasi Rute</h1>
                        <p class="text-sm text-gray-500">Algoritma Dijkstra & Vehicle Routing Problem</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Icon bell -->
                    <div id="notificationBell"></div>
                    <span id="notificationBadgeCount"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- 📌 Sidebar Notifikasi -->
    <div id="notificationSidebar"
        class="fixed top-16 right-[-320px] w-72 max-h-[70vh] z-50 flex flex-col bg-white rounded-lg shadow-lg overflow-y-auto transition-all duration-300">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Notifikasi</h2>
            <button id="closeSidebar" class="text-gray-500 hover:text-gray-700 cursor-pointer">✖</button>
        </div>
        <div id="notificationContainerInner" class="p-4 space-y-2"></div>
    </div>

    <!-- Main Content -->
    <div class="max-w-full w-full mx-auto overflow-x-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Control Panel -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Parameter Optimasi</h2>

            <!-- Hidden Defaults to Keep Algorithm Working -->
            <input type="hidden" id="truckCount" value="3">
            <input type="hidden" id="truckCapacity" value="15">
            <input type="hidden" id="startTime" value="06:00">

            <!-- Only Algorithm Shown -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Algoritma</label>
                <input type="hidden" id="algorithm" name="algorithm" value="vrp">
                <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                    VRP + Dijkstra
                </div>
            </div>

            <div class="flex space-x-4">
                <button onclick="runOptimization()"
                    class="flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    <i data-lucide="play" class="h-4 w-4 mr-2"></i>
                    Jalankan Optimasi
                </button>
                <button onclick="resetOptimization()"
                    class="flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <i data-lucide="refresh-cw" class="h-4 w-4 mr-2"></i>
                    Reset
                </button>
                <button onclick="exportResults()"
                    class="flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                    Export Hasil
                </button>
            </div>
        </div>

        <!-- Results Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Map Visualization -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Visualisasi Peta Rute</h3>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-xs text-gray-600">Depot</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-xs text-gray-600">TPS</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-xs text-gray-600">Rute</span>
                        </div>
                    </div>
                </div>

                <div id="mapContainer" class="map-container"></div>

                <!-- Algorithm Status -->
                <div id="algorithmStatus" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="loading-spinner hidden" id="loadingSpinner"></div>
                        <span class="text-sm font-medium text-gray-700">Status Algoritma:</span>
                        <span id="statusText" class="text-sm text-gray-600">Menunggu...</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="progressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                            style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Route Table -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hasil Perhitungan Rute</h3>

                <div id="routeResults" class="space-y-4">
                    <div class="text-center py-8">
                        <i data-lucide="table" class="h-16 w-16 text-gray-400 mx-auto mb-2"></i>
                        <p class="text-gray-500">Hasil perhitungan akan ditampilkan di sini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white rounded-xl shadow-lg p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Optimasi</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4" id="statisticsGrid">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <i data-lucide="route" class="h-5 w-5 text-blue-600"></i>
                        <span class="text-sm font-medium text-gray-700">Total Jarak</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-600" id="totalDistance">-</p>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <i data-lucide="clock" class="h-5 w-5 text-green-600"></i>
                        <span class="text-sm font-medium text-gray-700">Total Waktu</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600" id="totalTime">-</p>
                </div>

                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <i data-lucide="fuel" class="h-5 w-5 text-yellow-600"></i>
                        <span class="text-sm font-medium text-gray-700">Efisiensi</span>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600" id="efficiency">-</p>
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <i data-lucide="zap" class="h-5 w-5 text-purple-600"></i>
                        <span class="text-sm font-medium text-gray-700">Waktu Komputasi</span>
                    </div>
                    <p class="text-2xl font-bold text-purple-600" id="computationTime">-</p>
                </div>
            </div>
        </div>
    </div>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- VRP Modules -->
<script src="{{ asset('js/vrp-map.js') }}"></script>
<script src="{{ asset('js/notification-system.js') }}"></script>
<script src="{{ asset('js/vrp-algorithm.js') }}"></script>
<script src="{{ asset('js/route-table-ui.js') }}"></script>
<script src="{{ asset('js/vrp-app.js') }}"></script>

<script>
    // Set locations data from server
    const locations = {
        depot: {
            name: 'Depot Utama',
            lat: {{ $depoData->first()?->latitude ?? -3.9778 }},
            lng: {{ $depoData->first()?->longitude ?? 122.5150 }}
        },
        tps: [
            @foreach($tpsData as $tps)
            {
                id: '{{ $tps->id }}',
                name: '{{ $tps->nama }}',
                lat: {{ $tps->latitude }},
                lng: {{ $tps->longitude }},
                demand: {{ $tps->kapasitas ?? 5 }}
            }@if(!$loop->last),@endif
            @endforeach
        ]
    };

    // Set truck values from database
    document.getElementById('truckCount').value = {{ $armadaData->count() }};
    document.getElementById('truckCapacity').value = {{ $armadaData->first()?->kapasitas ?? 15 }};

    // Initialize VRP app with locations
    if (window.VRPApp) {
        window.VRPApp.setLocations(locations);
    }
</script>

@endpush
@endsection
{{-- </body> --}}

{{--

</html> --}}
