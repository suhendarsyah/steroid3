<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Kecamatan extends Model
{

// use HasRoles;
    protected $guarded = [];
    public function desas()
    {
        return $this->hasMany(Desa::class);
    }
    public function unitLayanans()
    {
        return $this->belongsToMany(UnitLayanan::class, 'unit_layanan_kecamatan');
    }

}
