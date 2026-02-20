<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function data(Request $request)
    {
        $kode = $request->query('kode');

        /*
        |--------------------------------------------------------------------------
        | VALIDASI AWAL
        |--------------------------------------------------------------------------
        */
        if (!$kode) {
            return response()->json([
                'wilayah'   => null,
                'komoditas' => [],
                'tematik'   => [],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | MAPPING NAMA KECAMATAN → ID DATABASE
        |--------------------------------------------------------------------------
        | Ini solusi aman supaya GeoJSON tidak konflik lagi
        */
        $kecamatanId = DB::table('kecamatans')
            ->where('nama', $kode)
            ->value('id');

        if (!$kecamatanId) {
            return response()->json([
                'wilayah'   => null,
                'komoditas' => [],
                'tematik'   => [],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | DATA WILAYAH (UPT WILAYAH + STATISTIK)
        |--------------------------------------------------------------------------
        */
        $wilayah = DB::table('kecamatans')
            ->join('unit_layanan_kecamatan','unit_layanan_kecamatan.kecamatan_id','=','kecamatans.id')
            ->join('unit_layanans','unit_layanans.id','=','unit_layanan_kecamatan.unit_layanan_id')
            ->join('upts','upts.id','=','unit_layanans.upt_id')

            ->leftJoin('objek_produksis','objek_produksis.unit_layanan_id','=','unit_layanans.id')
            ->leftJoin('pemiliks','pemiliks.id','=','objek_produksis.pemilik_id')

            ->where('kecamatans.id',$kecamatanId)
            ->where('upts.jenis_upt','wilayah')

            ->select(
                'kecamatans.nama as kecamatan',
                'upts.nama as upt_wilayah',
                DB::raw('COUNT(DISTINCT objek_produksis.id) as total_objek'),
                DB::raw('COUNT(DISTINCT pemiliks.id) as total_pemilik')
            )
            ->groupBy('kecamatans.nama','upts.nama')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | POTENSI KOMODITAS (AGREGAT PER SATUAN)
        |--------------------------------------------------------------------------
        */
        $komoditas = DB::table('kecamatans')
            ->join('unit_layanan_kecamatan','unit_layanan_kecamatan.kecamatan_id','=','kecamatans.id')
            ->join('unit_layanans','unit_layanans.id','=','unit_layanan_kecamatan.unit_layanan_id')

            ->join('objek_produksis','objek_produksis.unit_layanan_id','=','unit_layanans.id')
            ->join('komoditas','komoditas.id','=','objek_produksis.komoditas_id')

            ->leftJoin('data_teknis','data_teknis.objek_produksi_id','=','objek_produksis.id')

            ->where('kecamatans.id',$kecamatanId)

            ->select(
                'komoditas.nama',
                'komoditas.satuan_default as satuan',
                DB::raw('SUM(data_teknis.nilai) as total')
            )
            ->groupBy('komoditas.nama','komoditas.satuan_default')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | UPT TEMATIK (JUMLAH OBJEK)
        |--------------------------------------------------------------------------
        */
        $tematik = DB::table('kecamatans')
            ->join('unit_layanan_kecamatan','unit_layanan_kecamatan.kecamatan_id','=','kecamatans.id')
            ->join('unit_layanans','unit_layanans.id','=','unit_layanan_kecamatan.unit_layanan_id')
            ->join('upts','upts.id','=','unit_layanans.upt_id')

            ->leftJoin('objek_produksis','objek_produksis.unit_layanan_id','=','unit_layanans.id')

            ->where('kecamatans.id',$kecamatanId)
            ->where('upts.jenis_upt','tematis')

            ->select(
                'upts.nama as upt',
                DB::raw('COUNT(DISTINCT objek_produksis.id) as jumlah_objek')
            )
            ->groupBy('upts.nama')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RESPONSE FINAL
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'wilayah'   => $wilayah,
            'komoditas' => $komoditas,
            'tematik'   => $tematik,
        ]);
    }
}