<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rumah extends Model
{
    protected $table = 'rumah';

    protected $fillable = [
        'nomor_rumah',
        'status'
    ];

    public function penghuniRelasi()
    {
        return $this->hasMany(PenghuniRumah::class, 'rumah_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'rumah_id');
    }
}
