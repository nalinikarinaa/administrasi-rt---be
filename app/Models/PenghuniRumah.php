<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenghuniRumah extends Model
{
    protected $table = 'penghuni_rumah';

    protected $fillable = [
        'rumah_id',
        'penghuni_id',
        'start_date',
        'end_date'
    ];

    public function rumah()
    {
        return $this->belongsTo(Rumah::class, 'rumah_id');
    }

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class, 'penghuni_id');
    }
}
