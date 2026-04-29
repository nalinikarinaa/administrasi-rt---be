<?php

namespace App\Http\Controllers;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function store(Request $request)
{
  
    $request->validate([
        'rumah_id' => 'required|exists:rumah,id',
        'penghuni_id' => 'required|exists:penghuni,id',
        'bulan' => 'required|integer',
        'tahun' => 'required|integer',
    ]);

    $data = Pembayaran::create([
        'rumah_id' => $request->rumah_id,
        'penghuni_id' => $request->penghuni_id,
        'bulan' => $request->bulan,
        'tahun' => $request->tahun,
        'uang_security' => 100000,
        'uang_kebersihan' => 15000,
        'total' => 115000,
        'status' => 'lunas',
        'jenis_pembayaran' => 'bulanan',
        'paid_at' => now()
    ]);

    return response()->json([
        'message' => 'Pembayaran berhasil',
        'data' => $data
    ]);
}
}
