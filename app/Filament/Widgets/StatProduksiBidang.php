<?php

namespace App\Filament\Widgets;

use App\Models\DataTeknis;
use App\Models\Upt;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatProduksiBidang extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('kepala_bidang');
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        if (! $user->bidang_id) {
            return [
                Stat::make('Produksi Bidang', 0)
                    ->description('Bidang tidak terdeteksi'),
            ];
        }

        $bidangId = $user->bidang_id;

        // ===============================
        // TOTAL PRODUKSI BIDANG
        // ===============================
        $totalProduksi = DataTeknis::whereHas('objekProduksi.upt', function ($q) use ($bidangId) {
            $q->where('bidang_id', $bidangId);
        })->sum('nilai');

        // ===============================
        // JUMLAH UPT DI BIDANG INI
        // ===============================
        $totalUpt = Upt::where('bidang_id', $bidangId)->count();

        // ===============================
        // UPT YANG SUDAH INPUT DATA
        // ===============================
        $uptAktif = DataTeknis::whereHas('objekProduksi.upt', function ($q) use ($bidangId) {
            $q->where('bidang_id', $bidangId);
        })
        ->distinct('objek_produksi_id')
        ->count('objek_produksi_id');

        // ===============================
        // RATA-RATA PRODUKSI PER UPT
        // ===============================
        $rataRata = $totalUpt > 0
            ? $totalProduksi / $totalUpt
            : 0;


        return [

            Stat::make('Total Produksi Bidang', number_format($totalProduksi, 2))
                ->description('Akumulasi seluruh UPT')
                ->color('success'),

            Stat::make('Jumlah UPT', $totalUpt)
                ->description('UPT dalam bidang ini')
                ->color('info'),

            Stat::make('UPT Aktif Input', $uptAktif)
                ->description('UPT yang sudah mengisi data')
                ->color($uptAktif < $totalUpt ? 'warning' : 'success'),

            Stat::make('Rata-rata Produksi / UPT', number_format($rataRata, 2))
                ->description('Indikasi pemerataan produksi')
                ->color('primary'),

        ];
    }
}
