<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetHistory;

class AssetHistoryController extends Controller
{
    // GET /api/histories
    public function index()
    {
        $histories = AssetHistory::with('user', 'asset')->latest()->get();

        return response()->json([
            'message' => 'Histori semua aset',
            'data' => $histories
        ]);
    }

    // GET /api/assets/{id}/histories
    public function showByAsset($id)
    {
        $histories = AssetHistory::where('asset_id', $id)
            ->with('user', 'asset')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Histori untuk aset ID ' . $id,
            'data' => $histories
        ]);
    }

    // GET /api/histories/filter
    public function filter(Request $request)
    {
        $query = AssetHistory::with('user', 'asset');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $result = $query->latest()->get();

        return response()->json([
            'message' => 'Histori hasil filter',
            'data' => $result
        ]);
    }
}
