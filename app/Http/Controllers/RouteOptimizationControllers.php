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
        // $truckCount = (int) $request->truckCount;
        // $capacity = (int) $request->truckCapacity;

        // use App\Models\Armada;

        $armadas = Armada::all(); // atau ->where('status', 'aktif')->get();
        $truckCount = $armadas->count();
        $capacity = $armadas->pluck('kapasitas')->toArray();
        $vehicleNames = $armadas->pluck('namaTruk')->toArray();

        $depot = Depots::first();
        $tpsList = TPS::all();

        $locations = [
            'depot' => [
                'id' => 'depot',
                'nama' => $depot->nama,
                'lat' => $depot->latitude,
                'lng' => $depot->longitude
            ],
            'tps' => $tpsList->map(fn($t) => [
                'id' => 'tps' . $t->id,
                'nama' => $t->nama,
                'lat' => $t->latitude,
                'lng' => $t->longitude,
                'demand' => $t->kapasitas
            ])->toArray()
        ];

        $solver = new VRPdjakstrasSolvers($locations, $capacity, $truckCount, $vehicleNames);
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
