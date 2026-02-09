<?php

namespace App\Filament\Widgets;

use App\Models\Upt;
use App\Models\Komoditas;
use App\Models\DataTeknis;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatTotalData extends StatsOverviewWidget
{
    protected ?string $heading = 'Total Data';
    protected function getStats(): array
    {
        return [
            // Stat::make('Total Data Teknis', DataTeknis::count()),
            // Stat::make('Total UPT', Upt::count()),
            // Stat::make('Total Komoditas', Komoditas::count()),

            Stat::make('Aktivitas Lapangan', DataTeknis::count())
                ->description('Total laporan masuk')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('primary'),

            Stat::make('Jumlah UPT', Upt::count())
                ->description('UPT aktif')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),

            Stat::make('Komoditas', Komoditas::count())
                ->description('Objek produksi')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('warning'),

        ];
    }


    // hanya super admin dan kadis yang bisa melihat
    public static function canView(): bool
{
    $user = auth()->user();

    return $user
        && (
            $user->hasRole('super_admin')
            || $user->hasRole('kepala_dinas')
        );
}

}
