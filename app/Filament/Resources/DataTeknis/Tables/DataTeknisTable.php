<?php

namespace App\Filament\Resources\DataTeknis\Tables;

use Filament\Tables\Table;
use App\Models\UnitLayanan;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;



class DataTeknisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('objekProduksi.nama')
                    ->label('Unit Usaha')
                    
                    ->sortable(),
                TextColumn::make('objekProduksi.unitLayanan.nama')
                    ->label('Unit Layanan')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('kegiatan.nama')
                    
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                // TextColumn::make('nilai')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('nilai')
                    ->label('Jumlah')
                    ->formatStateUsing(function ($state, $record) {

                        $satuan = optional(
                            optional($record->objekProduksi)->komoditas
                        )->satuan_default ?? '';

                        return number_format($state) . ' ' . $satuan;
                    })
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {

                        $komoditas = optional(
                            optional($record->objekProduksi)->komoditas
                        );

                        $satuan = $komoditas?->satuan_default ?? '';

                        // jika satuan ekor → bulat
                        if (strtolower($satuan) === 'ekor') {
                            return number_format($state, 0, ',', '.') . ' ' . $satuan;
                        }

                        // selain ekor → pakai 2 desimal
                        return number_format($state, 2, ',', '.') . ' ' . $satuan;
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

     

           

->filters([
    SelectFilter::make('unit_layanan_id')
        ->label('Unit Layanan')

        ->options(function () {

            $user = auth()->user();

            if ($user->hasRole('upt')) {
                return UnitLayanan::where('upt_id', $user->upt_id)
                    ->pluck('nama','id')
                    ->toArray();
            }

            return UnitLayanan::pluck('nama','id')->toArray();
        })

        ->query(function (Builder $query, array $data) {

            $value = $data['value'] ?? null;

            // ⭐ TANPA PILIHAN → JANGAN FILTER
            if (!$value) {
                return $query;
            }

            return $query->whereHas('objekProduksi', function ($q) use ($value) {
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
