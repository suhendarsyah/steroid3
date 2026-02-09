<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DataQueryService
{
    /**
     * =====================================================
     * PRODUKSI PER KECAMATAN (FORMAT BPS)
     * =====================================================
     * OUTPUT:
     * - kecamatan
     * - komoditas
     * - total_produksi
     *
     * RETURN:
     * - Illuminate\Database\Query\Builder
     *
     * CATATAN DESAIN:
     * - Unit Layanan OPSIONAL
     * - Kecamatan dari pivot unit_layanan_kecamatan
     * - Alias TIDAK dipakai di where/groupBy
     * =====================================================
     */
    public static function produksiPerKecamatan(int $tahun)
    {
        $user = Auth::user();

        $query = DB::table('data_teknis as dt')
            ->join('objek_produksis as op', 'op.id', '=', 'dt.objek_produksi_id')

            // unit layanan OPSIONAL
            ->leftJoin('unit_layanans as ul', 'ul.id', '=', 'op.unit_layanan_id')
            ->leftJoin('unit_layanan_kecamatan as ulk', 'ulk.unit_layanan_id', '=', 'ul.id')
            ->leftJoin('kecamatans as kc', 'kc.id', '=', 'ulk.kecamatan_id')

            // komoditas WAJIB
            ->join('komoditas as k', 'k.id', '=', 'op.komoditas_id')

            ->whereYear('dt.tanggal', $tahun)

            // hanya data yang punya kecamatan valid
            ->whereNotNull('kc.id')

            ->select(
                'kc.id as kecamatan_id',
                'kc.nama as kecamatan',
                'k.id as komoditas_id',
                'k.nama as komoditas',
                DB::raw('SUM(dt.nilai) as total_produksi')
            )

            ->groupBy(
                'kc.id',
                'kc.nama',
                'k.id',
                'k.nama'
            )

            ->orderBy('kc.nama')
            ->orderBy('k.nama');

        /**
         * =============================================
         * FILTER DATA BERDASARKAN ROLE
         * =============================================
         */
        if ($user) {

            // 🔒 UPT → hanya datanya sendiri
            if ($user->hasRole('upt')) {
                $query->where('op.upt_id', $user->upt_id);
            }

            // 🔒 Kepala Bidang → seluruh UPT di bidangnya
            if ($user->hasRole('kepala_bidang')) {
                $query->join('upts as u', 'u.id', '=', 'op.upt_id')
                    ->where('u.bidang_id', $user->bidang_id);
            }

            // 🔓 Kadis / Perencanaan / Super Admin → tanpa filter
        }

        return $query;
    }


    /**
     * =====================================================
     * POPULASI TERNAK PER WILAYAH (UPT)
     * =====================================================
     * Sumber data:
     * - objek_produksis.populasi_awal
     * - dikelompokkan per UPT & Komoditas
     *
     * Catatan:
     * - Ini BUKAN data teknis (bukan produksi)
     * - Ini snapshot populasi
     * =====================================================
     */
    public static function populasiTernakPerWilayah()
    {
        $user = Auth::user();

        $query = DB::table('objek_produksis as op')
            ->join('upts as u', 'u.id', '=', 'op.upt_id')
            ->join('komoditas as k', 'k.id', '=', 'op.komoditas_id')

            ->select(
                'u.nama as wilayah',
                'k.nama as komoditas',
                DB::raw('SUM(op.populasi_awal) as populasi')
            )

            ->groupBy(
                'u.id',
                'u.nama',
                'k.id',
                'k.nama'
            )

            ->orderBy('u.nama')
            ->orderBy('k.nama');

        /**
         * ===============================
         * FILTER BERDASARKAN ROLE
         * ===============================
         */
        if ($user) {

            // 🔒 UPT → hanya wilayahnya sendiri
            if ($user->hasRole('upt')) {
                $query->where('u.id', $user->upt_id);
            }

            // 🔒 Kepala Bidang → seluruh UPT di bidangnya
            if ($user->hasRole('kepala_bidang')) {
                $query->where('u.bidang_id', $user->bidang_id);
            }

            // 🔓 Kadis / Perencanaan / Super Admin → tanpa filter
        }

        return $query;
    }
}
