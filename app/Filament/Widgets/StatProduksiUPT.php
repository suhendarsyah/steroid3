<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\DataTeknis;
use Illuminate\Support\Facades\Auth;

class StatProduksiUPT extends StatsOverviewWidget
{
    // Role-based
    public static function canView(): bool
    {
        return Auth::user()?->hasRole('upt');
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        // Default 0 jika UPT tidak ada
        $total = 0;

        if ($user && $user->upt_id) {
            $total = DataTeknis::whereHas('objekProduksi.upt', function($q) {
    $q->where('jenis_upt', 'wilayah');
})->sum('nilai');



        }

        // Kembalikan array Stat
        return [
            Stat::make('Produksi UPT', number_format($total, 2))
                ->description('Total produksi UPT saat ini'),
        ];
    }
}
