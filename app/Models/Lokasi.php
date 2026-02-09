<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $fillable = [
        'nama',
        'desa_id',
        'jenis',
        'alamat_detail',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // helper (opsional)
    public function kecamatan()
    {
        return $this->desa->kecamatan();
    }
}
