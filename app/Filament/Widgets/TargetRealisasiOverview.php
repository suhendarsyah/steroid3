<?php

namespace App\Filament\Widgets;

use App\Models\Target;
use App\Models\DataTeknis;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TargetRealisasiOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $tahun = now()->year;

        // TOTAL TARGET
        $totalTarget = Target::where('tahun', $tahun)
            ->sum('target_jumlah');

        // TOTAL REALISASI (dari data teknis)
        $totalRealisasi = DataTeknis::whereYear('tanggal', $tahun)
            ->sum('nilai');

        $persen = $totalTarget > 0
            ? round(($totalRealisasi / $totalTarget) * 100, 1)
            : 0;

        return [
            Stat::make('Total Target ' . $tahun, number_format($totalTarget)),
            Stat::make('Total Realisasi', number_format($totalRealisasi)),
            Stat::make('Capaian', $persen . ' %'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('perencanaan');
    }
}
