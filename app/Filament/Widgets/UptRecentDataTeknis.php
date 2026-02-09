<?php

namespace App\Filament\Widgets;

use App\Models\DataTeknis;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class UptRecentDataTeknis extends TableWidget
{
    /**
     * Widget hanya tampil untuk role UPT
     */
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('upt');
    }

    /**
     * Judul widget di dashboard
     */
    protected static ?string $heading = 'Data Teknis Terakhir';

    /**
     * Ambil data untuk tabel
     */
    protected function getTableQuery(): Builder
    {
        $uptId = auth()->user()->upt_id;

        return DataTeknis::query()
            ->whereHas('objekProduksi', function ($q) use ($uptId) {
                $q->where('upt_id', $uptId);
            })
            ->latest('tanggal')
            ->limit(5);
    }

    /**
     * Kolom tabel
     */
    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('tanggal')
                ->label('Tanggal')
                ->date('d M Y'),

            TextColumn::make('objekProduksi.nama')
                ->label('Objek Produksi'),

            TextColumn::make('kegiatan.nama')
                ->label('Kegiatan'),

            TextColumn::make('nilai')
                ->label('Nilai')
                ->numeric(),
        ];
    }
}
