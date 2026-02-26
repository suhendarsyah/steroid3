<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function data(Request $request)
    {
        $kode  = $request->query('kode');
        $tahun = $request->query('tahun'); // 🔥 filter tahun

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
        | DATA WILAYAH (UPT WILAYAH + STATISTIK) + FILTER TAHUN
        |--------------------------------------------------------------------------
        */
        $wilayah = DB::table('kecamatans')
            ->join('unit_layanan_kecamatan','unit_layanan_kecamatan.kecamatan_id','=','kecamatans.id')
            ->join('unit_layanans','unit_layanans.id','=','unit_layanan_kecamatan.unit_layanan_id')
            ->join('upts','upts.id','=','unit_layanans.upt_id')

            ->leftJoin('objek_produksis','objek_produksis.unit_layanan_id','=','unit_layanans.id')
            ->leftJoin('pemiliks','pemiliks.id','=','objek_produksis.pemilik_id')

            // 🔥 join data_teknis untuk filter tahun (tanpa merusak relasi)
            // ->leftJoin('data_teknis','data_teknis.objek_produksi_id','=','objek_produksis.id')
            ->leftJoin('data_teknis', function($join) use ($tahun) {

                $join->on('data_teknis.objek_produksi_id','=','objek_produksis.id');

                if($tahun){
                    $join->whereYear('data_teknis.tanggal',$tahun);
                }

            })
                        ->where('kecamatans.id',$kecamatanId)
            ->where('upts.jenis_upt','wilayah')

            ->when($tahun, function($q) use ($tahun){
                $q->whereYear('data_teknis.tanggal', $tahun);
            })

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
        | POTENSI KOMODITAS (AGREGAT PER SATUAN) + FILTER TAHUN
        |--------------------------------------------------------------------------
        */
        $komoditas = DB::table('kecamatans')
            ->join('unit_layanan_kecamatan','unit_layanan_kecamatan.kecamatan_id','=','kecamatans.id')
            ->join('unit_layanans','unit_layanans.id','=','unit_layanan_kecamatan.unit_layanan_id')

            ->join('objek_produksis','objek_produksis.unit_layanan_id','=','unit_layanans.id')
            ->join('komoditas','komoditas.id','=','objek_produksis.komoditas_id')

            // ->leftJoin('data_teknis','data_teknis.objek_produksi_id','=','objek_produksis.id')
            ->leftJoin('data_teknis', function($join) use ($tahun){

                $join->on('data_teknis.objek_produksi_id','=','objek_produksis.id');

                if($tahun){
                    $join->whereYear('data_teknis.tanggal',$tahun);
                }

            })
            ->where('kecamatans.id',$kecamatanId)

            ->when($tahun, function($q) use ($tahun){
                $q->whereYear('data_teknis.tanggal', $tahun);
            })

            ->select(
                'komoditas.nama',
                'komoditas.satuan_default as satuan',
                DB::raw('SUM(data_teknis.nilai) as total')
            )
            ->groupBy('komoditas.nama','komoditas.satuan_default')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | UPT TEMATIK (JUMLAH OBJEK) + FILTER TAHUN
        |--------------------------------------------------------------------------
        */
        $tematik = DB::table('kecamatans')
            ->join('unit_layanan_kecamatan','unit_layanan_kecamatan.kecamatan_id','=','kecamatans.id')
            ->join('unit_layanans','unit_layanans.id','=','unit_layanan_kecamatan.unit_layanan_id')
            ->join('upts','upts.id','=','unit_layanans.upt_id')

            ->leftJoin('objek_produksis','objek_produksis.unit_layanan_id','=','unit_layanans.id')

            // 🔥 filter tahun tetap lewat data_teknis
            // ->leftJoin('data_teknis','data_teknis.objek_produksi_id','=','objek_produksis.id')
            ->leftJoin('data_teknis', function($join) use ($tahun){

                $join->on('data_teknis.objek_produksi_id','=','objek_produksis.id');

                if($tahun){
                    $join->whereYear('data_teknis.tanggal',$tahun);
                }

            })
            ->where('kecamatans.id',$kecamatanId)
            ->where('upts.jenis_upt','tematis')

            ->when($tahun, function($q) use ($tahun){
                $q->whereYear('data_teknis.tanggal', $tahun);
            })

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

    /*
    |--------------------------------------------------------------------------
    | LIST TAHUN DINAMIS UNTUK FILTER
    |--------------------------------------------------------------------------
    */
    public function tahunList()
    {
        $tahun = DB::table('data_teknis')
            ->whereNotNull('tanggal')
            ->selectRaw('YEAR(data_teknis.tanggal) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return response()->json($tahun);
    }

    public function foto(Request $request)
    {
        $nama = $request->query('kecamatan');

        if(!$nama){
            return response()->json([
                'url' => 'https://upload.wikimedia.org/wikipedia/commons/3/3e/Garut_Regency.jpg'
            ]);
        }

        $keyword = urlencode($nama.' Garut');

        $api = "https://commons.wikimedia.org/w/api.php?action=query&format=json&prop=pageimages&piprop=thumbnail&pithumbsize=600&generator=search&gsrsearch={$keyword}&gsrlimit=1";

        $response = @file_get_contents($api);

        if(!$response){
            return response()->json([
                'url' => 'https://upload.wikimedia.org/wikipedia/commons/3/3e/Garut_Regency.jpg'
            ]);
        }

        $json = json_decode($response,true);

        $pages = $json['query']['pages'] ?? null;

        if(!$pages){
            // 🔥 FALLBACK FOTO GARUT
            return response()->json([
                'url' => 'https://upload.wikimedia.org/wikipedia/commons/3/3e/Garut_Regency.jpg'
            ]);
        }

        $first = array_values($pages)[0];

        return response()->json([
            'url' => $first['thumbnail']['source']
                ?? 'https://upload.wikimedia.org/wikipedia/commons/3/3e/Garut_Regency.jpg'
        ]);
    }

    public function proxyFoto(Request $request)
{
    $url = $request->query('url');

    if(!$url){
        abort(404);
    }

    $image = @file_get_contents($url);

    if(!$image){
        abort(404);
    }

    return response($image)
        ->header('Content-Type','image/jpeg');
}
}