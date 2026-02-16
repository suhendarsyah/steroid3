<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

use App\Models\DataTeknis;

class TopUptAktifWidget extends Widget
{
    use InteractsWithPageFilters;

    protected string $view = 'filament.widgets.top-upt-aktif-widget';

    public function getColumnSpan(): int|string|array
        {
            return [
                'default' => 1,
                'md' => 1,
            ];
        }

    public function getTopUpt()
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

        $query = DataTeknis::query()->with('upt');

        if ($startDate) {
            $query->whereDate('tanggal','>=',$startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal','<=',$endDate);
        }

        // if ($bidangId) {
        //     $query->whereHas('upt', fn($q)=>$q->where('bidang_id',$bidangId));
        // }

        if ($user?->hasRole('kepala_bidang') && !$uptId) {
                    $bidangId = $user->bidang_id ?? $bidangId;
                }


        if ($uptId) {
            $query->where('upt_id',$uptId);
        }

        /**
         * 🟪 SAFE MODE
         * ranking di PHP
         */
        return $query->get()
            ->groupBy('upt_id')
            ->map(function ($items) {
                return [
                    'nama' => $items->first()->upt->nama ?? '-',
                    'total' => $items->sum('nilai'),
                ];
            })
            ->sortByDesc('total')
            ->take(5);
    }
}
