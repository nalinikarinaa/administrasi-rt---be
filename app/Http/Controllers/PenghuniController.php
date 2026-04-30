<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Penghuni;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'ktp_photo' => [
                'nullable',
                Rule::unique('penghuni', 'ktp_photo'),
            ],
            'status' => 'required|in:kontrak,tetap',
            'phone' => 'required|string|max:15',
            'status_pernikahan' => ['required', Rule::in([0, 1])],
        ]);

        $data = Penghuni::create([
            ...$request->all(),
            'status_pernikahan' => (int) $request->status_pernikahan,
        ]);

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
        'ktp_photo' => [
            'nullable',
           Rule::unique('penghuni', 'ktp_photo')->ignore($id),
        ],
        'status' => 'required|in:kontrak,tetap',
       'phone' => 'required|string|max:15',
        'status_pernikahan' => ['required', Rule::in(['menikah', 'belum'])],
    ]);

    $data->update([
        'name' => $request->name,
        'ktp_photo' => $request->ktp_photo ?? $data->ktp_photo,
        'status' => $request->status,
        'phone' => $request->phone,
        'status_pernikahan' => $request->status_pernikahan,
    ]);

    return response()->json([
        'message' => 'Penghuni berhasil diupdate',
        'data' => $data
    ]);
}

    // DELETE
    public function destroy($id)
    {
        $data = Penghuni::findOrFail($id);
        $data->delete();

        return response()->json([
            'message' => 'Penghuni berhasil dihapus'
        ]);
    }

}
