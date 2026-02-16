<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

use App\Models\DataTeknis;

class ChartTrenProduksiBulanan extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }


    public function getHeading(): ?string
    {
        return 'Tren Produksi Bulanan';
    }

    protected function getType(): string
    {
        return 'line';
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
         * 🔥 AUTO ROLE SCOPE
         */
        if ($user?->hasRole('kepala_bidang')) {
            $bidangId = $user->bidang_id ?? $bidangId;
        }

        if ($user?->hasRole('upt')) {
            $uptId = $user->upt_id ?? $uptId;
        }

        $query = DataTeknis::query();

        if ($startDate) {
            $query->whereDate('tanggal','>=',$startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal','<=',$endDate);
        }

        if ($bidangId) {
            $query->whereHas('upt', fn($q)=>$q->where('bidang_id',$bidangId));
        }

        if ($uptId) {
            $query->where('upt_id',$uptId);
        }

        /**
         * 🟪 SAFE MODE
         * group bulanan di PHP
         */
        $data = $query->get()
            ->groupBy(fn($row)=>date('Y-m', strtotime($row->tanggal)))
            ->map(fn($items)=>$items->sum('jumlah_produksi'));

        return [
            'datasets' => [
                [
                    'label' => 'Produksi',
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }
}
