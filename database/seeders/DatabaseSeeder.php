<?php

namespace Database\Seeders;

use App\Models\Armada;
use App\Models\Depots;
use App\Models\TPS;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('123'),
            'role' => 'admin',
        ]);

        Depots::create([
            'nama' => 'Depot Utama',
            'alamat' => 'Kambu',
            'status' => 'aktif',
            'latitude' => -4.0,
            'longitude' => 122.5,
            'kapasitas' => 100,
        ]);
        Armada::insert([
            [
                'namaTruk' => 'Truk A',
                'nomorPlat' => 'DT 1234 AB',
                'kapasitas' => 3000,
                'status' => 'Aktif',
                'driver' => 'Budi',
                'lastMaintenance' => '2024-12-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'namaTruk' => 'Truk B',
                'nomorPlat' => 'DT 5678 CD',
                'kapasitas' => 2500,
                'status' => 'Perawatan',
                'driver' => 'Joko',
                'lastMaintenance' => '2025-01-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'namaTruk' => 'Truk C',
                'nomorPlat' => 'DT 9012 EF',
                'kapasitas' => 4000,
                'status' => 'Tidak Aktif',
                'driver' => 'Andi',
                'lastMaintenance' => '2024-11-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        // $tpsList = [
        //     ['nama' => 'TPS Jalan Merdeka', 'latitude' => -3.98, 'longitude' => 122.48, 'kapasitas' => 3],
        //     ['nama' => 'TPS Pasar Sentral', 'latitude' => -3.99, 'longitude' => 122.52, 'kapasitas' => 5],
        //     ['nama' => 'TPS Perumahan A', 'latitude' => -4.01, 'longitude' => 122.49, 'kapasitas' => 2],
        //     ['nama' => 'TPS Sekolah', 'latitude' => -4.02, 'longitude' => 122.53, 'kapasitas' => 4],
        //     ['nama' => 'TPS Perkantoran', 'latitude' => -3.97, 'longitude' => 122.47, 'kapasitas' => 3],
        //     ['nama' => 'TPS Mall', 'latitude' => -3.995, 'longitude' => 122.515, 'kapasitas' => 6],
        // ];

        $tpsList = [
            [
                'nama' => 'TPS Jalan Merdeka',
                'alamat' => 'Jl. Merdeka No. 123, Kendari',
                'latitude' => -3.98,
                'longitude' => 122.48,
                'kapasitas' => 3,
                'status' => 'Aktif'
            ],
            [
                'nama' => 'TPS Pasar Sentral',
                'alamat' => 'Jl. Pasar Sentral No. 45, Kendari',
                'latitude' => -3.99,
                'longitude' => 122.52,
                'kapasitas' => 5,
                'status' => 'Aktif'
            ],
            [
                'nama' => 'TPS Perumahan A',
                'alamat' => 'Perumahan A Blok B, Kendari',
                'latitude' => -4.01,
                'longitude' => 122.49,
                'kapasitas' => 2,
                'status' => 'Maintenance'
            ],
            [
                'nama' => 'TPS Sekolah',
                'alamat' => 'Jl. Pendidikan No. 10, Kendari',
                'latitude' => -4.02,
                'longitude' => 122.53,
                'kapasitas' => 4,
                'status' => 'Aktif'
            ],
            [
                'nama' => 'TPS Perkantoran',
                'alamat' => 'Jl. Kantor Walikota No. 5, Kendari',
                'latitude' => -3.97,
                'longitude' => 122.47,
                'kapasitas' => 3,
                'status' => 'Tidak Aktif'
            ],
            [
                'nama' => 'TPS Mall',
                'alamat' => 'Jl. Mall Kendari No. 1, Kendari',
                'latitude' => -3.995,
                'longitude' => 122.515,
                'kapasitas' => 6,
                'status' => 'Aktif'
            ],
            [
                'nama' => 'TPS Rumah Sakit',
                'alamat' => 'Jl. RSUD Kendari No. 7, Kendari',
                'latitude' => -3.985,
                'longitude' => 122.505,
                'kapasitas' => 4,
                'status' => 'Maintenance'
            ],
            [
                'nama' => 'TPS Terminal',
                'alamat' => 'Jl. Terminal Baruga No. 2, Kendari',
                'latitude' => -4.005,
                'longitude' => 122.51,
                'kapasitas' => 5,
                'status' => 'Aktif'
            ],
        ];

        foreach ($tpsList as $tps) {
            TPS::create($tps);
        }
    }
}
