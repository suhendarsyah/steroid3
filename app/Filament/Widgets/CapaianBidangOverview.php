<?php

namespace App\Filament\Widgets;

use App\Models\Target;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CapaianBidangOverview extends StatsOverviewWidget
{
    protected function getHeading(): ?string
    {
        return 'Capaian Kinerja BIdang (Tahun Berjalan)';
    }

    protected function getStats(): array
    {
        // Ambil semua target beserta relasi bidang
        $targetsPerBidang = Target::with('bidang')
            ->get()
            ->groupBy('master_bidang_id');

        $stats = [];

        foreach ($targetsPerBidang as $bidangId => $items) {
            // total target per bidang
            $totalTarget = $items->sum('target_jumlah');

            // total realisasi tahun ini (dari method di model Target)
            $totalRealisasi = $items->sum(function ($target) {
                return $target->realisasiTahunIni();
            });

            // persen capaian
            $persen = $totalTarget > 0
                ? ($totalRealisasi / $totalTarget) * 100
                : 0;

            // nama bidang
            $namaBidang = optional($items->first()->bidang)->nama ?? 'Bidang';

            // warna indikator
            if ($persen >= 90) {
                $color = 'success';
            } elseif ($persen >= 60) {
                $color = 'warning';
            } else {
                $color = 'danger';
            }

            $stats[] = Stat::make(
                $namaBidang,
                number_format($persen, 1) . ' %'
            )
                ->description('Realisasi vs Target Tahun Berjalan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($color);

            $stats = collect($stats)
                ->sortBy(fn ($stat) => $stat->getColor() === 'danger' ? 0 : 1)
                ->values()
                ->all();

        }

        return $stats;
    }


    public static function canView(): bool
{
    return auth()->user()?->hasAnyRole([
        'super_admin',
        'kepala_dinas',
    ]);
}
}
