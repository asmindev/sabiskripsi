@extends('layouts.app')

@section('title', 'Edit Depot - Sistem Manajemen Truk Sampah')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map {
        height: 400px !important;
        width: 100% !important;
    }

    .leaflet-container {
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
                    <div class="flex-shrink-0">
                        <i data-lucide="warehouse" class="h-8 w-8 text-indigo-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Edit Depot</h1>
                        <p class="text-sm text-gray-500">Edit lokasi depot</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.depot.index') }}"
                        class="flex items-center space-x-2 text-gray-600 hover:text-gray-800">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        <span>Kembali ke Data Depot</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex gap-4 flex-col lg:flex-row">

                <form action="{{ route('depot.update', $depot->id) }}" method="POST" class="space-y-6 w-1/3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Depot</label>
                        <input type="text" name="nama" value="{{ $depot->nama }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Contoh: Depot Sampah Utama">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select name="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Type</option>
                            <option value="endpoint" {{ $depot->type === 'endpoint' ? 'selected' : '' }}>Tempat
                                Pembuangan Akhir</option>
                            <option value="startpoint" {{ $depot->type === 'startpoint' ? 'selected' : '' }}>Depot
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Masukkan alamat lengkap depot">{{ $depot->alamat }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kapasitas (ton)</label>
                        <input type="number" name="kapasitas" value="{{ $depot->kapasitas }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Contoh: 200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="aktif" {{ $depot->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="maintenance" {{ $depot->status === 'maintenance' ? 'selected' : ''
                                }}>Maintenance</option>
                            <option value="nonaktif" {{ $depot->status === 'nonaktif' ? 'selected' : '' }}>Non-Aktif
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Koordinat Latitude</label>
                            <input type="number" name="latitude" step="any" value="{{ $depot->latitude }}" required
                                id="latitude"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Contoh: -5.147665">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Koordinat Longitude</label>
                            <input type="number" name="longitude" step="any" value="{{ $depot->longitude }}" required
                                id="longitude"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Contoh: 119.432732">
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('admin.depot.index') }}"
                            class="px-6 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>

                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Lokasi di Peta</label>
                    <div id="map" class="w-full h-full border border-gray-300 rounded-lg"></div>
                    <p class="text-sm text-gray-500 mt-1">Klik pada peta untuk memilih koordinat lokasi depot</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    if (typeof L === 'undefined') {
    document.write('<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin><\/script>');
}
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for everything to load
    setTimeout(function() {
        try {
            if (typeof L === 'undefined') {
                console.error('Leaflet is not loaded');
                return;
            }

            // Initialize map
            // default koordinat kendari
            const DEFAULT_COORDS = [-4.0167, 122.5500]; // Kendari, Sulawesi Tenggara
            var map = L.map('map').setView(DEFAULT_COORDS, 12); // Default to Kendari area

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            var marker;

            // Function to update marker
            function updateMarker(lat, lng) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker([lat, lng]).addTo(map);
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
            }

            // Set initial marker if depot has coordinates
            var initialLat = {{ $depot->latitude ?? 'null' }};
            var initialLng = {{ $depot->longitude ?? 'null' }};
            if (initialLat !== null && initialLng !== null) {
                updateMarker(initialLat, initialLng);
                map.setView([initialLat, initialLng], 15);
            }

            // Click event on map
            map.on('click', function(e) {
                updateMarker(e.latlng.lat, e.latlng.lng);
            });

            // Update marker when inputs change
            document.getElementById('latitude').addEventListener('input', function() {
                var lat = parseFloat(this.value);
                var lng = parseFloat(document.getElementById('longitude').value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    updateMarker(lat, lng);
                    map.setView([lat, lng], 15);
                }
            });

            document.getElementById('longitude').addEventListener('input', function() {
                var lat = parseFloat(document.getElementById('latitude').value);
                var lng = parseFloat(this.value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    updateMarker(lat, lng);
                    map.setView([lat, lng], 15);
                }
            });

            console.log('Map initialized successfully');
        } catch (error) {
            console.error('Error initializing map:', error);
            // Fallback: show message
            document.getElementById('map').innerHTML = '<p class="text-center text-gray-500 p-4">Peta tidak dapat dimuat. Pastikan koneksi internet stabil.</p>';
        }
    }, 1000);
});
</script>
@endpush
