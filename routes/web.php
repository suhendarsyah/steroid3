<?php

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProduksiPerUptExport;
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


use Illuminate\Support\Facades\Route;
use App\Exports\DataTeknisMentahExport;

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
