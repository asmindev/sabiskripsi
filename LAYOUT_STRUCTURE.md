# Layout Structure - Sistem Optimasi Rute TPS

## 📁 Struktur Layout (Laravel Convention)

### Layouts yang Tersedia:

#### 1. **`layouts/app.blade.php`** - Layout Utama (Authenticated)

Digunakan untuk semua halaman yang memerlukan autentikasi (admin & pengguna).

**Fitur:**

-   ✅ Tailwind CSS v4
-   ✅ Chart.js (untuk grafik)
-   ✅ Lucide Icons
-   ✅ Font Awesome
-   ✅ Google Fonts (Poppins)
-   ✅ CSRF Token
-   ✅ Include Sidebar/Header (`includes.headers`)
-   ✅ Stack untuk styles & scripts tambahan

**Digunakan oleh:**

-   `admins/dashboard.blade.php`
-   `admins/dataTPS.blade.php`
-   `admins/dataDepo.blade.php`
-   `admins/dataArmada.blade.php`
-   `admins/optimasiRutes.blade.php`
-   `petugas/rute.blade.php`

**Cara Penggunaan:**

```blade
@extends('layouts.app')

@section('title', 'Judul Halaman')

@push('styles')
<style>
    /* Custom CSS */
</style>
@endpush

@section('content')
    <!-- Konten halaman -->
@endsection

@push('scripts')
<script>
    // Custom JavaScript
</script>
@endpush
```

---

#### 2. **`layouts/guest.blade.php`** - Layout Guest (Unauthenticated)

Digunakan untuk halaman publik seperti login, register, dll.

**Fitur:**

-   ✅ Tailwind CSS v4
-   ✅ Lucide Icons
-   ✅ Google Fonts (Poppins)
-   ✅ CSRF Token
-   ✅ Background gradient untuk auth pages
-   ✅ Stack untuk styles & scripts tambahan

**Digunakan oleh:**

-   `auth/login.blade.php`

**Cara Penggunaan:**

```blade
@extends('layouts.guest')

@section('title', 'Login - Sistem TPS')

@push('styles')
<style>
    /* Custom CSS */
</style>
@endpush

@section('content')
    <!-- Konten halaman -->
@endsection

@push('scripts')
<script>
    // Custom JavaScript
</script>
@endpush
```

---

## 🎨 Best Practices

### 1. **Naming Convention (Laravel Style)**

-   ✅ `app.blade.php` - layout utama (bukan `apps`)
-   ✅ `guest.blade.php` - layout guest
-   ✅ Gunakan singular, bukan plural

### 2. **Struktur Blade Template**

```blade
@extends('layouts.app')           // 1. Extends layout
@section('title', 'Page Title')    // 2. Set title

@push('styles')                     // 3. Custom styles (optional)
    <style>...</style>
@endpush

@section('content')                 // 4. Main content
    <!-- Your content here -->
@endsection

@push('scripts')                    // 5. Custom scripts (optional)
    <script>...</script>
@endpush
```

### 3. **Stack untuk Assets Tambahan**

-   `@push('styles')` - untuk CSS custom per halaman
-   `@push('scripts')` - untuk JavaScript custom per halaman

### 4. **Include Partials**

Gunakan `@include()` untuk component yang reusable:

```blade
@include('includes.headers')     // Sidebar/Header
@include('includes.footer')      // Footer (jika ada)
@include('components.alert')     // Alert component
```

---

## 📦 Dependencies yang Tersedia

### CSS Frameworks & Icons:

-   Tailwind CSS v4 (via CDN)
-   Font Awesome 6.4.0
-   Lucide Icons (latest)
-   Google Fonts - Poppins

### JavaScript Libraries:

-   Chart.js 3.9.1 (untuk visualisasi data)
-   Lucide Icons (auto-initialize)

### Laravel Built-in:

-   CSRF Protection
-   Blade Templating
-   Asset Helper

---

## 🚀 Migration Summary

### Files Renamed:

-   ❌ `layouts/apps.blade.php` → ✅ `layouts/app.blade.php`

### Files Created:

-   ✅ `layouts/app.blade.php` - Main layout
-   ✅ `layouts/guest.blade.php` - Guest layout

### Files Deleted:

-   ❌ `layouts/apps.blade.php` (old)
-   ❌ `layouts/apps.blade copy.php`
-   ❌ `layouts/apps.blade copy 2.php`
-   ❌ `layouts/appsAdmins.blade.php`

### Views Updated (Auto-replaced):

-   ✅ All admin views (`admins/*.blade.php`)
-   ✅ All petugas views (`petugas/*.blade.php`)
-   ✅ Login view (`auth/login.blade.php`)

---

## ✨ Keuntungan Struktur Baru

1. **Konsisten dengan Laravel Convention** - mengikuti best practice Laravel
2. **Reusable** - satu layout untuk banyak halaman
3. **Maintainable** - mudah update assets & dependencies
4. **Clean Code** - view files lebih ringkas
5. **Flexible** - mudah tambah custom styles/scripts per halaman
6. **SEO Friendly** - dynamic title per halaman

---

## 📝 Contoh Implementasi

### Dashboard Page:

```blade
@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="p-6">
        <h1>Dashboard</h1>
        <!-- Content -->
    </div>
@endsection

@push('scripts')
<script>
    // Initialize dashboard charts
    console.log('Dashboard loaded');
</script>
@endpush
```

### Login Page:

```blade
@extends('layouts.guest')

@section('title', 'Login')

@push('styles')
<style>
    .custom-login { /* styles */ }
</style>
@endpush

@section('content')
    <div class="min-h-screen flex items-center justify-center">
        <!-- Login form -->
    </div>
@endsection
```
