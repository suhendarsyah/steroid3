<?php

namespace App\Filament\Resources\ObjekProduksis\Schemas;

use App\Models\UnitLayanan;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;

class ObjekProduksiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | UPT (AUTO DARI USER LOGIN)
                |--------------------------------------------------------------------------
                */
                Hidden::make('upt_id')
                    ->default(fn () => auth()->user()->upt_id)
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | IDENTITAS OBJEK PRODUKSI
                |--------------------------------------------------------------------------
                */
                TextInput::make('nama')
                    ->label('Nama Objek Produksi')
                    ->placeholder('Contoh: Produksi Daging Sapi')
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | UNIT LAYANAN (KHUSUS UPT TEMATIK)
                |--------------------------------------------------------------------------
                */
                Select::make('unit_layanan_id')
                    ->label('Unit Layanan')
                    ->options(
                        UnitLayanan::query()
                            ->where('upt_id', auth()->user()->upt_id)
                            ->pluck('nama', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->nullable()
                    ->helperText('Kosongkan jika UPT Wilayah'),

                /*
                |--------------------------------------------------------------------------
                | PEMILIK
                |--------------------------------------------------------------------------
                */
                Select::make('pemilik_id')
                    ->label('Pemilik')
                    ->relationship('pemilik', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | KOMODITAS
                |--------------------------------------------------------------------------
                */
                Select::make('komoditas_id')
                    ->label('Komoditas')
                    ->relationship('komoditas', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | OPSIONAL
                |--------------------------------------------------------------------------
                */
                TextInput::make('kode_identitas')
                    ->label('Kode Identitas')
                    ->nullable(),

                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->default(1)
                    ->required(),

                TextInput::make('populasi_awal')
                    ->label('Populasi Awal (ekor)')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
