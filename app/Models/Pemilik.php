<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pemilik extends Model

{
    use HasFactory;
   protected $guarded = [];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function objekProduksis()
    {
        return $this->hasMany(ObjekProduksi::class);
    }

    public function pemilik()
{
    return $this->belongsTo(\App\Models\Pemilik::class);
}
}
