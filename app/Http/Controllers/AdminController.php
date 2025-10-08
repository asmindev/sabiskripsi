<?php

namespace App\Http\Controllers;

use App\Models\Armada;
use App\Models\Depots;
use App\Models\TPS;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the TPS data page.
     */
    public function dataTps()
    {
        $tpsData = TPS::with('armada')->get();

        return view('admins.tps.index', [
            'tpsData' => $tpsData
        ]);
    }

    /**
     * Display the Armada data page.
     */
    public function dataArmada()
    {
        $armadaData = Armada::with('tps')->get();

        return view('admins.armada.index', [
            'armadaData' => $armadaData
        ]);
    }

    /**
     * Display the route optimization page.
     */
    public function optimasiRute()
    {
        $tpsData = TPS::all();
        $depotStart = Depots::where('type', 'startpoint')->first();
        $depotEnd = Depots::where('type', 'endpoint')->first();
        $armadaData = Armada::where('status', 'aktif')->get();

        // Fallback if type not set
        if (!$depotStart) {
            $depotStart = Depots::first();
        }
        if (!$depotEnd) {
            $depotEnd = Depots::where('id', '!=', $depotStart->id)->first() ?? $depotStart;
        }

        return view('admins.optimasi.rute', [
            'tpsData' => $tpsData,
            'depotStart' => $depotStart,
            'depotEnd' => $depotEnd,
            'armadaData' => $armadaData
        ]);
    }
}
