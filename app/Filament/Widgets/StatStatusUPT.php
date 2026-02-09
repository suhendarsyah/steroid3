<?php

namespace App\Filament\Widgets;

use App\Models\DataTeknis;
use App\Models\Upt;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatStatusUPT extends StatsOverviewWidget
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

        $totalUpt = Upt::where('bidang_id', $bidangId)->count();

        $uptAktif = DataTeknis::whereHas('objekProduksi.upt', function ($q) use ($bidangId) {
            $q->where('bidang_id', $bidangId);
        })
        ->distinct('objek_produksi_id')
        ->count('objek_produksi_id');

        $uptBelumAktif = max($totalUpt - $uptAktif, 0);

        return [
            Stat::make('UPT Aktif', $uptAktif)
                ->description('Sudah input data')
                ->color('success'),

            Stat::make('UPT Belum Input', $uptBelumAktif)
                ->description('Perlu perhatian')
                ->color($uptBelumAktif > 0 ? 'danger' : 'success'),
        ];
    }
}
