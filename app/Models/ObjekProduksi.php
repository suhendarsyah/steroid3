<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjekProduksi extends Model
{
    // protected $fillable = [
    //     'pemilik_id',
    //     'komoditas_id',
    //     'kode_identitas',
    //     'jumlah',
    // ];

    protected $guarded = [];

    public function pemilik()
    {
        return $this->belongsTo(Pemilik::class);
    }

    public function komoditas()
    {
        return $this->belongsTo(Komoditas::class);
    }

    // akses otomatis ke bidang (tanpa FK langsung)
    public function bidang()
    {
        return $this->komoditas->bidang();
    }

    // shortcut by-name-by-address
    public function desa()
    {
        return $this->pemilik->desa();
    }

    public function kecamatan()
    {
        return $this->pemilik->desa->kecamatan();
    }

    public function upt()
    {
        return $this->belongsTo(\App\Models\Upt::class);
    }

    public function dataTeknis()
    {
        return $this->hasMany(\App\Models\DataTeknis::class, 'objek_produksi_id');
    }

    public function unitLayanan()
        {
            return $this->belongsTo(UnitLayanan::class,'unit_layanan_id');
        }


    public function populasiSekarang($tahun = null): int
    {
        $tahun = $tahun ?? now()->year;

        $lahir = $this->dataTeknis()
            ->whereYear('tanggal', $tahun)
            ->whereHas('kegiatan', function ($q) {
                $q->where('tipe', 'lahir');
            })
            ->sum('nilai');

        $mati = $this->dataTeknis()
            ->whereYear('tanggal', $tahun)
            ->whereHas('kegiatan', function ($q) {
                $q->where('tipe', 'mati');
            })
            ->sum('nilai');

        $keluar = $this->dataTeknis()
            ->whereYear('tanggal', $tahun)
            ->whereHas('kegiatan', function ($q) {
                $q->where('tipe', 'keluar');
            })
            ->sum('nilai');

        return (int) $this->populasi_awal + $lahir - $mati - $keluar;
    }

}
