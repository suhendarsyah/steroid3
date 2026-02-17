<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

use App\Models\DataTeknis;
use App\Models\ObjekProduksi;

class StatProduksiWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    /**
     * 🔵 Layout span (tidak full supaya tidak menabrak widget lain)
     */
    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 1,
            'xl' => 2,
        ];
    }

    protected function getStats(): array
    {
        $user    = auth()->user();
        // $filters = $this->filters ?? [];
        $filters = array_merge(
            $this->filters ?? [],
            request()->only(['startDate','endDate','upt_id','bidang_id'])
        );


        $startDate = $filters['startDate'] ?? null;
        $endDate   = $filters['endDate'] ?? null;
        $bidangId  = $filters['bidang_id'] ?? null;
        $uptId     = $filters['upt_id'] ?? null;


        /**
         * 🔥 AUTO ROLE SCOPE (AMAN)
         */
        // if ($user?->hasRole('kepala_bidang')) {
        //     $bidangId = $user->bidang_id ?? $bidangId;
        // }

        if ($user?->hasRole('kepala_bidang') && !$uptId) {
                $bidangId = $user->bidang_id ?? $bidangId;
            }

        if ($user?->hasRole('upt') && !$user?->hasRole('kepala_bidang')) {
                $uptId = $user->upt_id ?? $uptId;
            }

        /**
         * 🔵 BASE QUERY
         */
        $query = DataTeknis::query();

        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        // if ($bidangId) {
        //     $query->whereHas('upt', fn ($q) => $q->where('bidang_id', $bidangId));
        // }

        if ($bidangId) {
                $query->whereHas('kegiatan', function ($q) use ($bidangId) {
                    $q->where('bidang_id', $bidangId);
                });
            }


//         if ($bidangId) {
//             $query->whereHas('kegiatan', function ($q) use ($bidangId) {
//                 $q->where('bidang_id', $bidangId);
//             });
// }

        if ($uptId) {
            $query->where('upt_id', $uptId);
        }

        /**
         * 🔵 GROUP DATA PER KOMODITAS
         */
        // $komoditas = (clone $query)
        //     ->get()
        //     ->groupBy('objek_produksi_id')
        //     ->map(fn ($items) => $items->sum('nilai'))
        //     ->sortDesc();

        $topPerBidang = (clone $query)
            ->with(['kegiatan.bidang','objekProduksi.komoditas'])
            ->get()
            ->groupBy(fn ($row) => optional($row->kegiatan)->bidang_id)
            ->map(function ($items) {

                $bidangNama = optional(
                    optional($items->first()->kegiatan)->bidang
                )->nama ?? 'Bidang';

                $topObjek = $items
                    ->groupBy('objek_produksi_id')
                    ->map(function ($rows) {
                        return [
                            'total' => $rows->sum('nilai'),
                            'objek' => optional($rows->first()->objekProduksi),
                        ];
                    })
                    ->sortByDesc('total')
                    ->first();

                return [
                    'bidang' => $bidangNama,
                    'objek'  => $topObjek['objek'] ?? null,
                    'total'  => $topObjek['total'] ?? 0,
                ];
            });




            
        /**
         * 🔵 HITUNG GLOBAL (TIDAK HILANG)
         */
        // $totalProduksi = (clone $query)->sum('nilai');
        $totalProduksi = (clone $query)->count();


        $komoditasDominan = $topPerBidang
            ->map(fn ($row) => optional($row['objek'])->nama)
            ->filter()
            ->first() ?? '-';


        /**
         * 🔥 BUILD STATS (TANPA KONFLIK)
         */
        $stats = [];

        /**
         * 🟢 GLOBAL STATS
         */
        $stats[] = Stat::make('Total Laporan', number_format($totalProduksi))
            ->description('Akumulasi produksi sesuai filter')
            ->icon('heroicon-o-cube');

        $stats[] = Stat::make('Komoditas Terbanyak', $komoditasDominan)
            ->description('Komoditas dominan berdasarkan data')
            ->icon('heroicon-o-star');

        /**
         * 🔵 KOMODITAS DINAMIS
         */
        foreach ($topPerBidang as $item) {

            $namaBidang = $item['bidang'];

            $namaObjek = optional($item['objek'])->nama ?? 'Komoditas';

            $satuan = optional(
                optional($item['objek'])->komoditas
            )->satuan_default ?? '';

            $stats[] = Stat::make(
                $namaBidang,
                number_format($item['total']) . ' ' . $satuan
            )
            ->description('Komoditas dominan: ' . $namaObjek)
            ->icon('heroicon-o-chart-bar');
        }


        return $stats;
    }
}
