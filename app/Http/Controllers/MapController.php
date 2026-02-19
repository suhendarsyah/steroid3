<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function data(Request $request)
    {
        $kode = $request->query('kode');

        if (!$kode) {
            return response()->json([]);
        }

        $rows = DB::table('kecamatans')

            ->join('unit_layanan_kecamatan', 'unit_layanan_kecamatan.kecamatan_id', '=', 'kecamatans.id')

            ->join('unit_layanans', 'unit_layanans.id', '=', 'unit_layanan_kecamatan.unit_layanan_id')

            ->join('upts', 'upts.id', '=', 'unit_layanans.upt_id')

            ->leftJoin('objek_produksis', 'objek_produksis.unit_layanan_id', '=', 'unit_layanans.id')

            ->leftJoin('pemiliks', 'pemiliks.id', '=', 'objek_produksis.pemilik_id')

            ->leftJoin('komoditas', 'komoditas.id', '=', 'objek_produksis.komoditas_id')

            ->leftJoin('data_teknis', 'data_teknis.objek_produksi_id', '=', 'objek_produksis.id')

            ->where('kecamatans.id', $kode)

            ->select(
                'kecamatans.nama as kecamatan',
                'upts.nama as upt',
                'upts.jenis_upt',
                'unit_layanans.nama as unit_layanan',
                'objek_produksis.nama as objek',
                'pemiliks.nama as pemilik',
                'komoditas.nama as komoditas',
                'komoditas.satuan_default as satuan',
                DB::raw('SUM(data_teknis.nilai) as total')
            )

            ->groupBy(
                'kecamatans.nama',
                'upts.nama',
                'upts.jenis_upt',
                'unit_layanans.nama',
                'objek_produksis.nama',
                'pemiliks.nama',
                'komoditas.nama',
                'komoditas.satuan_default'
            )

            ->orderBy('upts.nama')
            ->get();

        return response()->json($rows);
    }
}
