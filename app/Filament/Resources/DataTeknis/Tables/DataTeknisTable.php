<?php

namespace App\Filament\Resources\DataTeknis\Tables;

use Filament\Tables\Table;
use App\Models\UnitLayanan;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class DataTeknisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('objek_produksi_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('objekProduksi.unitLayanan.nama')
                    ->label('Unit Layanan')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('kegiatan_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('nilai')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // ->filters([
            //     SelectFilter::make('unit_layanan_id')
            //         ->label('Unit Layanan')
            //         ->options(
            //             UnitLayanan::query()->pluck('nama', 'id')->toArray()
            //         )
            //         ->query(function ($query, $value) {
            //             $query->whereHas('objekProduksi', function ($q) use ($value) {
            //                 $q->where('unit_layanan_id', $value);
            //             });
            //         }),
            // ])

            ->filters([
                SelectFilter::make('unit_layanan_id')
                    ->label('Unit Layanan')
                    ->options(function () {
                        $user = auth()->user();

                        // =========================
                        // ROLE: UPT
                        // =========================
                        if ($user->hasRole('upt')) {
                            return UnitLayanan::query()
                                ->where('upt_id', $user->upt_id)
                                ->pluck('nama', 'id')
                                ->toArray();
                        }

                        // =========================
                        // ROLE LAIN (Kabid, Admin)
                        // =========================
                        return UnitLayanan::query()
                            ->pluck('nama', 'id')
                            ->toArray();
                    })
                    ->query(function ($query, $value) {
                        $query->whereHas('objekProduksi', function ($q) use ($value) {
                            $q->where('unit_layanan_id', $value);
                        });
                    }),
            ])

            ->recordActions([
                EditAction::make()
                ->visible(fn () => auth()->user()->hasRole('upt')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
