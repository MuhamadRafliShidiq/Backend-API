<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\User;
use App\Models\Maintenance;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Ambil beberapa asset dan user dari database
         $assets = Asset::all();
         $users = User::all();
 
         // Pastikan ada data asset dan user
         if ($assets->isEmpty() || $users->isEmpty()) {
             $this->command->info('Pastikan ada data asset dan user sebelum menjalankan seeder ini.');
             return;
         }
 
         // Menambahkan data dummy ke tabel maintenances
         foreach ($assets as $asset) {
             foreach ($users as $user) {
                 Maintenance::create([
                     'asset_id' => $asset->id,
                     'user_id' => $user->id,
                     'lokasi' => $asset->lokasi, // Asumsikan lokasi diambil dari asset
                     'jadwal' => now()->addDays(rand(1, 30)), // Jadwal acak dalam 1-30 hari ke depan
                     'keterangan' => 'Pemeliharaan rutin untuk ' . $asset->nama, // Asumsikan ada kolom nama di asset
                     'status' => 'Dijadwalkan',
                 ]);
             }
         }
    }
}
