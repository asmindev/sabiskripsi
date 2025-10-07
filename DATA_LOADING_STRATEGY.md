# Data Loading Strategy - Server-Side Rendering

## 🎯 Perubahan dari AJAX ke Server-Side

### Before (AJAX Pattern):

❌ View kosong → Load → Fetch API → Display data
❌ Multiple HTTP requests
❌ JavaScript dependency
❌ Loading states handling
❌ Error handling di client

### After (Server-Side Pattern):

✅ Controller load data → Pass to view → Display
✅ Single HTTP request
✅ Data ready on page load
✅ No JavaScript needed
✅ Server-side error handling

---

## 📦 Controllers Updated

### 1. **AdminController**

#### dataTps()

```php
public function dataTps()
{
    $tpsData = TPS::all();

    return view('admins.dataTPS', [
        'tpsData' => $tpsData
    ]);
}
```

#### dataDepo()

```php
public function dataDepo()
{
    $depoData = Depots::all();

    return view('admins.dataDepo', [
        'depoData' => $depoData
    ]);
}
```

#### dataArmada()

```php
public function dataArmada()
{
    $armadaData = Armada::all();

    return view('admins.dataArmada', [
        'armadaData' => $armadaData
    ]);
}
```

#### optimasiRute()

```php
public function optimasiRute()
{
    $tpsData = TPS::all();
    $depoData = Depots::all();
    $armadaData = Armada::where('status', 'aktif')->get();

    return view('admins.optimasiRutes', [
        'tpsData' => $tpsData,
        'depoData' => $depoData,
        'armadaData' => $armadaData
    ]);
}
```

---

### 2. **PetugasController**

#### rute()

```php
public function rute()
{
    $tpsData = TPS::all();
    $depoData = Depots::all();
    $armadaData = Armada::where('status', 'aktif')->get();

    return view('petugas.rute', [
        'tpsData' => $tpsData,
        'depoData' => $depoData,
        'armadaData' => $armadaData
    ]);
}
```

---

## 🛣️ Routes Cleaned

### Removed (No longer needed):

```php
❌ Route::get('/tps', [TpsController::class, 'index']);
❌ Route::get('/depos', [DepoControllers::class, 'index']);
❌ Route::get('/locations', [RouteOptimizationControllers::class, 'getLocations']);
❌ Route::get('/armada', [RouteOptimizationControllers::class, 'getArmada']);
```

### Kept (Still needed for processing):

```php
✅ Route::post('/api/run-vrp', [RouteOptimizationControllers::class, 'runVRP']);
```

_Note: VRP route tetap ada untuk proses komputasi optimasi, bukan fetch data._

---

## 📊 Data Available in Views

### Admin Pages:

#### `/dashboard-admin/data-tps`

-   `$tpsData` - Collection of all TPS

#### `/dashboard-admin/data-depo`

-   `$depoData` - Collection of all Depots

#### `/dashboard-admin/data-armada`

-   `$armadaData` - Collection of all Armada

#### `/dashboard-admin/optimasi-rute`

-   `$tpsData` - All TPS
-   `$depoData` - All Depots
-   `$armadaData` - Active Armada only

---

### Petugas Pages:

#### `/rute`

-   `$tpsData` - All TPS
-   `$depoData` - All Depots
-   `$armadaData` - Active Armada only

---

## 🎨 How to Use in Blade

### Before (with AJAX):

```blade
<div id="tps-list">Loading...</div>

<script>
fetch('/tps')
    .then(res => res.json())
    .then(data => {
        // render data
    });
</script>
```

### After (Server-Side):

```blade
@foreach($tpsData as $tps)
    <div class="tps-item">
        <h3>{{ $tps->nama_tps }}</h3>
        <p>{{ $tps->alamat }}</p>
    </div>
@endforeach
```

---

## ✨ Benefits

### 1. **Performance**

-   ✅ Faster initial load (data ready)
-   ✅ No loading spinners needed
-   ✅ Single HTTP request
-   ✅ Server-side caching possible

### 2. **SEO Friendly**

-   ✅ Content visible to crawlers
-   ✅ No JavaScript required
-   ✅ Proper HTML rendering

### 3. **Better UX**

-   ✅ Instant content display
-   ✅ No flash of loading state
-   ✅ Progressive enhancement

### 4. **Simpler Code**

-   ✅ No AJAX logic
-   ✅ No error handling in JS
-   ✅ Easier to debug
-   ✅ Less client-side code

### 5. **Security**

-   ✅ Data validation on server
-   ✅ No API exposure risk
-   ✅ Proper authentication check

---

## 🔄 Data Flow

```
User Request
    ↓
Route
    ↓
Controller (Load from DB)
    ↓
Pass to View
    ↓
Blade Render
    ↓
HTML Response (with data)
```

**Old Flow:**

```
User Request → Empty HTML → JavaScript → AJAX → API → JSON → Render
```

**New Flow:**

```
User Request → Controller → DB Query → View → HTML (with data)
```

---

## 📝 Usage Examples

### DataTPS Page:

```blade
<!-- admins/dataTPS.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Data TPS</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama TPS</th>
                <th>Alamat</th>
                <th>Kapasitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tpsData as $tps)
            <tr>
                <td>{{ $tps->id }}</td>
                <td>{{ $tps->nama_tps }}</td>
                <td>{{ $tps->alamat }}</td>
                <td>{{ $tps->kapasitas }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
```

### Optimasi Rute Page:

```blade
<!-- admins/optimasiRutes.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Optimasi Rute</h1>

    <!-- TPS List -->
    <select name="tps" id="tps">
        @foreach($tpsData as $tps)
            <option value="{{ $tps->id }}">{{ $tps->nama_tps }}</option>
        @endforeach
    </select>

    <!-- Depo List -->
    <select name="depo" id="depo">
        @foreach($depoData as $depo)
            <option value="{{ $depo->id }}">{{ $depo->nama_depo }}</option>
        @endforeach
    </select>

    <!-- Armada List (Active only) -->
    <select name="armada" id="armada">
        @foreach($armadaData as $armada)
            <option value="{{ $armada->id }}">
                {{ $armada->plat_nomor }} - {{ $armada->jenis_kendaraan }}
            </option>
        @endforeach
    </select>
</div>
@endsection
```

---

## 🚀 Migration Checklist

-   [x] Update AdminController to load data
-   [x] Update PetugasController to load data
-   [x] Remove unused API routes
-   [x] Keep VRP route for processing
-   [x] Pass data to views
-   [x] Update views to use $data variables
-   [x] Remove AJAX fetch calls
-   [x] Test all pages load correctly

---

## ⚙️ API Routes Status

### Removed (Data Fetching):

-   ❌ `/tps` - Now loaded in controller
-   ❌ `/depos` - Now loaded in controller
-   ❌ `/locations` - Now loaded in controller
-   ❌ `/armada` - Now loaded in controller

### Kept (Data Processing):

-   ✅ `/api/run-vrp` - VRP computation endpoint

### Kept (CRUD Operations):

-   ✅ `/armadas` (GET, POST, PUT, DELETE) - Admin only
-   ✅ `/depos` (POST, PUT, DELETE) - Admin only
-   ✅ `/tps` (POST, PUT, DELETE) - Admin only
-   ✅ `/notifications` (GET, POST, DELETE) - Admin only

---

## 📌 Important Notes

1. **VRP Endpoint** tetap menggunakan AJAX karena:

    - Proses komputasi berat
    - Asynchronous processing
    - Real-time result needed

2. **CRUD Operations** tetap pakai AJAX untuk:

    - Create, Update, Delete actions
    - Dynamic table updates
    - Better UX tanpa full page reload

3. **Initial Data Load** sekarang server-side:
    - Faster page load
    - Data ready on render
    - No loading states

---

## ✅ Final Structure

```
Controllers load data → Pass to view → Blade render
     ↓                       ↓              ↓
   Models              Variables      @foreach
   Eloquent            $tpsData       Display
   Query               $depoData      HTML
                       $armadaData
```

**Status: ✅ Complete & Optimized!**
