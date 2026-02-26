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

                /*
                |--------------------------------------------------------------------------
                | TAHUN
                |--------------------------------------------------------------------------
                */
                TextColumn::make('tahun')
                    ->label('Tahun'),

                /*
                |--------------------------------------------------------------------------
                | BIDANG TEKNIS (FIXED ENGINE STEROID)
                |--------------------------------------------------------------------------
                */
                TextColumn::make('bidang.nama')
                    ->label('Bidang Teknis')
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | OBJEK / KOMODITAS DINAMIS SESUAI BIDANG TARGET
                |--------------------------------------------------------------------------
                */
                TextColumn::make('komoditas.nama')

                    ->label(function ($record) {

                        $bidang = strtolower(
                            $record->bidang->nama ?? ''
                        );

                        if (str_contains($bidang, 'kesehatan')) {
                            return 'Objek Pelayanan';
                        }

                        if (str_contains($bidang, 'peternakan')) {
                            return 'Komoditas Ternak';
                        }

                        if (str_contains($bidang, 'perikanan')) {
                            return 'Komoditas Perikanan';
                        }

                        return 'Objek Teknis';
                    })

                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | CREATED & UPDATED
                |--------------------------------------------------------------------------
                */
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                /*
                |--------------------------------------------------------------------------
                | TARGET
                |--------------------------------------------------------------------------
                */
                TextColumn::make('target_jumlah')
                    ->label('Target')
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | REALISASI
                |--------------------------------------------------------------------------
                */
                TextColumn::make('realisasi')
                    ->label('Realisasi')
                    ->getStateUsing(fn ($record) => $record->realisasiTahunIni()),

                /*
                |--------------------------------------------------------------------------
                | PERSENTASE CAPAIAN
                |--------------------------------------------------------------------------
                */
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

            /*
            |--------------------------------------------------------------------------
            | RECORD ACTIONS
            |--------------------------------------------------------------------------
            */
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole([
                        'super_admin',
                        'kepala_dinas',
                        'perencanaan',
                        'kepala_bidang',
                    ]) ?? false),
            ])

            /*
            |--------------------------------------------------------------------------
            | TOOLBAR ACTIONS
            |--------------------------------------------------------------------------
            */
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}