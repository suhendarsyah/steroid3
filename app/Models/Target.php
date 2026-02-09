<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = [
        'tahun',
        'master_bidang_id',
        'komoditas_id',
        'target_jumlah',
        'master_kegiatan_teknis_id',
    ];

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'master_bidang_id');
    }

    public function komoditas()
    {
        return $this->belongsTo(Komoditas::class);
    }
    public function kegiatan()
    {
        return $this->belongsTo(MasterKegiatanTeknis::class, 'master_kegiatan_teknis_id');
    }


    public function realisasiTahunIni(): float
{
    // return DataTeknis::query()
    //     ->join(
    //         'objek_produksis',
    //         'data_teknis.objek_produksi_id',
    //         '=',
    //         'objek_produksis.id'
    //     )
    //     ->where('objek_produksis.komoditas_id', $this->komoditas_id)
    //     ->where('data_teknis.kegiatan_id', $this->master_kegiatan_teknis_id)
    //     ->whereYear('data_teknis.tanggal', $this->tahun)
    //     ->sum('data_teknis.nilai');
    return DataTeknis::where('kegiatan_id', $this->master_kegiatan_teknis_id)
        ->whereYear('tanggal', $this->tahun)
        ->sum('nilai');
}
}
