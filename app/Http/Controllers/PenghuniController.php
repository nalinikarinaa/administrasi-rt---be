<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Penghuni;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\PenghuniRumah;
use App\Models\Rumah;

class PenghuniController extends Controller
{
    //BUAT NAMPILIN RUMAH YG BERPENGHUNI DULU DAN SEKARANG
//     public function index()
// {
//     $data = \App\Models\Penghuni::with('rumahRelasi')->get();

//     return response()->json($data);
// }

//buat nampilin rumah yg berpenghuni sekarang aja
public function index()
{
    $data = Penghuni::with('rumahAktif.rumah')->get();

    return response()->json($data);
}

 // POST create
public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'ktp_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'status' => 'required|in:kontrak,tetap',
        'phone' => 'required|string|max:15',
        'status_pernikahan' => 'required|in:0,1',
        'rumah_id' => 'required|exists:rumah,id',
    ]);

    $ktpPath = null;

    if ($request->hasFile('ktp_photo')) {
        $ktpPath = $request->file('ktp_photo')->store('ktp', 'public');
    }

    $data = Penghuni::create([
        'name' => $request->name,
        'ktp_photo' => $ktpPath,
        'status' => $request->status,
        'phone' => $request->phone,
        'status_pernikahan' => (int) $request->status_pernikahan,
    ]);

PenghuniRumah::create([
    'penghuni_id' => $data->id,
    'rumah_id' => $request->rumah_id,
    'start_date' => now(),
    'end_date' => null,
]);

$rumah = Rumah::find($request->rumah_id);
$rumah->updateStatus();


    return response()->json([
        'message' => 'Penghuni berhasil ditambahkan',
        'data' => $data
    ]);
}

    // GET by id
    public function show($id)
    {
        $data = Penghuni::findOrFail($id);

        return response()->json($data);
    }

    // PUT update
 public function update(Request $request, $id)
{
    $data = Penghuni::findOrFail($id);

    $request->validate([
        'name' => 'required',
        'ktp_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'status' => 'required|in:kontrak,tetap',
        'phone' => 'required|string|max:15',
        'status_pernikahan' => 'required|in:0,1',
        'rumah_id' => 'required|exists:rumah,id',
    ]);

    // ===== update KTP =====
    $ktpPath = $data->ktp_photo;

    if ($request->hasFile('ktp_photo')) {
        $ktpPath = $request->file('ktp_photo')->store('ktp', 'public');
    }

    // ===== update data penghuni =====
    $data->update([
        'name' => $request->name,
        'ktp_photo' => $ktpPath,
        'status' => $request->status,
        'phone' => $request->phone,
        'status_pernikahan' => (int) $request->status_pernikahan,
    ]);

    // ===== ambil rumah aktif lama =====
    $activeRumah = PenghuniRumah::where('penghuni_id', $data->id)
        ->whereNull('end_date')
        ->first();

    $oldRumahId = null;

    // ===== tutup rumah lama =====
    if ($activeRumah) {
        $oldRumahId = $activeRumah->rumah_id;

        $activeRumah->update([
            'end_date' => now()
        ]);

        // 🔥 update status rumah lama
        $rumahLama = Rumah::find($oldRumahId);
        if ($rumahLama) {
            $rumahLama->updateStatus();
        }
    }

    // ===== buat relasi rumah baru =====
    PenghuniRumah::create([
        'penghuni_id' => $data->id,
        'rumah_id' => $request->rumah_id,
        'start_date' => now(),
        'end_date' => null,
    ]);

    // 🔥 update status rumah baru
    $rumahBaru = Rumah::find($request->rumah_id);
    if ($rumahBaru) {
        $rumahBaru->updateStatus();
    }

    return response()->json([
        'message' => 'Penghuni berhasil diupdate',
        'data' => $data
    ]);
}

    // DELETE
    public function destroy($id)
{
    $penghuni = Penghuni::findOrFail($id);

    // 1. ambil relasi aktif
    $activeRumah = \App\Models\PenghuniRumah::where('penghuni_id', $id)
        ->whereNull('end_date')
        ->first();

    if ($activeRumah) {

        // 2. tutup kontrak
        $activeRumah->update([
            'end_date' => now()
        ]);

        $rumahId = $activeRumah->rumah_id;

        // 3. cek apakah masih ada penghuni aktif di rumah itu
        $masihAda = \App\Models\PenghuniRumah::where('rumah_id', $rumahId)
            ->whereNull('end_date')
            ->exists();

        // 4. kalau tidak ada → kosongkan rumah
        if (!$masihAda) {
            $rumah = \App\Models\Rumah::find($rumahId);
            if ($rumah) {
                $rumah->status = 'kosong';
                $rumah->save();
            }
        }
    }

    // 5. hapus penghuni
    $penghuni->delete();

    return response()->json([
        'message' => 'Penghuni berhasil dihapus'
    ]);
}

public function historyPembayaran($id)
{
    $data = \App\Models\Pembayaran::with('rumah')
        ->where('penghuni_id', $id)
        ->orderBy('tahun', 'asc')
        ->orderBy('bulan', 'asc')
        ->get();

    return response()->json($data);
}

}
