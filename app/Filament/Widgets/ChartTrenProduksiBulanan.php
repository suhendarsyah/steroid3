<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\DataTeknis;

class ChartTrenProduksiBulanan extends ChartWidget
{
    use InteractsWithPageFilters;

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): ?string
    {
        return 'Tren Produksi Bulanan per Komoditas';
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $filters = $this->filters ?? [];

        $startDate = $filters['startDate'] ?? null;
        $endDate   = $filters['endDate'] ?? null;
        $bidangId  = $filters['bidang_id'] ?? null;
        $uptId     = $filters['upt_id'] ?? null;

        /**
         * 🔥 AUTO ROLE SCOPE (tidak merusak struktur sistem)
         */
        if ($user?->hasRole('kepala_bidang')) {
            $bidangId = $user->bidang_id ?? $bidangId;
        }

        if ($user?->hasRole('upt')) {
            $uptId = $user->upt_id ?? $uptId;
        }

        $query = DataTeknis::query()
            ->with('objekProduksi.komoditas');

        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        if ($bidangId) {
            $query->whereHas('objekProduksi.upt', fn ($q) =>
                $q->where('bidang_id', $bidangId)
            );
        }

        if ($uptId) {
            $query->whereHas('objekProduksi', fn ($q) =>
                $q->where('upt_id', $uptId)
            );
        }

        $rows = $query->get();

        /**
         * =============================
         * 🟢 GROUPING PER KOMODITAS
         * =============================
         */
        $grouped = $rows->groupBy(fn ($row) =>
            $row->objekProduksi?->komoditas?->nama ?? 'Tanpa Komoditas'
        );

        $labels = collect(range(1, 12))
            ->map(fn ($m) => date('M', mktime(0,0,0,$m,1)))
            ->toArray();

        $datasets = [];

        $colors = [
            '#3b82f6','#ef4444','#10b981','#f59e0b',
            '#8b5cf6','#06b6d4','#ec4899','#84cc16'
        ];

        $i = 0;

        foreach ($grouped as $komoditas => $items) {

            $bulanan = collect(range(1,12))->map(function ($bulan) use ($items) {

                return $items
                    ->filter(fn ($r) => date('n', strtotime($r->tanggal)) == $bulan)
                    ->sum('nilai'); // 🔥 pakai field nilai
            });

            $datasets[] = [
                'label' => $komoditas,
                'data' => $bulanan->values(),
                'borderColor' => $colors[$i % count($colors)],
                'backgroundColor' => $colors[$i % count($colors)],
                'tension' => 0.4,
                'pointRadius' => 3,
                'borderWidth' => 2,
            ];

            $i++;
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    /**
     * 🎨 UPGRADE STYLE CHART
     */
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
