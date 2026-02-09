<?php

namespace App\Filament\Widgets;

use App\Models\DataTeknis;
use App\Models\Upt;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatUPTPerluPerhatian extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('kepala_bidang');
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        if (! $user->bidang_id) {
            return [];
        }

        $bidangId = $user->bidang_id;

        // Total UPT di bidang ini
        $totalUpt = Upt::where('bidang_id', $bidangId)->count();

        // UPT yang sudah input data
        $uptAktif = DataTeknis::whereHas('objekProduksi.upt', function ($q) use ($bidangId) {
            $q->where('bidang_id', $bidangId);
        })
        ->distinct('objek_produksi_id')
        ->count('objek_produksi_id');

        $uptBermasalah = max($totalUpt - $uptAktif, 0);

        return [
            Stat::make('UPT Perlu Perhatian', $uptBermasalah)
                ->description('Belum input data teknis')
                ->color($uptBermasalah > 0 ? 'danger' : 'success'),
        ];
    }
}
