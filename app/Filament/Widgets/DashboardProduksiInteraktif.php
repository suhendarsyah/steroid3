<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Komoditas;

// class DashboardProduksiInteraktif extends Widget
// {
//     protected string $view = 'filament.widgets.dashboard-produksi-interaktif';

//     /** FILTER */
//     public ?int $tahun = null;
//     public ?int $komoditas = null;

//     public function mount(): void
//     {
//         $this->tahun = now()->year;
//     }

//     protected function getViewData(): array
//     {
//         $user = Auth::user();

//         /** QUERY DASAR */
//         $query = DB::table('data_teknis')
//             ->join('objek_produksis', 'data_teknis.objek_produksi_id', '=', 'objek_produksis.id')
//             ->join('komoditas', 'objek_produksis.komoditas_id', '=', 'komoditas.id')
//             ->whereYear('data_teknis.tanggal', $this->tahun);

//         /** FILTER KOMODITAS */
//         if ($this->komoditas) {
//             $query->where('komoditas.id', $this->komoditas);
//         }

//         /** FILTER ROLE */
//         if ($user?->hasRole('kabid')) {
//             $query->where('objek_produksis.master_bidang_id', $user->bidang_id);
//         }

//         if ($user?->hasRole('upt')) {
//             $query->where('objek_produksis.upt_id', $user->upt_id);
//         }

//         /** KPI */
//         $totalProduksi = (clone $query)->sum('data_teknis.nilai');
//         $rataRataBulanan = $totalProduksi / 12;

//         /** PRODUKSI PER UPT */
//         $produksiPerUpt = (clone $query)
//             ->selectRaw('objek_produksis.upt_id as upt, SUM(data_teknis.nilai) as total')
//             ->groupBy('objek_produksis.upt_id')
//             ->get();

//         return [
//             'daftarTahun' => DB::table('data_teknis')
//                 ->selectRaw('YEAR(tanggal) as tahun')
//                 ->distinct()
//                 ->orderByDesc('tahun')
//                 ->pluck('tahun'),

//             'daftarKomoditas' => Komoditas::orderBy('nama')->pluck('nama', 'id'),

//             'totalProduksi' => $totalProduksi,
//             'rataRataBulanan' => round($rataRataBulanan, 2),
//             'produksiPerUpt' => $produksiPerUpt,
//         ];
//     }
// }
