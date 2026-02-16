<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKegiatanTeknis extends Model
{
    protected $table = 'master_kegiatan_teknis';

    protected $guarded = [];

    public function dataTeknis()
    {
        return $this->hasMany(DataTeknis::class, 'kegiatan_id');
    }

    public function bidang()
        {
            return $this->belongsTo(Bidang::class, 'bidang_id');
        }

}
