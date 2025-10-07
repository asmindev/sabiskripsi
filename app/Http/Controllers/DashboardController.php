<?php

namespace App\Http\Controllers;

use App\Models\Armada;
use App\Models\TPS;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $armadas = Armada::where('status', 'aktif')->get();
        $tpsCounts = TPS::count();
        $truckCounts = $armadas->count();
        $truckCountsTotals = Armada::count();
        $capacity = Armada::sum('kapasitas');

        return view('admins.dashboard', [
            'users' => $user,
            'tpsCounts' => $tpsCounts,
            'truckCounts' => $truckCounts,
            'armadas' => $truckCountsTotals,
            'capacity' => $capacity,
        ]);
    }
}
