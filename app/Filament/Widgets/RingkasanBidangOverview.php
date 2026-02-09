<?php

namespace App\Filament\Widgets;

use App\Models\Target;
use App\Models\DataTeknis;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RingkasanBidangOverview extends StatsOverviewWidget
{
 
protected function getStats(): array
{
    $user  = auth()->user();
    $tahun = now()->year;

    if (! $user->bidang_id) {
        return [
            Stat::make('Target Bidang', 0),
            Stat::make('Realisasi', 0),
            Stat::make('Capaian', '0 %'),
        ];
    }

    // ===============================
    // TARGET (SUM DARI TABEL TARGET)
    // ===============================
    $targets = Target::query()
        ->where('master_bidang_id', $user->bidang_id)
        ->where('tahun', $tahun)
        ->get();

    $totalTarget = $targets->sum('target_jumlah');

    // ===============================
    // REALISASI (MATCH PER TARGET)
    // ===============================
    $totalRealisasi = 0;

    foreach ($targets as $target) {
        $totalRealisasi += DataTeknis::query()
            ->whereYear('tanggal', $target->tahun)
            ->where('kegiatan_id', $target->master_kegiatan_teknis_id)
            ->sum('nilai');
    }

    $persen = $totalTarget > 0
        ? round(($totalRealisasi / $totalTarget) * 100, 1)
        : 0;

    return [
        Stat::make('Target Bidang', number_format($totalTarget)),
        Stat::make('Realisasi', number_format($totalRealisasi)),
        Stat::make('Capaian', $persen . ' %'),
    ];
}



    public static function canView(): bool
    {
        return auth()->user()?->hasRole('kepala_bidang');
    }
}
