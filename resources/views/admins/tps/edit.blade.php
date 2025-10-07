@extends('layouts.app')

@section('title', 'Edit TPS - Sistem Manajemen Truk Sampah')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map {
        height: 100%;
        width: 100%;
        border-radius: 0.5rem;
        z-index: 1;
    }
</style>
@endpush

@section('content')

<div class="flex-1 flex flex-col overflow-hidden">
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.tps') }}" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="arrow-left" class="h-6 w-6"></i>
                    </a>
                    <div class="flex-shrink-0">
                        <i data-lucide="map-pin" class="h-8 w-8 text-indigo-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Edit TPS: {{ $tps->nama }}</h1>
                        <p class="text-sm text-gray-500">Perbarui data dan lokasi TPS</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('tps.update', $tps->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Form Fields Column -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data TPS</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama TPS <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="nama" value="{{ old('nama', $tps->nama) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nama') border-red-500 @enderror"
                                    placeholder="Contoh: TPS Kampung Salo" required>
                                @error('nama')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span
                                        class="text-red-500">*</span></label>
                                <textarea name="alamat" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('alamat') border-red-500 @enderror"
                                    placeholder="Masukkan alamat lengkap TPS"
                                    required>{{ old('alamat', $tps->alamat) }}</textarea>
                                @error('alamat')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas (m³) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="kapasitas" value="{{ old('kapasitas', $tps->kapasitas) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('kapasitas') border-red-500 @enderror"
                                    placeholder="Contoh: 50" step="0.1" min="0.1" required>
                                @error('kapasitas')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Koordinat Lokasi</h3>
                        <p class="text-sm text-gray-500 mb-4">Klik pada peta untuk mengubah lokasi TPS</p>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Latitude <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="latitude" id="latitude"
                                    value="{{ old('latitude', $tps->latitude) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('latitude') border-red-500 @enderror"
                                    placeholder="-3.9778" required readonly>
                                @error('latitude')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Longitude <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="longitude" id="longitude"
                                    value="{{ old('longitude', $tps->longitude) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('longitude') border-red-500 @enderror"
                                    placeholder="122.5150" required readonly>
                                @error('longitude')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <i data-lucide="info" class="h-5 w-5 text-blue-600 mt-0.5 mr-2"></i>
                                <p class="text-sm text-blue-800">Klik pada peta di sebelah kanan untuk mengubah
                                    koordinat TPS</p>
                            </div>
                        </div>
                    </div>

                    @if($tps->armada)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-3">
                            <i data-lucide="truck" class="h-5 w-5 inline mr-2"></i>
                            Armada Ter-assign
                        </h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-800">TPS ini saat ini ter-assign ke:</p>
                                <p class="text-lg font-bold text-green-900 mt-1">{{ $tps->armada->namaTruk }}</p>
                                <p class="text-sm text-green-700">{{ $tps->armada->nomorPlat }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-orange-900 mb-2">
                            <i data-lucide="alert-circle" class="h-5 w-5 inline mr-2"></i>
                            Status Assignment
                        </h3>
                        <p class="text-sm text-orange-800">TPS ini belum ter-assign ke armada manapun</p>
                    </div>
                    @endif

                    <div class="flex space-x-3">
                        <button type="submit"
                            class="flex-1 bg-indigo-600 text-white py-2.5 px-4 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                            <i data-lucide="save" class="h-4 w-4 inline mr-2"></i>
                            Update TPS
                        </button>
                        <a href="{{ route('admin.tps') }}"
                            class="flex-1 bg-gray-300 text-gray-700 py-2.5 px-4 rounded-lg hover:bg-gray-400 transition-colors text-center font-semibold">
                            <i data-lucide="x" class="h-4 w-4 inline mr-2"></i>
                            Batal
                        </a>
                    </div>
                </div>

                <!-- Map Column -->
                <div class="lg:sticky lg:top-8">
                    <div class="bg-white rounded-xl shadow-lg p-6 h-[calc(100vh-12rem)]">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Lokasi TPS di Peta</h3>
                        <div id="map" class="h-[calc(100%-3rem)]"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    let map, marker;

    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        initializeMap();
    });

    function initializeMap() {
        const currentLat = parseFloat(document.getElementById('latitude').value) || -3.9778;
        const currentLng = parseFloat(document.getElementById('longitude').value) || 122.5150;

        // Initialize map centered on current TPS location
        map = L.map('map').setView([currentLat, currentLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Place marker at current location
        const latlng = L.latLng(currentLat, currentLng);
        updateMarker(latlng);

        // Add click event to map
        map.on('click', function(e) {
            updateMarker(e.latlng);
        });
    }

    function updateMarker(latlng) {
        // Remove existing marker
        if (marker) {
            map.removeLayer(marker);
        }

        // Add new marker
        marker = L.marker(latlng).addTo(map);

        // Update input fields
        document.getElementById('latitude').value = latlng.lat.toFixed(6);
        document.getElementById('longitude').value = latlng.lng.toFixed(6);
    }
</script>
@endpush
@endsection
