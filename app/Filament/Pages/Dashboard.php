<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Services\DownloadDataService;
use App\Filament\Widgets\UptStatOverview;
use App\Filament\Widgets\StatRealisasiGlobal;
use App\Filament\Widgets\UptRecentDataTeknis;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    // protected function getWidgets(): array
    // {
    //     return [
    //         UptStatOverview::class,
    //         UptQuickActions::class,
    //         UptRecentDataTeknis::class,
    //     ];
    // }



    /**
     * ===============================
     * HEADER WIDGETS (KARTU STAT)
     * ===============================
     */
    protected function getHeaderWidgets(): array
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return [
                \App\Filament\Widgets\StatTotalData::class,
            ];
        }

        if ($user->hasRole('kepala_dinas')) {
            return [
                \App\Filament\widgets\StatRealisasiGlobal::class,
            ];
        }

        if ($user->hasRole('kepala_bidang')) {
            return [
                \App\Filament\Widgets\StatProduksiBidang::class,
            ];
        }

        if ($user->hasRole('upt')) {
            return [
                \App\Filament\Widgets\StatProduksiUPT::class,
            ];
        }

        return [];
    }

    /**
     * ===============================
     * HEADER ACTIONS (TOMBOL ATAS)
     * ===============================
     * ⛔️ PENTING:
     * - DI FILAMENT 4 HARUS getHeaderActions()
     * - getActions() TIDAK DIPAKAI
     */
    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        // Hanya Kadis & Super Admin
        if (! $user || ! $user->hasAnyRole(['kepala_dinas', 'super_admin'])) {
            return [];
        }

        return [
            Action::make('dalamAngka')
                ->label('Peternakan & Perikanan Dalam Angka')
                ->icon('heroicon-o-chart-bar')
                ->color('primary')
                ->url('#'), // sementara, nanti kita ganti ke Page

        ];
    }

    public function getTitle(): string
    {
        return 'DASHBOARD INI AKTIF';
    }


    /**
     * ===============================
     * AKSES DASHBOARD
     * ===============================
     */
    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole([
            'super_admin',
            'perencanaan',
            'kepala_dinas',
            'kepala_bidang',
            'upt',
        ]);
    }
}
