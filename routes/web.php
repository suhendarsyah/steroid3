<?php

use App\Exports\DataTeknisMentahExport;
use App\Exports\ProduksiPerUptExport;
use App\Http\Controllers\MapController;

use App\Services\DownloadDataService;


Route::get('/', function () {
    return view('welcome2');
});

// Route::get('/cek-upt-user', function () {
//     $user = auth()->user();

//     return [
//         'email' => $user->email,
//         'role' => $user->getRoleNames(),
//         'upt_id' => $user->upt_id,
//     ];
// });


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/test-export-service/{tahun}', function ($tahun) {
    return app(DownloadDataService::class)
        ->dataTeknisMentah((int) $tahun);
});

Route::get('/test-produksi-upt/{tahun}', function ($tahun) {
    return Excel::download(
        new ProduksiPerUptExport((int) $tahun),
        "produksi_per_upt_{$tahun}.xlsx"
    );
});


Route::get('/test-service-produksi-upt/{tahun}', function ($tahun) {
    return app(DownloadDataService::class)->produksiPerUpt((int) $tahun);
});

Route::get('/admin/map-data', [MapController::class, 'data']);

// Route::get('/admin/map-data', function (Request $request) {

//     $kode = $request->query('kode');

//     $rows = DB::table('kecamatans')

//         ->join('unit_layanan_kecamatan', 'unit_layanan_kecamatan.kecamatan_id', '=', 'kecamatans.id')

//         ->join('unit_layanans', 'unit_layanans.id', '=', 'unit_layanan_kecamatan.unit_layanan_id')

//         ->join('upts', 'upts.id', '=', 'unit_layanans.upt_id')

//         ->leftJoin('objek_produksis', 'objek_produksis.unit_layanan_id', '=', 'unit_layanans.id')

//         ->leftJoin('pemiliks', 'pemiliks.id', '=', 'objek_produksis.pemilik_id')

//         ->leftJoin('komoditas', 'komoditas.id', '=', 'objek_produksis.komoditas_id')

//         ->leftJoin('data_teknis', 'data_teknis.objek_produksi_id', '=', 'objek_produksis.id')

//         ->where('kecamatans.id', $kode)

//         ->select(
//             'kecamatans.nama as kecamatan',
//             'upts.nama as upt',
//             'upts.jenis_upt',
//             'unit_layanans.nama as unit_layanan',
//             'objek_produksis.nama as objek',
//             'pemiliks.nama as pemilik',
//             'komoditas.nama as komoditas',
//             DB::raw('SUM(data_teknis.nilai) as total')
//         )

//         ->groupBy(
//             'kecamatans.nama',
//             'upts.nama',
//             'upts.jenis_upt',
//             'unit_layanans.nama',
//             'objek_produksis.nama',
//             'pemiliks.nama',
//             'komoditas.nama'
//         )
//         ->get();

//     return response()->json($rows);
// });
