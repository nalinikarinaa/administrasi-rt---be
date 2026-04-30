<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    protected $table = 'penghuni';

    protected $fillable = [
        'nama',
        'ktp',
        'status',
        'no_hp',
        'status_pernikahan'
    ];

    public function rumahRelasi()
    {
        return $this->hasMany(PenghuniRumah::class, 'penghuni_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'penghuni_id');
    }

//buat nampilin rumah yg berpenghuni aja
        public function rumahAktif()
    {
        return $this->hasOne(PenghuniRumah::class, 'penghuni_id')
                    ->whereNull('end_date');
    }
}
