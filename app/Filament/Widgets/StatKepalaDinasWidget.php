<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

use App\Models\DataTeknis;
use App\Models\User;

class StatKepalaDinasWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected  ?string $pollingInterval = '30s';

    /**
     * 🔵 FULL WIDTH
     */
    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 1,
            'xl' => 'full',
        ];
    }

    protected function getStats(): array
    {
        /**
         * 🔵 USER LOGIN
         */
        $user = auth()->user();

        /**
         * 🔵 FILTER DASHBOARD
         */
        $filters   = $this->filters ?? [];

        $startDate = $filters['startDate'] ?? null;
        $endDate   = $filters['endDate'] ?? null;
        $bidangId  = $filters['bidang_id'] ?? null;
        $uptId     = $filters['upt_id'] ?? null;
        $objekProduksiId = $filters['objek_produksi_id'] ?? null;

        /**
         * 🔥 AUTO ROLE SCOPE
         */
        if ($user?->hasRole('kepala_bidang')) {
            $bidangId = $user->bidang_id ?? $bidangId;
        }

        if ($user?->hasRole('upt')) {
            $uptId = $user->upt_id ?? $uptId;
        }

        /**
         * 🔵 BASE QUERY
         */
        // $baseQuery = DataTeknis::query();

        $baseQuery = DataTeknis::query()
            ->select(['id','upt_id','objek_produksi_id','kegiatan_id','tanggal']);



        if ($startDate) {
            $baseQuery->whereDate('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $baseQuery->whereDate('tanggal', '<=', $endDate);
        }

        // if ($bidangId) {
        //     $baseQuery->whereHas('upt', fn ($q) => $q->where('bidang_id', $bidangId));
        // }

        if ($bidangId) {
                $baseQuery->whereHas('kegiatan', function ($q) use ($bidangId) {
                    $q->where('bidang_id', $bidangId);
                });
            }


        if ($uptId) {
            $baseQuery->where('upt_id', $uptId);
        }

        if ($objekProduksiId) {
             $baseQuery->where('objek_produksi_id', $objekProduksiId);
}

        /**
         * 🟢 HITUNG DATA (CLONE QUERY BIAR AMAN)
         */
        $uptAktif = (clone $baseQuery)
            ->whereNotNull('upt_id')
            ->distinct('upt_id')
            ->count('upt_id');

        /**
         * 🔵 Total Aktivitas Produksi
         */
        $totalAktivitas = (clone $baseQuery)->count();

        /**
         * 🔵 Total User Sistem
         */
        $totalPengguna = User::query()->count();

        /**
         * 🔥 BIDANG TERAKTIF (DINAMIS - bukan hardcode)
         */
        $bidangTeraktif = (clone $baseQuery)
            ->with('kegiatan.bidang')
            ->get()
            ->filter(fn($row) => optional($row->kegiatan)->bidang_id)
            ->groupBy(fn($row) => optional($row->kegiatan->bidang)->nama ?? 'Tanpa Bidang')
            ->map(fn($items) => $items->count())
            ->sortDesc()
            ->keys()
            ->first() ?? '-';


        /**
         * 🔥 ROLE AWARE OUTPUT
         */
        $stats = [];

        /**
         * 🟣 ROLE UPT (OPERASIONAL)
         */
        if ($user?->hasRole('upt')) {

            $stats[] = Stat::make('Aktivitas UPT Anda', $totalAktivitas)
                ->description('Jumlah aktivitas produksi sesuai filter')
                ->icon('heroicon-o-building-office');

            return $stats;
        }

        /**
         * 🟡 ROLE KEPALA BIDANG (TAKTIS)
         */
        if ($user?->hasRole('kepala_bidang')) {

            $stats[] = Stat::make('UPT Aktif Bidang Anda', $uptAktif)
                ->description('UPT yang aktif pada bidang Anda')
                ->icon('heroicon-o-building-office');

            $stats[] = Stat::make('Total Aktivitas Produksi', $totalAktivitas)
                ->description('Jumlah laporan produksi bidang')
                ->icon('heroicon-o-clipboard-document-list');

            return $stats;
        }

        /**
         * 👑 ROLE STRATEGIS (KADIS / SUPER ADMIN / PERENCANAAN)
         */
        $stats[] = Stat::make('UPT Aktif Organisasi', $uptAktif)
            ->description('UPT yang menginput data sesuai filter')
            ->icon('heroicon-o-building-office')
            ->extraAttributes([
                'class' => 'ring-1 ring-amber-500/30 bg-slate-900/40 backdrop-blur-xl'
            ]);

        $stats[] = Stat::make('Total Pengguna', $totalPengguna)
            ->description('Jumlah pengguna sistem')
            ->icon('heroicon-o-users')
            ->extraAttributes([
                'class' => 'ring-1 ring-amber-500/30 bg-slate-900/40 backdrop-blur-xl'
            ]);

        $stats[] = Stat::make('Bidang Teraktif', $bidangTeraktif)
            ->description('Bidang dengan aktivitas tertinggi')
            ->icon('heroicon-o-chart-bar')
            ->extraAttributes([
                'class' => 'ring-1 ring-amber-500/30 bg-slate-900/40 backdrop-blur-xl'
            ]);

        return $stats;
    }
}
