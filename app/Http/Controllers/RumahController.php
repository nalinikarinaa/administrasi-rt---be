<?php

namespace App\Http\Controllers;

use App\Models\Rumah;
use Illuminate\Http\Request;

class RumahController extends Controller
{
    // 🔥 GET semua rumah
    public function index()
    {
        $data = Rumah::with('penghuniRelasi')->latest()->get();

        return response()->json($data);
    }

    // 🔥 POST tambah rumah
    public function store(Request $request)
    {
        $request->validate([
            'nomor_rumah' => 'required|string',
             'status' => 'in:terisi,kosong'
        ]);

        $data = Rumah::create([
            'nomor_rumah' => $request->nomor_rumah,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Rumah berhasil ditambahkan',
            'data' => $data
        ]);
    }

    // 🔥 GET detail rumah
    public function show($id)
    {
        $data = Rumah::with(['penghuniRelasi', 'pembayaran'])->findOrFail($id);

        return response()->json($data);
    }

    // 🔥 UPDATE rumah
    public function update(Request $request, $id)
    {
        $rumah = Rumah::findOrFail($id);

        $rumah->update([
            'nomor_rumah' => $request->nomor_rumah ?? $rumah->nomor_rumah,
            'status' => $request->status ?? $rumah->status,
        ]);

        return response()->json([
            'message' => 'Rumah berhasil diupdate',
            'data' => $rumah
        ]);
    }

    // 🔥 DELETE rumah
    public function destroy($id)
    {
        $rumah = Rumah::findOrFail($id);
        $rumah->delete();

        return response()->json([
            'message' => 'Rumah berhasil dihapus'
        ]);
    }
}
