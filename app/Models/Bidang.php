<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Bidang extends Model
{
    // use HasFactory;
    // use HasRoles;

    protected $table = 'master_bidang';

    protected $guarded = [];

    public function komoditas()
    {
        return $this->hasMany(Komoditas::class, 'master_bidang_id');
    }
}


