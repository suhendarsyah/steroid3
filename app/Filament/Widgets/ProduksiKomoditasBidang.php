<?php

namespace App\Filament\Widgets;

use App\Models\DataTeknis;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class ProduksiKomoditasBidang extends TableWidget
{
    protected static ?string $heading = 'Produksi per Komoditas';

    protected static ?int $sort = 2;

    /**
     * Hanya tampil untuk Kepala Bidang
     */
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('kepala_bidang');
    }

    /**
     * Table widget (Filament 4 HARUS pakai ini)
     */
    public function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->query(
                DataTeknis::query()
    ->selectRaw('
        komoditas.id as id,
        komoditas.nama as komoditas_nama,
        SUM(data_teknis.nilai) as total_produksi
    ')
    ->join('objek_produksis', 'objek_produksis.id', '=', 'data_teknis.objek_produksi_id')
    ->join('komoditas', 'komoditas.id', '=', 'objek_produksis.komoditas_id')
    ->groupBy('komoditas.id', 'komoditas.nama')
    ->orderByDesc('total_produksi')

            )
            ->columns([
                TextColumn::make('komoditas_nama')
                    ->label('Komoditas')
                    ->searchable(),

                TextColumn::make('total_produksi')
                    ->label('Total Produksi')
                    ->numeric()
                    ->sortable(),
            ]);
    }
}
