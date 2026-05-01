<?php

namespace App\Http\Controllers;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
//    public function store(Request $request)
// {
//     $request->validate([
//         'rumah_id' => 'required|exists:rumah,id',
//         'penghuni_id' => 'required|exists:penghuni,id',
//         'bulan' => 'required|integer|min:1|max:12',
//         'tahun' => 'required|integer',
//         'jenis_pembayaran' => 'required|in:bulanan,tahunan',
//         'status' => 'required|in:lunas,belum_bayar',
//     ]);

//     $uang_security = 100000;
//     $uang_kebersihan = 15000;

//     // kalau tahunan → kebersihan x12
//     if ($request->jenis_pembayaran == 'tahunan') {
//         $uang_kebersihan = 15000 * 12;
//         $uang_security = 100000 *12;
//     }

//     $total = $uang_security + $uang_kebersihan;

//     $data = Pembayaran::create([
//         'rumah_id' => $request->rumah_id,
//         'penghuni_id' => $request->penghuni_id,
//         'bulan' => $request->bulan,
//         'tahun' => $request->tahun,
//         'uang_security' => $uang_security,
//         'uang_kebersihan' => $uang_kebersihan,
//         'total' => $total,
//         'status' => $request->status,
//         'jenis_pembayaran' => $request->jenis_pembayaran,
//         'paid_at' => $request->status == 'lunas' ? now() : null
//     ]);

//     return response()->json([
//         'message' => 'Pembayaran berhasil',
//         'data' => $data
//     ]);
// }

public function store(Request $request)
{
    $request->validate([
        'rumah_id' => 'required|exists:rumah,id',
        'penghuni_id' => 'required|exists:penghuni,id',
        'bulan' => 'required|integer|min:1|max:12',
        'tahun' => 'required|integer',
        'jenis_pembayaran' => 'required|in:bulanan,tahunan',
        'status' => 'required|in:lunas,belum_bayar',
    ]);

    $uang_security = 100000;
    $uang_kebersihan = 15000;
    $total = $uang_security + $uang_kebersihan;

    // =========================
    // 🔥 PEMBAYARAN TAHUNAN
    // =========================
    if ($request->jenis_pembayaran == 'tahunan') {

        for ($i = 0; $i < 12; $i++) {

            $bulan = $request->bulan + $i;
            $tahun = $request->tahun;

            // handle kalau lewat Desember
            if ($bulan > 12) {
                $bulan -= 12;
                $tahun += 1;
            }

            // ❗ CEK BIAR TIDAK DUPLIKAT
            $exists = Pembayaran::where('rumah_id', $request->rumah_id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->exists();

            if (!$exists) {
                Pembayaran::create([
                    'rumah_id' => $request->rumah_id,
                    'penghuni_id' => $request->penghuni_id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'uang_security' => $uang_security,
                    'uang_kebersihan' => $uang_kebersihan,
                    'total' => $total,
                    'status' => 'lunas', // otomatis lunas
                    'jenis_pembayaran' => 'tahunan',
                    'paid_at' => now()
                ]);
            }
        }

        return response()->json([
            'message' => 'Pembayaran tahunan berhasil (12 bulan)',
        ]);
    }

    // =========================
    // 🔹 PEMBAYARAN BULANAN
    // =========================

    // ❗ CEK DUPLIKAT
    $exists = Pembayaran::where('rumah_id', $request->rumah_id)
        ->where('bulan', $request->bulan)
        ->where('tahun', $request->tahun)
        ->exists();

    if ($exists) {
        return response()->json([
            'message' => 'Data pembayaran bulan ini sudah ada'
        ], 400);
    }

    $data = Pembayaran::create([
        'rumah_id' => $request->rumah_id,
        'penghuni_id' => $request->penghuni_id,
        'bulan' => $request->bulan,
        'tahun' => $request->tahun,
        'uang_security' => $uang_security,
        'uang_kebersihan' => $uang_kebersihan,
        'total' => $total,
        'status' => $request->status,
        'jenis_pembayaran' => 'bulanan',
        'paid_at' => $request->status == 'lunas' ? now() : null
    ]);

    return response()->json([
        'message' => 'Pembayaran bulanan berhasil',
        'data' => $data
    ]);
}

public function detail(Request $request)
{
    $data = Pembayaran::with(['penghuni', 'rumah'])
        ->where('bulan', $request->bulan)
        ->where('tahun', $request->tahun)
        ->get();

    return response()->json([
        'data' => $data,
        'total' => $data->sum('total')
    ]);
}

public function summary()
{
    $data = Pembayaran::selectRaw('
            bulan,
            tahun,
            SUM(total) as pemasukan
        ')
        ->where('status', 'lunas')
        ->groupBy('bulan', 'tahun')
        ->orderBy('tahun')
        ->orderBy('bulan')
        ->get();

    return response()->json($data);
}

public function history()
{
    return Pembayaran::with(['penghuni', 'rumah'])
        ->orderBy('tahun')
        ->orderBy('bulan')
        ->get();
}

}
