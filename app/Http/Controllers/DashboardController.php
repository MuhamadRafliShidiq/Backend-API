<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\Maintenance;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary()
    {
        // Total data
        $totalAssets = Asset::count();
        $totalUsers = User::count();
        $totalMaintenance = Maintenance::count();

        // Total aset aktif (kosong atau mengandung 'aktif')
        $aktif = Asset::where(function ($query) {
            $query->whereNull('keterangan')
                  ->orWhere('keterangan', 'like', '%aktif%');
        })->count();

        // Total aset rusak (mengandung 'rusak')
        $rusak = Asset::where('keterangan', 'like', '%rusak%')->count();

        // Statistik kategori aset
        $assetsByCategory = Asset::select('kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('kategori')
            ->get();

        // Statistik lokasi aset
        $assetsByLocation = Asset::select('lokasi', DB::raw('COUNT(*) as total'))
            ->groupBy('lokasi')
            ->get();

        // Statistik maintenance by status
        $maintenanceByStatus = Maintenance::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'message' => 'Summary dashboard berhasil diambil',
            'data' => [
                'total_aset' => $totalAssets,
                'total_users' => $totalUsers,
                'total_maintenance' => $totalMaintenance,
                'aset_aktif' => $aktif,
                'aset_rusak' => $rusak,
                'assets_by_category' => $assetsByCategory,
                'assets_by_location' => $assetsByLocation,
                'maintenance_by_status' => $maintenanceByStatus,
            ]
        ]);
    }
}
