<?php

namespace App\Filament\Resources\DataTeknis\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class DataTeknisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('objek_produksi_id')
                //     ->required()
                //     ->numeric(),
                // TextInput::make('kegiatan_id')
                //     ->required()
                //     ->numeric(),
                // DatePicker::make('tanggal')
                //     ->required(),
                // TextInput::make('nilai')
                //     ->numeric()
                //     ->default(null),
                // Textarea::make('keterangan')
                //     ->default(null)
                //     ->columnSpanFull(),


                Select::make('objek_produksi_id')
                    ->label('Objek Produksi')
                    ->relationship('objekProduksi', 'nama') // relasi ke model ObjekProduksi
                    // ->options(
                    //     \App\Models\ObjekProduksi::all()
                    //         ->mapWithKeys(fn($o) => [
                    //             $o->id => $o->nama ?? 'Objek #' . $o->id,
                    //         ])
                    ->options(
                        \App\Models\ObjekProduksi::query()
                            ->where('upt_id', auth()->user()->upt_id)
                            ->pluck('nama', 'id')
                            ->toArray()

                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('kegiatan_id')
                    ->label('Jenis Kegiatan')
                    ->relationship('kegiatan', 'nama') // relasi ke model Kegiatan
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('tanggal')
                    ->required(),

                TextInput::make('nilai')
                    ->numeric()
                    ->required(),

                Textarea::make('keterangan')
                    ->rows(3),

            ]);
    }
}
