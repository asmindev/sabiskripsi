@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Manajemen Truk Sampah')

@section('content')
@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .animate-counter {
        animation: countUp 2s ease-out;
    }

    @keyframes countUp {
        from { transform: scale(0.5); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .notification-badge {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* Scrollable container notifikasi */
    #notificationContainerInner {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 4px;
    }
</style>
@endpush

<!-- Main Content Area -->
<div class="flex-1 flex flex-col overflow-hidden">
    <!-- Header -->
    <div class="bg-white shadow-lg border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Dashboard Overview</h1>
                <p class="text-sm text-gray-500">Selamat datang kembali, Admin Super</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Tanggal -->
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <i data-lucide="calendar" class="h-4 w-4"></i>
                    <span id="currentDate"></span>
                </div>
                <!-- Waktu -->
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <i data-lucide="clock" class="h-4 w-4"></i>
                    <span id="currentTime"></span>
                </div>
                <!-- Notifikasi Icon -->
                <div class="relative">
                    <button id="notificationBell" class="p-2 rounded-full hover:bg-gray-200 relative">
                        🔔
                        <span id="notificationBadgeCount"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                            0
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Notifikasi -->
    <div id="notificationSidebar" class="fixed top-20 right-4 w-72 z-50 space-y-2 hidden">
        <div class="flex items-center justify-between p-4 border-b bg-white rounded-t-lg shadow">
            <h2 class="text-lg font-semibold">Notifikasi</h2>
            <button id="closeSidebar" class="text-gray-500 hover:text-gray-700">✖</button>
        </div>
        <div id="notificationContainerInner" class="p-4 space-y-3 bg-white rounded-b-lg shadow sidebar-scroll">
            <!-- Semua notifikasi akan ditambahkan di sini -->
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="flex-1 overflow-y-auto px-6 py-6">
        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total TPS -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total TPS</p>
                        <p class="text-3xl font-bold text-blue-600 animate-counter" id="totalTPS">{{ $tpsCounts }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i data-lucide="trending-up" class="h-3 w-3 inline mr-1"></i>
                            +12 bulan ini
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i data-lucide="map-pin" class="h-8 w-8 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Truk Aktif -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Truk Aktif</p>
                        <p class="text-3xl font-bold text-green-600 animate-counter" id="activeTrucks">{{ $truckCounts }}</p>
                        <p class="text-xs text-gray-500 mt-1">dari {{ $armadas }} total truk</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i data-lucide="truck" class="h-8 w-8 text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Kapasitas -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Kapasitas</p>
                        <p class="text-3xl font-bold text-purple-600 animate-counter" id="dailyRoutes">{{ $capacity }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i data-lucide="check-circle" class="h-3 w-3 inline mr-1"></i>
                            <span id="completedRoutes"></span> selesai
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i data-lucide="route" class="h-8 w-8 text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/lucide/dist/lucide.min.js"></script>
<!-- Tambahkan script Lucide -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const bell = document.getElementById("notificationBell");
    const sidebar = document.getElementById("notificationSidebar");
    const closeBtn = document.getElementById("closeSidebar");
    const badge = document.getElementById("notificationBadgeCount");
    const container = document.getElementById('notificationContainerInner');

    // Render notifikasi ke DOM
    function renderNotification(id, message) {
        const notif = document.createElement('div');
        notif.className = 'flex items-center justify-between p-3 bg-gray-100 rounded-lg shadow border border-gray-200';
        notif.dataset.id = id;

        const text = document.createElement('span');
        text.textContent = message;

        const close = document.createElement('button');
        close.innerHTML = '✖';
        close.className = 'ml-3 text-gray-400 hover:text-gray-600 cursor-pointer text-sm font-bold';
        close.onclick = async () => {
            // Hapus dari DOM
            notif.remove();
            badge.textContent = Math.max(parseInt(badge.textContent || '0') - 1, 0);

            // Hapus dari server
            try {
                await fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
            } catch (err) {
                console.error("Gagal hapus notifikasi:", err);
            }
        };

        notif.appendChild(text);
        notif.appendChild(close);
        container.prepend(notif);
    }

    // Fetch notifikasi dari server
    async function loadNotifications() {
        const res = await fetch("{{ route('notifications.index') }}");
        const data = await res.json();

        badge.textContent = data.length;
        data.forEach(msg => renderNotification(msg.id, msg.message));
    }

    // Toggle sidebar
    bell.addEventListener('click', () => sidebar.classList.toggle('hidden'));
    closeBtn.addEventListener('click', () => sidebar.classList.add('hidden'));

    loadNotifications();

    // Fungsi menambah notifikasi baru (dari server/event)
    window.addNotification = function(message){
        renderNotification(message);
        badge.textContent = parseInt(badge.textContent||'0')+1;
    }

    // Update date & time
    function updateDateTime() {
        const now = new Date();
        document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Dashboard data
    const dashboardData = {
        totalTPS: {{ $tpsCounts }},
        activeTrucks: {{ $truckCounts }},
        dailyRoutes: {{ $capacity }},
        completedRoutes: 28,
        efficiency: 87,
        truckStatus: { operating:12, traveling:3, maintenance:2, inactive:1 },
        activities: [
            { id:1, type:'success', icon:'check-circle', message:'Truk A-001 menyelesaikan rute TPS Kelurahan Mandonga', time:'2 menit yang lalu' },
            { id:2, type:'info', icon:'truck', message:'Truk B-002 memulai pengangkutan di TPS Pasar Sentral', time:'15 menit yang lalu' },
            { id:3, type:'warning', icon:'alert-circle', message:'Truk C-003 memerlukan maintenance rutin', time:'1 jam yang lalu' },
            { id:4, type:'info', icon:'map-pin', message:'Truk C-003 memerlukan maintenance rutin', time:'1 jam yang lalu' }
        ]
    };

    // Counter animation
    function animateCounter(element, target, duration=2000) {
        let current = 0, increment = target/(duration/16);
        const timer = setInterval(() => {
            current += increment;
            if(current>=target){ current=target; clearInterval(timer); }
            element.textContent = Math.floor(current);
        },16);
    }
    animateCounter(document.getElementById('totalTPS'), dashboardData.totalTPS);
    animateCounter(document.getElementById('activeTrucks'), dashboardData.activeTrucks);
    animateCounter(document.getElementById('dailyRoutes'), dashboardData.dailyRoutes);

    // Notifikasi
   window.addNotification = function(message){
    const container = document.getElementById('notificationContainer');
    const badge = document.getElementById('notificationBadgeCount');
    if(!container) return;

    const notif = document.createElement('div');
    notif.className='p-3 bg-gray-100 rounded-lg shadow border border-gray-200';
    notif.textContent = message;
    container.prepend(notif);

    if(badge) badge.textContent = parseInt(badge.textContent||'0')+1;

    // Hapus notifikasi otomatis setelah 8 detik
    // setTimeout(()=>{
    //     notif.remove();
    //     if(badge) badge.textContent = Math.max(parseInt(badge.textContent)-1,0);
    // },8000)
}
});
</script>
@endpush
@endsection
