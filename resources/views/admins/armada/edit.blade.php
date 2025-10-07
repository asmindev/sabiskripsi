@extends('layouts.app')

@section('title', 'Edit Armada - Sistem Manajemen Truk Sampah')

@section('content')

<div class="flex-1 flex flex-col overflow-hidden">
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.armada') }}" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="arrow-left" class="h-6 w-6"></i>
                    </a>
                    <div class="flex-shrink-0">
                        <i data-lucide="truck" class="h-8 w-8 text-indigo-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Edit Armada: {{ $armada->namaTruk }}</h1>
                        <p class="text-sm text-gray-500">Perbarui data armada truk sampah</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('armadas.update', $armada->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Fields Column (2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Data Armada Card -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Armada</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Truk <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="namaTruk" value="{{ old('namaTruk', $armada->namaTruk) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('namaTruk') border-red-500 @enderror"
                                    placeholder="Contoh: Truk Sampah A-001" required>
                                @error('namaTruk')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Plat <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="nomorPlat" value="{{ old('nomorPlat', $armada->nomorPlat) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nomorPlat') border-red-500 @enderror"
                                    placeholder="Contoh: B 1234 ABC" required>
                                @error('nomorPlat')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas (ton) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="kapasitas" value="{{ old('kapasitas', $armada->kapasitas) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('kapasitas') border-red-500 @enderror"
                                    placeholder="Contoh: 10" step="0.1" min="0.1" required>
                                @error('kapasitas')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                        class="text-red-500">*</span></label>
                                <select name="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror"
                                    required>
                                    <option value="">Pilih Status</option>
                                    <option value="Aktif" {{ old('status', $armada->status) == 'Aktif' ? 'selected' : ''
                                        }}>Aktif</option>
                                    <option value="Maintenance" {{ old('status', $armada->status) == 'Maintenance' ?
                                        'selected' : '' }}>Maintenance</option>
                                    <option value="Tidak Aktif" {{ old('status', $armada->status) == 'Tidak Aktif' ?
                                        'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Driver</label>
                                <input type="text" name="driver" value="{{ old('driver', $armada->driver) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Nama driver">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Maintenance</label>
                                <input type="date" name="lastMaintenance"
                                    value="{{ old('lastMaintenance', $armada->lastMaintenance) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Current TPS Assignment Info -->
                    @if($armada->tps->count() > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">
                            <i data-lucide="info" class="h-5 w-5 inline mr-2"></i>
                            TPS Saat Ini Ter-assign
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($armada->tps as $tps)
                            <div class="flex items-center text-sm text-blue-800">
                                <i data-lucide="map-pin" class="h-4 w-4 mr-2"></i>
                                <span class="font-medium">{{ $tps->nama }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- TPS Selection Column (1/3) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Ubah Assignment TPS</h3>
                        <p class="text-xs text-gray-500 mb-4">Pilih TPS yang akan di-assign. TPS yang sudah ter-assign
                            ke armada lain tidak bisa dipilih.</p>

                        <div class="border border-gray-300 rounded-lg p-4 max-h-[500px] overflow-y-auto bg-gray-50">
                            <div class="space-y-2">
                                @php
                                $currentTpsIds = old('tps_ids', $armada->tps->pluck('id')->toArray());
                                @endphp

                                @forelse($availableTPS as $tps)
                                <div class="flex items-start p-2 hover:bg-gray-100 rounded">
                                    <input type="checkbox" name="tps_ids[]" id="tps_{{ $tps->id }}"
                                        value="{{ $tps->id }}"
                                        class="tps-checkbox mt-1 w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                        onchange="updateSelectedCount()" {{ in_array($tps->id, $currentTpsIds) ?
                                    'checked' : '' }}>
                                    <label for="tps_{{ $tps->id }}"
                                        class="ml-2 text-sm text-gray-700 cursor-pointer flex-1">
                                        <span class="font-medium">{{ $tps->nama }}</span>
                                        <span class="text-xs text-gray-500 block">{{ $tps->alamat }}</span>
                                        @if(in_array($tps->id, $armada->tps->pluck('id')->toArray()))
                                        <span
                                            class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                            Saat ini ter-assign
                                        </span>
                                        @endif
                                    </label>
                                </div>
                                @empty
                                <div class="text-center py-8 text-gray-500">
                                    <i data-lucide="inbox" class="h-12 w-12 mx-auto text-gray-300 mb-2"></i>
                                    <p class="text-sm">Tidak ada TPS yang tersedia</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-3 text-sm text-gray-600">
                            <i data-lucide="check-square" class="h-4 w-4 inline"></i>
                            <span id="selectedCount">{{ count($currentTpsIds) }}</span> TPS dipilih
                        </div>

                        <div class="mt-6 space-y-3">
                            <button type="submit"
                                class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                                <i data-lucide="save" class="h-4 w-4 inline mr-2"></i>
                                Update Armada
                            </button>
                            <a href="{{ route('admin.armada') }}"
                                class="block w-full bg-gray-300 text-gray-700 py-2.5 px-4 rounded-lg hover:bg-gray-400 transition-colors text-center font-semibold">
                                <i data-lucide="x" class="h-4 w-4 inline mr-2"></i>
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        updateSelectedCount();
    });

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.tps-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = checked;
    }
</script>
@endpush
@endsection
