@php
    // Menu berdasarkan role
    $menu = [
        'admin' => [
            ['title' => 'Dashboard', 'icon' => 'layout-dashboard', 'url' => '/dashboard-admin'],
            ['title' => 'Daftar Truk', 'icon' => 'truck', 'url' => '/dashboard-admin/data-armada', 'section' => 'Manajemen Truk'],
            ['title' => 'Daftar TPS', 'icon' => 'building', 'url' => '/dashboard-admin/data-tps', 'section' => 'Pengelolaan TPS'],
            ['title' => 'Peta TPS', 'icon' => 'map', 'url' => '/rute', 'section' => 'Pengelolaan TPS'],
            ['title' => 'Analisis Optimasi Rutes', 'icon' => 'trending-up', 'url' => '/dashboard-admin/optimasi-rute', 'section' => 'Laporan & Analisis'],
        ],
        'pengguna' => [
            ['title' => 'Dashboard', 'icon' => 'layout-dashboard', 'url' => '/dashboard-admin'],
            ['title' => 'Peta TPS', 'icon' => 'map', 'url' => '/rute', 'section' => 'TPS Saya'],
            ['title' => 'Analisis Optimasi Rutes', 'icon' => 'trending-up', 'url' => '/dashboard-admin/optimasi-rute', 'section' => 'Laporan & Analisis'],
        ],
    ];

    // Ambil role, default ke 'user' jika tidak ada
    $role = Auth::user()->role ?? 'user';
    $currentSection = '';
@endphp

<div class="w-64 bg-white shadow-2xl border-r border-gray-200 flex flex-col">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg">
                <i data-lucide="truck" class="h-6 w-6 text-white"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900">TrashFlow</h1>
                <p class="text-xs text-gray-500">{{ $role === 'admin' ? 'Admin Panel' : 'User Panel' }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 sidebar-scroll overflow-y-auto">
        <div class="space-y-1">
            @if(isset($menu[$role]))
                @foreach($menu[$role] as $item)
                    @if(isset($item['section']) && $item['section'] !== $currentSection)
                        <div class="mt-6">
                            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ $item['section'] }}</h3>
                        </div>
                        @php $currentSection = $item['section']; @endphp
                    @endif
                    <a href="{{ $item['url'] }}"
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors
                       {{ request()->is(ltrim($item['url'], '/')) ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5"></i>
                        <span>{{ $item['title'] }}</span>
                    </a>
                @endforeach
            @else
                <p class="px-4 py-2 text-red-500">Role tidak terdefinisi di menu.</p>
            @endif
        </div>
    </nav>

    <!-- User Profile & Logout -->
    <div class="p-4 border-t border-gray-100">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                <i data-lucide="user" class="h-5 w-5 text-white"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button id="logoutBtn" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                <i data-lucide="log-out" class="h-4 w-4"></i>
                <span class="text-sm">Logout</span>
            </button>
        </form>
    </div>
</div>
