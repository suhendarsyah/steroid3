<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

use App\Models\DataTeknis;

class ChartProduksiUpt extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): ?string
    {
        return 'Produksi per UPT';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 1,
            'md' => 2,
        ];
    }

    protected function getCardClasses(): array
{
    return [
        'bg-slate-950',
        'ring-1',
        'ring-amber-500/20',
        'backdrop-blur-xl',
    ];
}


    public function getExtraAttributes(): array
        {
            return [
                'class' => 'ring-1 ring-amber-500/20 bg-slate-900/30 backdrop-blur-xl rounded-xl'
            ];
        }


    protected function getOptions(): array
{
    return [
        'responsive' => true,
        'maintainAspectRatio' => false,

        'plugins' => [
            'legend' => [
                'labels' => [
                    'color' => '#f59e0b', // amber glow
                    'font' => [
                        'size' => 12,
                        'weight' => '600',
                    ],
                ],
            ],
        ],

        'scales' => [
            'x' => [
                'ticks' => [
                    'color' => '#94a3b8',
                ],
                'grid' => [
                    'color' => 'rgba(255,255,255,0.05)',
                ],
            ],
            'y' => [
                'ticks' => [
                    'color' => '#94a3b8',
                ],
                'grid' => [
                    'color' => 'rgba(245,158,11,0.08)',
                ],
            ],
        ],
    ];
}


    protected function getData(): array
    {
        $user = auth()->user();
        $filters = $this->filters ?? [];

        $startDate = $filters['startDate'] ?? null;
        $endDate   = $filters['endDate'] ?? null;
        $bidangId  = $filters['bidang_id'] ?? null;
        $uptId     = $filters['upt_id'] ?? null;
        $objekProduksiId = $filters['objek_produksi_id'] ?? null;

        if ($user?->hasRole('kepala_bidang')) {
            $bidangId = $user->bidang_id ?? $bidangId;
        }

        if ($user?->hasRole('upt')) {
            $uptId = $user->upt_id ?? $uptId;
        }

        $query = DataTeknis::query()->with(['upt','objekProduksi']);

        if ($startDate) $query->whereDate('tanggal','>=',$startDate);
        if ($endDate)   $query->whereDate('tanggal','<=',$endDate);

        if ($bidangId) {
            $query->whereHas('upt', fn($q)=>$q->where('bidang_id',$bidangId));
        }

        if ($uptId) {
            $query->where('upt_id',$uptId);
        }

        if ($objekProduksiId) {
            $query->where('objek_produksi_id',$objekProduksiId);
        }

        $rows = $query->get();

        /**
         * 🔵 LABEL UPT
         */
        $uptList = $rows
            ->groupBy('upt_id')
            ->map(fn($items)=>optional($items->first()->upt)->nama ?? 'UPT')
            ->values();

        /**
         * 🎨 WARNA OBJEK PRODUKSI
         */
        $colors = [
            'ayam' => 'rgba(34,197,94,0.8)',
            'sapi' => 'rgba(139,69,19,0.8)',
            'ikan' => 'rgba(59,130,246,0.8)',
            'kambing' => 'rgba(234,179,8,0.8)',
        ];

        $datasets = [];

        $objekList = $rows->groupBy('objek_produksi_id');

        foreach ($objekList as $objekId => $items) {

            $namaObjek = optional($items->first()->objekProduksi)->nama ?? 'Produksi';

            $key = strtolower($namaObjek);

            $color = $colors[$key] ?? 'rgba(107,114,128,0.8)';

            $dataPerUpt = $rows
                ->groupBy('upt_id')
                ->map(function ($uptItems) use ($objekId) {
                    return $uptItems
                        ->where('objek_produksi_id', $objekId)
                        ->sum('nilai');
                })
                ->values();

            $datasets[] = [
                'label' => $namaObjek,
                'data'  => $dataPerUpt,

                // 🎨 Warna utama
                'backgroundColor' => $color,
                'borderColor' => $color,

                // 🔥 Command Center Style
                'borderWidth' => 2,
                'borderRadius' => 8,     // sudut rounded futuristik
                'borderSkipped' => false,
                'barThickness' => 28,
                'hoverBackgroundColor' => str_replace('0.8','1',$color),
                        ];
        }

        return [
            'datasets' => $datasets,
            'labels'   => $uptList->toArray(),
        ];
    }
}
