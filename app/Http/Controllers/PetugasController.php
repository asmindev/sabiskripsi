<?php

namespace App\Http\Controllers;

use App\Models\Armada;
use App\Models\Depots;
use App\Models\TPS;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    /**
     * Display the route page.
     */
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
}
