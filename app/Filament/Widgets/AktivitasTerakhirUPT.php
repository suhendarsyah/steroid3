<?php

namespace App\Filament\Widgets;

use App\Models\DataTeknis;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class AktivitasTerakhirUPT extends TableWidget
{
    protected static ?string $heading = 'Aktivitas Terakhir UPT';
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            // 'upt',
            // 'kepala_bidang',
            'kepala_dinas',
            'super_admin',
        ]);
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        $query = DataTeknis::query()
            ->with([
                'kegiatan',                 // master_kegiatan_teknis
                'objekProduksi.upt',
                'objekProduksi.komoditas',
            ])
            ->latest('tanggal')
            ->limit(5);

        /**
         * 🔒 ROLE UPT
         * hanya data UPT sendiri
         */
        // if ($user->hasRole('upt')) {
        //     $query->whereHas('objekProduksi.upt', function ($q) use ($user) {
        //         $q->where('id', $user->upt_id);
        //     });
        // }

        /**
         * 🟡 ROLE KEPALA BIDANG
         * filter BERDASARKAN URUSAN (KEGIATAN)
         */
        // if ($user->hasRole('kepala_bidang')) {
        //     $query->whereHas('kegiatan', function ($q) use ($user) {
        //         $q->where('bidang_id', $user->bidang_id);
        //     });
        // }

        /**
         * 🔓 KEPALA DINAS & SUPER ADMIN
         * TANPA FILTER
         */

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('objekProduksi.upt.nama')
                    ->label('UPT')
                    ->sortable(),

                TextColumn::make('kegiatan.nama')
                    ->label('Kegiatan')
                    ->badge()
                    ->color('info'),


                TextColumn::make('objekProduksi.komoditas.nama')
                    ->label('Komoditas')
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->color('gray'),

            ]);
    }
}
