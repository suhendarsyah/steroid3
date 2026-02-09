<?php

namespace App\Filament\Widgets;

use App\Models\ObjekProduksi;
use App\Models\DataTeknis;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class UptStatOverview extends StatsOverviewWidget
{
    /**
     * Widget ini hanya muncul untuk akun UPT
     */
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('upt');
    }

    /**
     * Isi stat card
     */
    protected function getStats(): array
    {
        $uptId = auth()->user()->upt_id;

        return [

            // ============================
            // JUMLAH OBJEK PRODUKSI
            // ============================
            Stat::make(
                'Objek Produksi',
                ObjekProduksi::where('upt_id', $uptId)->count()
            )
            ->description('Total objek produksi milik UPT Anda'),

            // ============================
            // DATA TEKNIS BULAN INI
            // ============================
            Stat::make(
                'Data Teknis Bulan Ini',
                DataTeknis::whereHas('objekProduksi', function ($q) use ($uptId) {
                    $q->where('upt_id', $uptId);
                })
                ->whereMonth('tanggal', Carbon::now()->month)
                ->whereYear('tanggal', Carbon::now()->year)
                ->count()
            )
            ->description('Jumlah laporan pada bulan berjalan'),
        ];
    }
}
