<?php

namespace App\Models;

use App\Models\Upt;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;


class DataTeknis extends Model
{

    // use HasRoles;
    protected $table = 'data_teknis';

    // protected $fillable = [
    //     'objek_produksi_id',
    //     'kegiatan_id',
    //     'tanggal',
    //     'nilai',
    //     'keterangan',
    // ];
    protected $guarded = [];

    public function objekProduksi()
    {
        return $this->belongsTo(ObjekProduksi::class, 'objek_produksi_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(MasterKegiatanTeknis::class, 'kegiatan_id');
    }

    public function upt()
    {
        return $this->belongsTo(Upt::class);
    }

// ===== SCOPES (BEST PRACTICE) =====
    public function scopeForUpt(Builder $query, int $uptId): Builder
    {
        return $query->where('upt_id', $uptId);
    }
    
}
