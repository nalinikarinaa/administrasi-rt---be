<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penghuni;

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

}
