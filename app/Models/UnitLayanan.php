<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitLayanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'upt_id',
        'nama',
        'keterangan',
    ];

    // unit layanan milik satu UPT
    public function upt()
    {
        return $this->belongsTo(Upt::class);
    }

    // (nanti) unit layanan punya banyak objek produksi
    public function objekProduksis()
    {
        return $this->hasMany(ObjekProduksi::class);
    }

    public function kecamatans()
    {
        return $this->belongsToMany(Kecamatan::class, 'unit_layanan_kecamatan');
    }

}
