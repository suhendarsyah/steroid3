<?php

namespace App\Filament\Resources\ObjekProduksis\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ObjekProduksisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('kode_identitas')
                    ->label('Kode Identitas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama')
                    ->label('Nama Objek')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pemilik.nama')
                    ->label('Pemilik')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('upt.nama')
                    ->label('UPT')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('komoditas.nama')
                    ->label('Komoditas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah Awal')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('populasi_sekarang')
                    ->label('Populasi Saat Ini')
                    ->getStateUsing(fn ($record) => $record->populasiSekarang())
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
