@extends('layouts.app')

@section('title', 'Data Depo Sampah - Sistem Manajemen Truk Sampah')

@section('content')




{{--
<!DOCTYPE html>
<html lang="id"> --}}

{{--

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Depo - Sistem Manajemen Truk Sampah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head> --}}
{{--

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50"> --}}
    <!-- Header -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <div class="bg-white shadow-lg border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <i data-lucide="warehouse" class="h-8 w-8 text-indigo-600"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Data Depo</h1>
                            <p class="text-sm text-gray-500">Manajemen Lokasi Depo Sampah</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="location.href='index.html'"
                            class="flex items-center space-x-2 text-gray-600 hover:text-gray-800">
                            <i data-lucide="arrow-left" class="h-4 w-4"></i>
                            <span>Kembali ke Dashboard</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto overflow-x-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Data Depo</h2>
                    <p class="text-gray-600">Kelola lokasi depo dan tempat penyimpanan sampah</p>
                </div>
                <button onclick="openAddModal()"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                    Tambah Depo
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Depo</p>
                            <p class="text-2xl font-bold text-indigo-600" id="totalDepo">5</p>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-full">
                            <i data-lucide="warehouse" class="h-6 w-6 text-indigo-600"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Depo Aktif</p>
                            <p class="text-2xl font-bold text-green-600" id="depoAktif">4</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Kapasitas Total</p>
                            <p class="text-2xl font-bold text-purple-600" id="kapasitasTotal">850 ton</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i data-lucide="scale" class="h-6 w-6 text-purple-600"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Maintenance</p>
                            <p class="text-2xl font-bold text-yellow-600" id="depoMaintenance">1</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i data-lucide="wrench" class="h-6 w-6 text-yellow-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <i data-lucide="search" class="h-5 w-5 absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" id="searchInput" placeholder="Cari depo..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <select id="statusFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="all">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                        <button onclick="resetFilter()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Nama Depo</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Lokasi</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Kapasitas</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($depoData as $depo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $depo->nama }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate">{{ $depo->alamat }}</div>
                                        <div class="text-xs text-gray-500">{{ $depo->latitude }}, {{ $depo->longitude }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $depo->kapasitas }}
                                        ton
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($depo->status === 'aktif')
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                        @elseif($depo->status === 'maintenance')
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Maintenance</span>
                                        @elseif($depo->status === 'nonaktif')
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Non-Aktif</span>
                                        @else
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{
                                            $depo->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="editDepo({{ $depo->id }})"
                                                class="text-indigo-600 hover:text-indigo-900">
                                                <i data-lucide="edit" class="h-4 w-4"></i>
                                            </button>
                                            <button onclick="deleteDepo({{ $depo->id }})"
                                                class="text-red-600 hover:text-red-900">
                                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                            </button>
                                            <button
                                                onclick="viewLocation({{ $depo->latitude }}, {{ $depo->longitude }})"
                                                class="text-green-600 hover:text-green-900">
                                                <i data-lucide="map-pin" class="h-4 w-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i data-lucide="database" class="h-12 w-12 text-gray-300 mb-2"></i>
                                            <p>Tidak ada data depo</p>
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
    <!-- Modal Add/Edit Depo -->
    <div id="depoModal"
        class="fixed inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Tambah Depo Baru</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
                </div>

                <form id="depoForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        {{-- <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kode Depo</label>
                            <input type="text" id="kodeDepo" name="kodeDepo" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Contoh: DP001">
                        </div> --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Depo</label>
                            <input type="text" id="namaDepo" name="namaDepo" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Contoh: Depo Sampah Utama">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea id="alamat" name="alamat" rows="3" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Masukkan alamat lengkap depo"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Koordinat Latitude</label>
                            <input type="number" id="latitude" name="latitude" step="any" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Contoh: -5.147665">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Koordinat Longitude</label>
                            <input type="number" id="longitude" name="longitude" step="any" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Contoh: 119.432732">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kapasitas (ton)</label>
                            <input type="number" id="kapasitas" name="kapasitas" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Contoh: 200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="aktif">Aktif</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                        </div>
                    </div>

                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Penanggung Jawab</label>
                        <input type="text" id="penanggungJawab" name="penanggungJawab" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Nama penanggung jawab depo">
                    </div> --}}

                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kontak</label>
                        <input type="tel" id="kontak" name="kontak" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Nomor telepon/WhatsApp">
                    </div> --}}

                    <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal"
        class="fixed inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="p-3 bg-red-100 rounded-full">
                        <i data-lucide="trash-2" class="h-8 w-8 text-red-600"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Konfirmasi Hapus</h3>
                <p class="text-gray-600 text-center mb-6">Apakah Anda yakin ingin menghapus depo ini? Tindakan ini tidak
                    dapat dibatalkan.</p>
                <div class="flex items-center justify-center space-x-4">
                    <button onclick="closeDeleteModal()"
                        class="px-6 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button onclick="confirmDelete()"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Initialize data from server for CRUD operations
            let depoData = @json($depoData);
            let editingId = null;
            let deleteId = null;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.addEventListener('DOMContentLoaded', () => {
                lucide.createIcons();
            });

            // Modal functions
            function openAddModal() {
                editingId = null;
                document.getElementById('modalTitle').textContent = 'Tambah Depo Baru';
                document.getElementById('depoForm').reset();
                document.getElementById('depoModal').classList.remove('hidden');
            }

            function editDepo(id) {
                const depo = depoData.find(d => d.id === id);
                if (!depo) return;

                editingId = id;
                document.getElementById('modalTitle').textContent = 'Edit Depo';

                // Isi form dengan data depo
                // document.getElementById('kodeDepo').value = depo.kode;
                document.getElementById('namaDepo').value = depo.nama;
                document.getElementById('alamat').value = depo.alamat;
                document.getElementById('latitude').value = depo.latitude;
                document.getElementById('longitude').value = depo.longitude;
                document.getElementById('kapasitas').value = depo.kapasitas;
                document.getElementById('status').value = depo.status;
                // document.getElementById('penanggungJawab').value = depo.penanggungJawab;
                // document.getElementById('kontak').value = depo.kontak;

                document.getElementById('depoModal').classList.remove('hidden');
            }

            function closeModal() {
                document.getElementById('depoModal').classList.add('hidden');
                editingId = null;
            }

            function deleteDepo(id) {
                deleteId = id;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
                deleteId = null;
            }

            function confirmDelete() {
                if (deleteId) {
                    fetch(`/depos/${deleteId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(res => res.json())
                        .then(() => {
                            alert('Depo berhasil dihapus!');
                            closeDeleteModal();
                            window.location.reload();
                        });
                }
            }

            function viewLocation(lat, lng) {
                const url = `https://www.google.com/maps?q=${lat},${lng}`;
                window.open(url, '_blank');
            }

            // Form submission
            document.getElementById('depoForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const depoObj = {
                    // kode: formData.get('kodeDepo'),
                    nama: formData.get('namaDepo'),
                    alamat: formData.get('alamat'),
                    latitude: parseFloat(formData.get('latitude')),
                    longitude: parseFloat(formData.get('longitude')),
                    kapasitas: parseInt(formData.get('kapasitas')),
                    status: formData.get('status'),
                    // penanggungJawab: formData.get('penanggungJawab'),
                    // kontak: formData.get('kontak')
                };

                const url = editingId ? `/depos/${editingId}` : '/depos';
                const method = editingId ? 'PUT' : 'POST';
                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(depoObj)
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(editingId ? 'Depo berhasil diperbarui!' : 'Depo berhasil ditambahkan!');
                        closeModal();
                        window.location.reload();
                    });
            });

            function viewLocation(lat, lng) {
                const url = `https://www.google.com/maps?q=${lat},${lng}`;
                window.open(url, '_blank');
            }

            // Search and filter
            document.getElementById('searchInput').addEventListener('input', function() {
                // Implementasi search bisa ditambahkan di sini
            });

            document.getElementById('statusFilter').addEventListener('change', function() {
                // Implementasi filter bisa ditambahkan di sini
            });

            function resetFilter() {
                document.getElementById('searchInput').value = '';
                document.getElementById('statusFilter').value = 'all';
            }

            lucide.createIcons();

    </script>
    @endpush
    @endsection
    {{--
</body>

</html> --}}
