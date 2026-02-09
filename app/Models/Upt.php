<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upt extends Model
{
     protected $table = 'upts';

    // protected $fillable = [
    //     'nama',
    //     'jenis',
    // ];

    protected $guarded = [];

    public function unitLayanans()
        {
            return $this->hasMany(UnitLayanan::class);
        }

}
