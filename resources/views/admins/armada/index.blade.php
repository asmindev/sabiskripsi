@extends('layouts.app')

@section('title', 'Data Armada - Sistem Manajemen Truk Sampah')

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
                        <i data-lucide="truck" class="h-8 w-8 text-indigo-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Data Armada</h1>
                        <p class="text-sm text-gray-500">Kelola Armada Truk Sampah</p>
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Armada</p>
                        <p class="text-3xl font-bold text-indigo-600" id="totalArmada">{{ $armadaData->count() }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 rounded-full">
                        <i data-lucide="truck" class="h-6 w-6 text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Truk Aktif</p>
                        <p class="text-3xl font-bold text-green-600" id="trukAktif">{{ $armadaData->where('status',
                            'Aktif')->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Maintenance</p>
                        <p class="text-3xl font-bold text-yellow-600" id="trukMaintenance">{{
                            $armadaData->where('status', 'Maintenance')->count() }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i data-lucide="wrench" class="h-6 w-6 text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Tidak Aktif</p>
                        <p class="text-3xl font-bold text-red-600" id="trukTidakAktif">{{ $armadaData->where('status',
                            'Tidak Aktif')->count() }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <i data-lucide="x-circle" class="h-6 w-6 text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('armadas.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                        Tambah Armada
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
                        <input type="text" id="searchInput" placeholder="Cari armada..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <select id="statusFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Armada Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Armada</h2>
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
                                    Nama Truk</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Nomor Plat</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Kapasitas</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Driver</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    TPS Assigned</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Terakhir Maintenance</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($armadaData as $index => $armada)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i data-lucide="truck" class="h-5 w-5 text-indigo-600 mr-2 flex-shrink-0"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $armada->namaTruk }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $armada->nomorPlat }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $armada->kapasitas }}
                                    ton
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($armada->status === 'Aktif')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                    @elseif($armada->status === 'Maintenance')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Maintenance</span>
                                    @elseif($armada->status === 'Tidak Aktif')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Tidak
                                        Aktif</span>
                                    @else
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{
                                        $armada->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $armada->driver ?: '-'
                                    }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $armada->tps->count() }} TPS
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $armada->lastMaintenance ?
                                    \Carbon\Carbon::parse($armada->lastMaintenance)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('armadas.edit', $armada->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors"
                                            title="Edit Armada">
                                            <i data-lucide="pencil" class="h-4 w-4 mr-1"></i>
                                            <span class="text-xs font-medium">Edit</span>
                                        </a>
                                        <button onclick="deleteArmada({{ $armada->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                            title="Hapus Armada">
                                            <i data-lucide="trash" class="h-4 w-4 mr-1"></i>
                                            <span class="text-xs font-medium">Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="truck" class="h-12 w-12 text-gray-300 mb-2"></i>
                                        <p>Tidak ada data armada</p>
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
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Armada</h3>
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus armada ini? TPS yang ter-assign akan
                    dilepaskan.</p>
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

    function deleteArmada(id) {
        document.getElementById('deleteForm').action = `/armadas/${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function exportData() {
        window.location.href = '/armadas/export';
    }
</script>
@endpush
@endsection
