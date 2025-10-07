@extends('layouts.guest')

@section('title', 'Login - Sistem Manajemen TPS')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    /* Custom animations */
    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .float-animation {
        animation: float 3s ease-in-out infinite;
    }

    .float-animation-delayed {
        animation: float 3s ease-in-out infinite 1.5s;
    }

    /* Gradient background */
    .bg-gradient-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Glass effect */
    .glass-effect {
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
</style>
@endpush

@section('content')
<!-- Background decorations -->
<div class="absolute inset-0 overflow-hidden">
    <!-- Floating waste bins -->
    <div class="absolute top-20 left-10 opacity-10 float-animation">
        <i data-lucide="trash-2" class="h-16 w-16 text-white"></i>
    </div>
    <div class="absolute top-40 right-20 opacity-10 float-animation-delayed">
        <i data-lucide="recycle" class="h-20 w-20 text-white"></i>
    </div>
    <div class="absolute bottom-20 left-20 opacity-10 float-animation">
        <i data-lucide="truck" class="h-18 w-18 text-white"></i>
    </div>
    <div class="absolute bottom-40 right-10 opacity-10 float-animation-delayed">
        <i data-lucide="leaf" class="h-14 w-14 text-white"></i>
    </div>

    <!-- Geometric shapes -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2">
    </div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full translate-x-1/2 translate-y-1/2">
    </div>
</div>

<!-- Main content -->
<div class="relative z-10 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo and title -->
        <div class="text-center mb-8">
            {{-- <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-6">
                <i data-lucide="recycle" class="h-10 w-10 text-green-600"></i>
            </div> --}}
            <h1 class="text-3xl font-bold text-white mb-2">Sistem Manajemen TPS</h1>
            <p class="text-white/80">Tempat Pembuangan Sampah Digital</p>
        </div>

        <!-- Login form -->
        <div class="glass-effect rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-white mb-2">Masuk ke Sistem</h2>
                <p class="text-white/70">Silakan masukkan kredensial Anda</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Error Alert -->
                @if ($errors->any())
                <div class="bg-red-500/20 border border-red-500/50 text-white px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-circle" class="h-5 w-5 mr-2"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
                @endif

                <!-- Success Alert -->
                @if (session('success'))
                <div class="bg-green-500/20 border border-green-500/50 text-white px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                <!-- Email field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-white">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="h-5 w-5 text-white/60"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-10 pr-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-white/50 transition-all duration-200"
                            placeholder="Masukkan email">
                    </div>
                </div>

                <!-- Password field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-white">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-white/60"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full pl-10 pr-12 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-white/50 transition-all duration-200"
                            placeholder="Masukkan password">
                        <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i data-lucide="eye" class="h-5 w-5 text-white/60 hover:text-white transition-colors"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-white/30 rounded bg-white/20">
                        <label for="remember" class="ml-2 block text-sm text-white">
                            Ingat saya
                        </label>
                    </div>
                </div>

                <!-- Login button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <span class="flex items-center justify-center">
                        <i data-lucide="log-in" class="h-5 w-5 mr-2"></i>
                        Masuk ke Sistem
                    </span>
                </button>
            </form>

            <!-- Divider -->
            {{-- <div class="my-6 flex items-center">
                <div class="flex-1 border-t border-white/20"></div>
                <span class="px-4 text-white/60 text-sm">atau</span>
                <div class="flex-1 border-t border-white/20"></div>
            </div> --}}

            <!-- Alternative login methods -->
            {{-- <div class="space-y-3">
                <button
                    class="w-full bg-white/10 border border-white/20 text-white py-3 px-4 rounded-lg font-medium hover:bg-white/20 transition-all duration-200 flex items-center justify-center">
                    <i data-lucide="smartphone" class="h-5 w-5 mr-2"></i>
                    Masuk dengan SMS
                </button>

                <button
                    class="w-full bg-white/10 border border-white/20 text-white py-3 px-4 rounded-lg font-medium hover:bg-white/20 transition-all duration-200 flex items-center justify-center">
                    <i data-lucide="qr-code" class="h-5 w-5 mr-2"></i>
                    Scan QR Code
                </button>
            </div> --}}
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-white/70">
            <p class="text-sm">
                Belum punya akun?
                <a href="#" class="text-white hover:text-green-300 font-medium transition-colors">
                    Daftar di sini
                </a>
            </p>
            <p class="text-xs mt-4">
                © 2024 Sistem Manajemen TPS. Semua hak dilindungi.
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        });

        // Input focus animations
        lucide.createIcons();
        });

        // Input focus animations
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
</script>
@endpush
@endsection
