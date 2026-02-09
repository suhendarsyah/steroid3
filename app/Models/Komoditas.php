<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Komoditas extends Model
{
    // protected $fillable = [
    //     'kode',
    //     'nama',
    //     'jenis',
    //     'satuan_default',
    //     'is_individual',
    //     'deskripsi',
    //     'is_active',
    //     'master_bidang_id',
    // ];

    protected $guarded = [];

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'master_bidang_id');
    }

    public function objekProduksis()
    {
        return $this->hasMany(ObjekProduksi::class);
    }

    public function komoditas()
{
    return $this->belongsTo(\App\Models\Komoditas::class);
}
}
