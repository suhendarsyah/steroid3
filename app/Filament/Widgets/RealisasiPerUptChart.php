<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RealisasiPerUptChart extends ChartWidget
{
    protected ?string $heading = 'Realisasi Per Upt Chart';

    protected function getData(): array
    {
        $tahun = now()->year;

        $rows = DB::table('data_teknis')
            ->join('objek_produksis', 'data_teknis.objek_produksi_id', '=', 'objek_produksis.id')
            ->join('upts', 'objek_produksis.upt_id', '=', 'upts.id')
            ->select(
                'upts.nama as nama_upt',
                DB::raw('SUM(data_teknis.nilai) as total')
            )
            ->whereYear('data_teknis.tanggal', $tahun)
            ->groupBy('upts.id', 'upts.nama')
            ->orderBy('upts.nama')
            ->get();
        return [
            'datasets' => [
                [
                    'label' => 'Total Produksi',
                    'data' => $rows->pluck('total')->toArray(),
                ],
            ],
            'labels' => $rows->pluck('nama_upt')->toArray(),

        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
{
    return auth()->user()?->hasAnyRole([
        'super_admin',
        'kepala_dinas',
    ]);
}
}
