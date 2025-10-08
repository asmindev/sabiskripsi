<?php

namespace App\Http\Controllers;

use App\Models\Armada;
use App\Models\Depots;
use App\Models\TPS;
use App\Services\VRPdjakstrasSolvers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteOptimizationControllers extends Controller
{
    //
    //     use App\Models\Depot;
    // use App\Models\Tps;
    // use App\Services\VRPSolver;
    public function getLocations()
    {
        $depot = DB::table('depots')->first();

        $tpsList = DB::table('t_p_s')->get()->map(function ($tps) {
            return [
                'id' => 'tps' . $tps->id,
                'name' => $tps->nama,
                'lat' => $tps->latitude,
                'lng' => $tps->longitude,
                'demand' => $tps->kapasitas,
                'type' => 'tps'
            ];
        });

        return response()->json([
            'depot' => [
                'id' => 'depot',
                'name' => $depot->nama,
                'lat' => $depot->latitude,
                'lng' => $depot->longitude,
                'type' => 'depot'
            ],
            'tps' => $tpsList
        ]);
    }


    public function getArmada()
    {
        $trucks = DB::table('armadas')->get();

        return response()->json([
            'truckCount' => $trucks->count(),
            'truckCapacity' => $trucks->first()->kapasitas ?? 0
        ]);
    }
    public function runVRP(Request $request)
    {
        // Get all armadas with their assigned TPS
        $armadas = Armada::with('tps')->get();
        $truckCount = $armadas->count();
        $capacity = $armadas->pluck('kapasitas')->toArray();
        $vehicleNames = $armadas->pluck('namaTruk')->toArray();

        // Get depot start (startpoint) and depot end (endpoint/TPA)
        $depotStart = Depots::where('type', 'startpoint')->first();
        $depotEnd = Depots::where('type', 'endpoint')->first();

        // Fallback if type not set
        if (!$depotStart) {
            $depotStart = Depots::first();
        }
        if (!$depotEnd) {
            $depotEnd = Depots::where('id', '!=', $depotStart->id)->first() ?? $depotStart;
        }

        // Create assignments mapping armada to their TPS
        $assignments = [];
        foreach ($armadas as $index => $armada) {
            $assignments[$index] = $armada->tps->map(fn($t) => 'tps' . $t->id)->toArray();
        }

        // Get all TPS with their armada_id
        $tpsList = TPS::all();

        $locations = [
            'depotStart' => [
                'id' => 'depotStart',
                'nama' => $depotStart->nama,
                'lat' => $depotStart->latitude,
                'lng' => $depotStart->longitude,
                'type' => 'startpoint'
            ],
            'depotEnd' => [
                'id' => 'depotEnd',
                'nama' => $depotEnd->nama,
                'lat' => $depotEnd->latitude,
                'lng' => $depotEnd->longitude,
                'type' => 'endpoint'
            ],
            'tps' => $tpsList->map(fn($t) => [
                'id' => 'tps' . $t->id,
                'nama' => $t->nama,
                'lat' => $t->latitude,
                'lng' => $t->longitude,
                'demand' => $t->kapasitas,
                'armada_id' => $t->armada_id
            ])->toArray()
        ];

        $solver = new VRPdjakstrasSolvers($locations, $capacity, $truckCount, $vehicleNames, $assignments);
        $routes = $solver->solve();

        return response()->json([
            'routes' => $routes,
            'statistics' => [
                'totalDistance' => round(array_sum(array_column($routes, 'totalDistance')), 1),
                'totalTime' => round(array_sum(array_column($routes, 'totalTime'))),
                'efficiency' => round((count($locations['tps']) / count($routes)) * 100, 1)
            ]
        ]);
    }
}
