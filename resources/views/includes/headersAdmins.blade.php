<header class="bg-white shadow-sm border-b">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Left side with hamburger menu and navigation -->
            <div class="flex items-center space-x-8">
                <button class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Navigation Tabs -->
                <nav class="flex space-x-8">
                    <a href="/admin/dashboard" class="text-blue-600 border-b-2 border-blue-600 pb-2 font-medium">Dashboard</a>
                    <a href="{{ route('destinations.index') }}" class="text-gray-600 hover:text-gray-900 pb-2">Wisata</a>
                    {{-- <a href="#" class="text-gray-600 hover:text-gray-900 pb-2">Kategori</a> --}}
                    <a href="/users" class="text-gray-600 hover:text-gray-900 pb-2">Pengguna</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"  class="text-gray-600 hover:text-gray-900 pb-2 cursor-pointer">Log Out</button>
                    </form>
                </nav>
            </div>

            <!-- Right side with profile -->
            <div class="flex items-center">
                <img src="/api/placeholder/40/40" alt="Profile" class="w-10 h-10 rounded-full">
            </div>
        </div>
    </div>
</header>
