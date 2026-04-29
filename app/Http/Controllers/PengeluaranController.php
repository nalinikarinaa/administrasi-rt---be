<?php

namespace App\Http\Controllers;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;

use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
   public function summary()
{
    $data = [];

    for ($i = 1; $i <= 12; $i++) {
        $pemasukan = Pembayaran::where('bulan', $i)
            ->sum('total');

        $pengeluaran = Pengeluaran::whereMonth('date', $i)
            ->sum('jumlah');

        $data[] = [
            'bulan' => $i,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'saldo' => $pemasukan - $pengeluaran
        ];
    }

    return response()->json($data);
}

public function store(Request $request)
{
    $request->validate([
        'judul' => 'required|string|max:255',
        'jumlah' => 'required|integer|min:0',
        'date' => 'required|date',
        'deskripsi' => 'nullable|string'
    ]);

    $data = Pengeluaran::create([
        'judul' => $request->judul,
        'jumlah' => $request->jumlah,
        'date' => $request->date,
        'deskripsi' => $request->deskripsi
    ]);

    return response()->json([
        'message' => 'Pengeluaran berhasil ditambahkan',
        'data' => $data
    ], 201);
}

public function detail(Request $request)
{
    $request->validate([
        'bulan' => 'required|integer',
        'tahun' => 'required|integer',
    ]);

    // 🔹 ambil pemasukan
    $pemasukan = Pembayaran::with(['penghuni', 'rumah'])
        ->where('bulan', $request->bulan)
        ->where('tahun', $request->tahun)
        ->get();

    // 🔹 ambil pengeluaran
    $pengeluaran = Pengeluaran::whereMonth('date', $request->bulan)
        ->whereYear('date', $request->tahun)
        ->get();

    // 🔹 total
    $totalPemasukan = $pemasukan->sum('total');
    $totalPengeluaran = $pengeluaran->sum('jumlah');

    return response()->json([
        'bulan' => $request->bulan,
        'tahun' => $request->tahun,
        'pemasukan' => $pemasukan,
        'pengeluaran' => $pengeluaran,
        'total_pemasukan' => $totalPemasukan,
        'total_pengeluaran' => $totalPengeluaran,
        'saldo' => $totalPemasukan - $totalPengeluaran
    ]);
}

public function grafik()
{
    $data = [];

    for ($i = 1; $i <= 12; $i++) {
        $pemasukan = Pembayaran::where('bulan', $i)->sum('total');
        $pengeluaran = Pengeluaran::whereMonth('date', $i)->sum('jumlah');

        $data[] = [
            'bulan' => $i,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran
        ];
    }

    return response()->json($data);
}
}
