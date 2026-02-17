<?php

namespace App\Filament\Resources\Targets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TargetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun'),
                // TextColumn::make('master_bidang_id')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('komoditas.bidang.nama')
                    ->label('Bidang')
                    ->sortable(),
                // TextColumn::make('komoditas_id')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('komoditas.nama')
                    ->label('Komoditas')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('target_jumlah')
                     ->label('Target')
                     ->sortable(),

                TextColumn::make('realisasi')
                    ->label('Realisasi')
                    ->getStateUsing(fn ($record) => $record->realisasiTahunIni()),

                TextColumn::make('persentase')
                    ->label('% Capaian')
                    ->getStateUsing(function ($record) {
                        $realisasi = $record->realisasiTahunIni();
                        if ($record->target_jumlah == 0) {
                            return '0 %';
                        }

                        $persen = ($realisasi / $record->target_jumlah) * 100;
                        return number_format($persen, 1) . ' %';
                    }),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                ->visible(fn () => auth()->user()->hasRole(['super_admin','kepala_dinas','perencanaan'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
