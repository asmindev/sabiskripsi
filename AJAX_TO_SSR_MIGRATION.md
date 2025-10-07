# Migration: AJAX to Server-Side Data Loading

## ✅ Completed Changes

### Files Updated:

#### 1. **dataTPS.blade.php**

**Before:**

```javascript
function fetchTPS() {
    fetch("/tps")
        .then((res) => res.json())
        .then((data) => {
            tpsData = data;
            renderTable();
        });
}
```

**After:**

```javascript
let tpsData = @json($tpsData);
let filteredData = [...tpsData];

document.addEventListener('DOMContentLoaded', function() {
    renderTable();
});
```

---

#### 2. **dataDepo.blade.php**

**Before:**

```javascript
function fetchDepos() {
    fetch("/depos")
        .then((res) => res.json())
        .then((data) => {
            depoData = data;
            renderTable();
        });
}
```

**After:**

```javascript
let depoData = @json($depoData);

document.addEventListener('DOMContentLoaded', () => {
    renderTable();
});
```

**CRUD Operations Updated:**

-   Create/Update: Update local `depoData` array after successful save
-   Delete: Filter `depoData` array after successful delete
-   No need to re-fetch data

---

#### 3. **dataArmada.blade.php**

**Before:**

```javascript
fetch("/armadas")
    .then((res) => res.json())
    .then((data) => {
        armadaData = data;
        renderTable();
    });
```

**After:**

```javascript
let armadaData = @json($armadaData);

document.addEventListener('DOMContentLoaded', () => {
    renderTable();
});
```

---

#### 4. **petugas/rute.blade.php**

**Before:**

```javascript
function fetchTPSData() {
    fetch('/tps').then(...)
}

function fetchDepotsData() {
    fetch('/depos').then(...)
}

// On load
fetchTPSData();
fetchDepotsData();
```

**After:**

```javascript
// Initialize data from server
const serverTpsData = @json($tpsData);
const serverDepoData = @json($depoData);
const serverArmadaData = @json($armadaData);

// Convert to map format
let tpsData = serverTpsData.map(item => ({...}));
let depotsData = serverDepoData.map(item => ({...}));

// Initialize markers
function initializeMarkers() {
    tpsData.forEach(tps => addTPSMarker(tps));
    depotsData.forEach(depot => addDepotMarker(depot));
}

// On load
initializeMarkers();
```

---

## 🛣️ Routes Removed

```php
❌ Route::get('/tps', [TpsController::class, 'index']);
❌ Route::get('/depos', [DepoControllers::class, 'index']);
❌ Route::get('/armada', [ArmadaController::class, 'index']);
```

These routes are no longer needed because:

-   Data is loaded in controller
-   Passed to view via `@json()`
-   No AJAX fetch required

---

## 🎯 Benefits

### Performance

-   ✅ **Faster initial load** - Data ready on page render
-   ✅ **Single request** - No additional AJAX calls
-   ✅ **No loading states** - Content immediately visible

### Code Quality

-   ✅ **Less JavaScript** - Removed fetch logic
-   ✅ **Simpler flow** - Controller → View → Render
-   ✅ **Better maintainability** - Data source in one place

### User Experience

-   ✅ **Instant display** - No waiting for AJAX
-   ✅ **No flash** - No empty state → loading → data
-   ✅ **SEO friendly** - Content in initial HTML

---

## 🔄 CRUD Operations (Still use AJAX)

### Why keep AJAX for CRUD?

1. **Better UX** - No full page reload
2. **Faster feedback** - Immediate response
3. **Partial updates** - Only update changed data
4. **Error handling** - Show errors without losing form data

### Pattern:

```javascript
// Create/Update
fetch("/endpoint", { method: "POST", body: data })
    .then((res) => res.json())
    .then((newData) => {
        // Update local array
        if (editMode) {
            arrayData[index] = newData;
        } else {
            arrayData.push(newData);
        }
        renderTable();
    });

// Delete
fetch("/endpoint/${id}", { method: "DELETE" }).then(() => {
    // Remove from local array
    arrayData = arrayData.filter((item) => item.id !== id);
    renderTable();
});
```

---

## 📊 Data Flow

### Initial Load (Server-Side):

```
Controller → Query DB → Pass to View → @json() → JavaScript
```

### CRUD Operations (Client-Side):

```
User Action → AJAX → API → Response → Update Local Data → Re-render
```

---

## ✅ Testing Checklist

-   [x] dataTPS.blade.php - Loads data from `$tpsData`
-   [x] dataDepo.blade.php - Loads data from `$depoData`
-   [x] dataArmada.blade.php - Loads data from `$armadaData`
-   [x] petugas/rute.blade.php - Loads TPS, Depo, Armada data
-   [x] Remove unused fetch functions
-   [x] Update CRUD to modify local arrays
-   [x] Remove API routes for data fetching
-   [x] Keep API routes for VRP processing
-   [x] Test all pages load correctly
-   [x] Test CRUD operations still work

---

## 🚨 Important Notes

1. **VRP Route Kept:**

    - `POST /api/run-vrp` - Still needed for route optimization
    - This is computational, not data fetching

2. **CRUD Routes Kept:**

    - `/armadas` (POST, PUT, DELETE)
    - `/depos` (POST, PUT, DELETE)
    - `/tps` (POST, PUT, DELETE)
    - These are for operations, not data fetching

3. **Data Freshness:**
    - Initial load: Fresh from DB
    - After CRUD: Updated in local JavaScript array
    - Page refresh: Fresh from DB again

---

## 📝 Code Examples

### Using @json() in Blade:

```blade
<script>
    // Pass Laravel data to JavaScript
    let tpsData = @json($tpsData);

    // Now use it
    tpsData.forEach(tps => {
        console.log(tps.nama);
    });
</script>
```

### Update Local Data After Create:

```javascript
fetch("/depos", {
    method: "POST",
    body: JSON.stringify(newDepo),
})
    .then((res) => res.json())
    .then((createdDepo) => {
        depoData.push(createdDepo); // Add to local array
        renderTable(); // Re-render
    });
```

### Update Local Data After Delete:

```javascript
fetch(`/depos/${id}`, { method: "DELETE" }).then(() => {
    depoData = depoData.filter((d) => d.id !== id); // Remove from array
    renderTable(); // Re-render
});
```

---

## 🎉 Summary

### What Changed:

-   ✅ Removed `fetch('/tps')`, `fetch('/depos')`, `fetch('/armadas')`
-   ✅ Use `@json($data)` to pass data from controller
-   ✅ CRUD operations update local JavaScript arrays
-   ✅ Removed unnecessary API routes

### What Stayed:

-   ✅ CRUD API endpoints (for operations)
-   ✅ VRP API endpoint (for processing)
-   ✅ AJAX for CRUD (better UX)

### Result:

-   🚀 Faster page load
-   📉 Less HTTP requests
-   💪 Simpler codebase
-   ✨ Better performance

**Status: ✅ Migration Complete!**
