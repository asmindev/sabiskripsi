<?php

namespace App\Services;

class VRPdjakstrasSolvers
{
    protected $locations;
    protected $vehicleCapacity;
    protected $vehicleCount;
    protected $vehicleNames;
    protected $graph;

    public function __construct($locations, $vehicleCapacity, $vehicleCount, $vehicleNames)
    {
        $this->locations = $locations;
        $this->vehicleCapacity = $vehicleCapacity;
        $this->vehicleCount = $vehicleCount;
        $this->vehicleNames = $vehicleNames;

        // Bangun graph dengan jarak antar node
        $this->graph = $this->buildGraph();
    }

    public function solve()
    {
        $routes = [];
        $unassigned = $this->locations['tps'];

        for ($v = 0; $v < $this->vehicleCount && count($unassigned) > 0; $v++) {
            $routes[] = $this->createRoute($unassigned, $v);
        }

        return $routes;
    }

    private function createRoute(&$unassigned, $vehicleIndex)
    {
        $vehicleCapacitys = $this->vehicleCapacity[$vehicleIndex];
        $vehicleName = $this->vehicleNames[$vehicleIndex] ?? 'Truk ' . chr(65 + $vehicleIndex);
        $route = [
            'vehicle' => $vehicleName,
            'path' => ['depot'],
            'tpsVisited' => [],
            'totalDistance' => 0,
            'totalTime' => 0,
            'load' => 0
        ];

        $currentLocation = 'depot';

        while (count($unassigned) > 0 && $route['load'] < $vehicleCapacitys) {
            $nearest = null;
            $minDistance = INF;

            // Jalankan Dijkstra dari lokasi saat ini
            $distances = $this->dijkstra($currentLocation);

            foreach ($unassigned as $index => $tps) {
                if ($route['load'] + $tps['demand'] <= $vehicleCapacitys) {
                    $distance = $distances[$tps['id']] ?? INF;

                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $nearest = ['tps' => $tps, 'index' => $index];
                    }
                }
            }

            if ($nearest) {
                $route['path'][] = $nearest['tps']['id'];
                $route['tpsVisited'][] = $nearest['tps'];
                $route['totalDistance'] += $minDistance;
                $route['load'] += $nearest['tps']['demand'];
                $currentLocation = $nearest['tps']['id'];
                array_splice($unassigned, $nearest['index'], 1);
            } else {
                break;
            }
        }

        // Tambahkan jarak balik ke depot
        $distances = $this->dijkstra($currentLocation);
        $route['totalDistance'] += $distances['depot'] ?? 0;
        $route['path'][] = 'depot';

        $route['totalTime'] = ($route['totalDistance'] / 30) * 60 + (count($route['tpsVisited']) * 10);

        return $route;
    }

    private function buildGraph()
    {
        $nodes = array_merge(['depot' => $this->locations['depot']], collect($this->locations['tps'])->keyBy('id')->toArray());
        $graph = [];

        foreach ($nodes as $id1 => $point1) {
            foreach ($nodes as $id2 => $point2) {
                if ($id1 !== $id2) {
                    $graph[$id1][$id2] = $this->calculateDistance($point1, $point2);
                }
            }
        }

        return $graph;
    }

    private function dijkstra($start)
    {
        $dist = [];
        $visited = [];

        foreach ($this->graph as $node => $edges) {
            $dist[$node] = INF;
        }
        $dist[$start] = 0;

        while (count($visited) < count($this->graph)) {
            // Ambil node dengan jarak minimum yang belum dikunjungi
            $minNode = null;
            $minDist = INF;

            foreach ($dist as $node => $d) {
                if (!isset($visited[$node]) && $d < $minDist) {
                    $minDist = $d;
                    $minNode = $node;
                }
            }

            if ($minNode === null) break;

            $visited[$minNode] = true;

            // Relaxation
            foreach ($this->graph[$minNode] as $neighbor => $weight) {
                if (!isset($visited[$neighbor])) {
                    $newDist = $dist[$minNode] + $weight;
                    if ($newDist < $dist[$neighbor]) {
                        $dist[$neighbor] = $newDist;
                    }
                }
            }
        }

        return $dist;
    }

    private function calculateDistance($point1, $point2)
    {
        // Masih pakai Haversine, tapi bisa diganti data real dari OSRM/Google Maps
        $lat1 = deg2rad($point1['lat']);
        $lon1 = deg2rad($point1['lng']);
        $lat2 = deg2rad($point2['lat']);
        $lon2 = deg2rad($point2['lng']);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat/2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon/2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $r = 6371;
        return $r * $c;
    }
}
