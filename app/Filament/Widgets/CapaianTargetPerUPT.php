<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class CapaianTargetPerUPT extends StatsOverviewWidget
{
    protected ?string $heading = 'Capaian Realisasi Per UPT';

    protected function getStats(): array
    {
        $tahun = date('Y'); // bisa diganti dinamis nanti

        // Ambil total realisasi per UPT dari DataTeknis
        $realisasi = DB::table('data_teknis')
            ->join('objek_produksis', 'data_teknis.objek_produksi_id', '=', 'objek_produksis.id')
            ->join('upts', 'objek_produksis.upt_id', '=', 'upts.id')
            ->whereYear('data_teknis.tanggal', $tahun)
            ->select(
                'objek_produksis.upt_id',
                'upts.nama as upt_nama',
                DB::raw('SUM(data_teknis.nilai) as total')
            )
            ->groupBy('objek_produksis.upt_id', 'upts.nama')
            ->get()
            ->keyBy('upt_id');

        // Ambil total target tahun berjalan (semua bidang/komoditas)
        $totalTarget = DB::table('targets')
            ->where('tahun', $tahun)
            ->sum('target_jumlah');

        $stats = [];

        foreach ($realisasi as $uptId => $row) {
            $percent = $totalTarget > 0 ? round(($row->total / $totalTarget) * 100, 2) : 0;

            $stats[] = Stat::make($row->upt_nama, $percent . '%')
                ->color($percent < 60 ? 'danger' : ($percent < 90 ? 'warning' : 'success'));
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
