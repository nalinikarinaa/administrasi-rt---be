<?php

namespace App\Http\Controllers;

use App\Models\Rumah;
use Illuminate\Http\Request;

class RumahController extends Controller
{
    //get semua rumah
    public function index()
    {
        // $data = Rumah::with('penghuniRelasi')->latest()->get();
       $data = Rumah::with('penghuniRelasi.penghuni')->latest()->get();

        return response()->json($data);
    }

    // tambah rumah
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

    // detail rumah
    public function show($id)
    {
        $data = Rumah::with(['penghuniRelasi', 'pembayaran'])->findOrFail($id);

        return response()->json($data);
    }

    // update rumah
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

    // hapus
    public function destroy($id)
    {
        $rumah = Rumah::findOrFail($id);
        $rumah->delete();

        return response()->json([
            'message' => 'Rumah berhasil dihapus'
        ]);
    }
}
