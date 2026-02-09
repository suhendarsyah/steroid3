<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Desa extends Model
{

// use HasRoles;
protected $guarded = [];

    public function kecamatan()
        {
            return $this->belongsTo(Kecamatan::class);
        }
    public function pemiliks()
        {
            return $this->hasMany(Pemilik::class);
        }
}
