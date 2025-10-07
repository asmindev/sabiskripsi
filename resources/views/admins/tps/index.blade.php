@extends('layouts.app')

@section('title', 'Data TPS - Sistem Manajemen Truk Sampah')

@section('content')

<!-- Success Alert -->
@if(session('success'))
<div id="successAlert"
    class="fixed top-4 right-4 z-[60] bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
    <i data-lucide="check-circle" class="h-6 w-6"></i>
    <span>{{ session('success') }}</span>
    <button onclick="closeAlert()" class="ml-4 text-white hover:text-gray-200">
        <i data-lucide="x" class="h-5 w-5"></i>
    </button>
</div>
@endif

<div class="flex-1 flex flex-col overflow-hidden">
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <i data-lucide="map-pin" class="h-8 w-8 text-indigo-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Data TPS</h1>
                        <p class="text-sm text-gray-500">Kelola Tempat Pembuangan Sementara</p>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto overflow-x-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total TPS</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ $tpsData->count() }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 rounded-full">
                        <i data-lucide="map-pin" class="h-6 w-6 text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">TPS Ter-assign</p>
                        <p class="text-3xl font-bold text-green-600">{{ $tpsData->whereNotNull('armada_id')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">TPS Belum Ter-assign</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $tpsData->whereNull('armada_id')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i data-lucide="alert-circle" class="h-6 w-6 text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tps.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                        Tambah TPS
                    </a>
                    <button onclick="exportData()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                        Export Data
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <i data-lucide="search"
                            class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                        <input type="text" id="searchInput" placeholder="Cari TPS..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <select id="statusFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="assigned">Ter-assign</option>
                        <option value="unassigned">Belum Ter-assign</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- TPS Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Daftar TPS</h2>
            </div>
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    No</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Nama TPS</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Alamat</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Kapasitas</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Koordinat</th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tpsData as $index => $tps)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i data-lucide="map-pin" class="h-5 w-5 text-indigo-600 mr-2 flex-shrink-0"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $tps->nama }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate" title="{{ $tps->alamat }}">{{ $tps->alamat }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tps->kapasitas }} m³
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center space-x-1">
                                        <span>{{ number_format($tps->latitude, 4) }},</span>
                                        <span>{{ number_format($tps->longitude, 4) }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('tps.edit', $tps->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors"
                                            title="Edit TPS">
                                            <i data-lucide="pencil" class="h-4 w-4 mr-1"></i>
                                            <span class="text-xs font-medium">Edit</span>
                                        </a>
                                        <button onclick="deleteTPS({{ $tps->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                            title="Hapus TPS">
                                            <i data-lucide="trash" class="h-4 w-4 mr-1"></i>
                                            <span class="text-xs font-medium">Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="map-pin" class="h-12 w-12 text-gray-300 mb-2"></i>
                                        <p>Tidak ada data TPS</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-xl max-w-sm w-full p-6">
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="text-center">
                <i data-lucide="alert-triangle" class="h-16 w-16 text-red-500 mx-auto mb-4"></i>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus TPS</h3>
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus TPS ini? Tindakan ini tidak dapat
                    dibatalkan.</p>
                <div class="flex space-x-3">
                    <button type="submit"
                        class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        updateTime();
        setInterval(updateTime, 1000);
        lucide.createIcons();

        // Auto close success alert after 3 seconds
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => closeAlert(), 3000);
        }
    });

    function closeAlert() {
        const alert = document.getElementById('successAlert');
        if (alert) {
            alert.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => alert.remove(), 300);
        }
    }

    function updateTime() {
        const now = new Date();
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };

        document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', dateOptions);
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID', timeOptions);
    }

    function deleteTPS(id) {
        document.getElementById('deleteForm').action = `/tps/${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function exportData() {
        window.location.href = '/tps/export';
    }
</script>
@endpush
@endsection
