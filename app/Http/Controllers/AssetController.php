<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    // GET /api/assets
    public function index()
    {
        $assets = Asset::all();
        return response()->json([
            'message' => 'Berhasil menampilkan Tabel Asset',
            'data' => $assets
        ], 200);
    }

    // GET /api/assets/{id}
    public function show($id)
    {
        $assets = Asset::find($id);
        return $assets
            ? response()->json($assets)
            : response()->json(['message' => 'Aset tidak ditemukan'], 404);
    }

    // POST /api/assets
    public function store(Request $request)
    {
        $request->validate([
            'nama_aset' => 'required|string',
            'kode_aset' => 'required|string|unique:assets',
            'kategori' => 'required|string',
            'lokasi' => 'required|string',
            'vendor' => 'required|string',
            'purchase_date' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $asset = Asset::create($request->all());

            if (auth()->check()) {
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'user_id' => auth()->id(),
                    'action' => 'created',
                    'keterangan' => 'Aset baru ditambahkan'
                ]);
            }

            return response()->json([
                'message' => 'Aset berhasil ditambahkan',
                'data' => $asset
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan Aset: ' . $e->getMessage()], 500);
        }
    }

    // PUT /api/assets/{id}
public function update(Request $request, $id)
{
    $request->validate([
        'nama_aset' => 'sometimes|required|string',
        'kode_aset' => 'sometimes|required|string|unique:assets,kode_aset,' . $id,
        'kategori' => 'sometimes|required|string',
        'lokasi' => 'sometimes|required|string',
        'vendor' => 'sometimes|required|string',
        'purchase_date' => 'sometimes|required|date',
        'keterangan' => 'nullable|string',
    ]);

    $asset = Asset::find($id);

    if (!$asset) {
        return response()->json(['message' => 'Aset tidak ditemukan'], 404);
    }

    try {
        $asset->update($request->all());

        if (auth()->check()) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'updated',
                'keterangan' => 'Aset telah diperbarui'
            ]);
        }

        return response()->json([
            'message' => 'Aset berhasil diperbarui',
            'data' => $asset
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Gagal memperbarui aset: ' . $e->getMessage()], 500);
    }
}

// DELETE /api/assets/{id}
public function destroy($id)
{
    $asset = Asset::find($id);

    if (!$asset) {
        return response()->json(['message' => 'Aset tidak ditemukan'], 404);
    }

    try {
        $asset->delete();

        if (auth()->check()) {
            AssetHistory::create([
                'asset_id' => $id,
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'keterangan' => 'Aset telah dihapus'
            ]);
        }

        return response()->json(['message' => 'Aset berhasil dihapus']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Gagal menghapus aset: ' . $e->getMessage()], 500);
    }
}

    // Filter Search
    public function search(Request $request)
    {
        $query = Asset::query();

        if ($request->filled('nama_aset')) {
            $query->where('nama_aset', 'like', '%' . $request->nama_aset . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('purchase_date', [$request->start_date, $request->end_date]);
        }

        $result = $query->get();
        return response()->json($result);
    }

    // Fitur List
    public function list()
    {
        $assets = Asset::select('id', 'nama_aset')->get();

        return response()->json([
            'message' => 'Daftar aset (id dan nama)',
            'data' => $assets
        ]);
    }

    // Statistik berdasarkan lokasi
    public function assetsByLocation()
    {
        $data = Asset::select('lokasi')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('lokasi')
            ->get();

        return response()->json([
            'message' => 'Statistik aset berdasarkan lokasi',
            'data' => $data
        ]);
    }

    // Statistik berdasarkan kategori
    public function assetsByCategory()
    {
        $data = Asset::select('kategori')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('kategori')
            ->get();

        return response()->json([
            'message' => 'Statistik aset berdasarkan kategori',
            'data' => $data
        ]);
    }

    // Statistik berdasarkan tahun pembelian
    public function assetsByYear()
    {
        $data = Asset::selectRaw('YEAR(purchase_date) as tahun, COUNT(*) as total')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        return response()->json([
            'message' => 'Statistik aset berdasarkan tahun',
            'data' => $data
        ]);
    }

}
