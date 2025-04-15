<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Asset;
use App\Models\AssetHistory;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    // GET /api/maintenances
    public function index()
    {
        $data = Maintenance::with('asset', 'user')->get();

        return response()->json([
            'message' => 'Daftar semua jadwal pemeliharaan',
            'data' => $data
        ]);
    }

    // GET /api/maintenances/{id}
    public function show($id)
    {
        $maintenance = Maintenance::with('asset', 'user')->find($id);

        return $maintenance
            ? response()->json(['data' => $maintenance])
            : response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    // POST /api/maintenances
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'jadwal' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);

        $validated['status'] = 'Dijadwalkan';
        $validated['lokasi'] = $asset->lokasi;

        $maintenance = Maintenance::create($validated);

        // Simpan histori aset
        if (auth()->check()) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'maintenance_created',
                'keterangan' => 'Jadwal pemeliharaan dibuat.'
            ]);
        }

        return response()->json([
            'message' => 'Jadwal pemeliharaan berhasil dibuat',
            'data' => $maintenance
        ], 201);
    }

    // PUT /api/maintenances/{id}
    public function update(Request $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'lokasi' => 'sometimes|required|string',
            'jadwal' => 'sometimes|required|date',
            'keterangan' => 'nullable|string',
            'status' => 'sometimes|required|string'
        ]);

        $maintenance->update(array_filter($validated));

        // Simpan histori aset
        if (auth()->check()) {
            AssetHistory::create([
                'asset_id' => $maintenance->asset_id,
                'user_id' => auth()->id(),
                'action' => 'maintenance_updated',
                'keterangan' => 'Jadwal pemeliharaan diperbarui.'
            ]);
        }

        return response()->json([
            'message' => 'Jadwal pemeliharaan berhasil diperbarui',
            'data' => $maintenance
        ]);
    }

    // PATCH /api/maintenances/{id}/complete
    public function updateResult(Request $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        $validated = $request->validate([
            'keterangan' => 'nullable|string',
            'status' => 'required|in:Selesai'
        ]);

        $maintenance->update([
            'status' => 'Selesai',
            'keterangan' => $validated['keterangan'] ?? $maintenance->keterangan
        ]);

        // Simpan histori aset
        if (auth()->check()) {
            AssetHistory::create([
                'asset_id' => $maintenance->asset_id,
                'user_id' => auth()->id(),
                'action' => 'maintenance_completed',
                'keterangan' => 'Pemeliharaan aset telah selesai.'
            ]);
        }

        return response()->json([
            'message' => 'Pemeliharaan berhasil diselesaikan',
            'data' => $maintenance
        ]);
    }

    // DELETE /api/maintenances/{id}
    public function destroy($id)
    {
        $maintenance = Maintenance::find($id);

        if (!$maintenance) {
            return response()->json(['message' => 'Data pemeliharaan tidak ditemukan'], 404);
        }

        $maintenance->delete();

        // Simpan histori aset
        if (auth()->check()) {
            AssetHistory::create([
                'asset_id' => $maintenance->asset_id,
                'user_id' => auth()->id(),
                'action' => 'maintenance_deleted',
                'keterangan' => 'Jadwal pemeliharaan dihapus.'
            ]);
        }

        return response()->json(['message' => 'Jadwal pemeliharaan berhasil dihapus']);
    }
}
