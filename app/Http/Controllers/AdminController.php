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
     * Display the Depo data page.
     */
    public function dataDepo()
    {
        $depoData = Depots::all();

        return view('admins.data.depo', [
            'depoData' => $depoData
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
        $depoData = Depots::all();
        $armadaData = Armada::where('status', 'aktif')->get();

        return view('admins.optimasi.rute', [
            'tpsData' => $tpsData,
            'depoData' => $depoData,
            'armadaData' => $armadaData
        ]);
    }
}
