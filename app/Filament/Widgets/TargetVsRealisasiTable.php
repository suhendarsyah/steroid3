<?php

namespace App\Filament\Widgets;

use App\Models\Target;
use App\Models\DataTeknis;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TargetVsRealisasiTable extends TableWidget
{
    protected static ?string $heading = 'Target vs Realisasi';

    protected static ?int $sort = 2;

    protected function getTableQuery(): Builder
    {
        return Target::query()
            ->with(['bidang', 'komoditas', 'kegiatan']);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tahun')
                ->label('Tahun'),

            Tables\Columns\TextColumn::make('bidang.nama')
                ->label('Bidang'),

            Tables\Columns\TextColumn::make('komoditas.nama')
                ->label('Komoditas'),

            Tables\Columns\TextColumn::make('kegiatan.nama')
                ->label('Kegiatan'),

            Tables\Columns\TextColumn::make('target_jumlah')
                ->label('Target')
                ->numeric(),

            Tables\Columns\TextColumn::make('realisasi')
                ->label('Realisasi')
                ->getStateUsing(function (Target $record) {
                    return DataTeknis::whereYear('tanggal', $record->tahun)
                        ->where('kegiatan_id', $record->master_kegiatan_teknis_id)
                        ->sum('nilai');
                }),

            Tables\Columns\TextColumn::make('capaian')
                ->label('Capaian (%)')
                ->getStateUsing(function (Target $record) {
                    $realisasi = DataTeknis::whereYear('tanggal', $record->tahun)
                        ->where('kegiatan_id', $record->master_kegiatan_teknis_id)
                        ->sum('nilai');

                    if ($record->target_jumlah == 0) {
                        return '0 %';
                    }

                    return round(($realisasi / $record->target_jumlah) * 100, 1) . ' %';
                }),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('perencanaan');
    }
}
