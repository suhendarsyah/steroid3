<?php

namespace App\Filament\Widgets;

use App\Models\DataTeknis;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TopUPTBidang extends TableWidget
{
    protected static ?string $heading = 'Top 3 UPT Kontributor';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('kepala_bidang');
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->query(
                DataTeknis::query()
                    ->selectRaw('
                        upts.id as id,
                        upts.nama as upt_nama,
                        SUM(data_teknis.nilai) as total_produksi
                    ')
                    ->join('objek_produksis', 'objek_produksis.id', '=', 'data_teknis.objek_produksi_id')
                    ->join('upts', 'upts.id', '=', 'objek_produksis.upt_id')
                    ->where('upts.bidang_id', $user->bidang_id)
                    ->groupBy('upts.id', 'upts.nama')
                    ->orderByDesc('total_produksi')
                    ->limit(3)
            )
            ->columns([
                TextColumn::make('upt_nama')
                    ->label('UPT'),

                TextColumn::make('total_produksi')
                    ->label('Total Produksi')
                    ->numeric()
                    ->sortable(),
            ]);
    }
}
