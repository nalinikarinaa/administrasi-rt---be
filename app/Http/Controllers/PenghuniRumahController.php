<?php

namespace App\Http\Controllers;
use App\Models\PenghuniRumah;

use Illuminate\Http\Request;

class PenghuniRumahController extends Controller
{
    public function penghuni(Request $request)
{

    $request->validate([
        'rumah_id' => 'required|exists:rumah,id',
        'penghuni_id' => 'required|exists:penghuni,id'
    ]);

    PenghuniRumah::where('penghuni_id', $request->penghuni_id)
        ->whereNull('end_date')
        ->update([
            'end_date' => now()
        ]);

    $data = PenghuniRumah::create([
        'rumah_id' => $request->rumah_id,
        'penghuni_id' => $request->penghuni_id,
        'start_date' => now(),
        'end_date' => null
    ]);

    return response()->json([
        'message' => 'Penghuni berhasil ditempatkan',
        'data' => $data
    ]);
}
}
