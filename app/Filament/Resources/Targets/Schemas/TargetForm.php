<?php

namespace App\Filament\Resources\Targets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;

class TargetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | MASTER BIDANG
                |--------------------------------------------------------------------------
                */
                Hidden::make('master_bidang_id')
                    ->default(fn () => auth()->user()->bidang_id)
                    ->visible(fn () => auth()->user()?->hasRole('kepala_bidang')),

                Select::make('master_bidang_id')
                    ->relationship('bidang', 'nama')
                    ->label('Bidang Teknis')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live()
                    ->visible(fn () =>
                        auth()->user()?->hasAnyRole([
                            'super_admin',
                            'perencanaan',
                        ])
                    ),

                /*
                |--------------------------------------------------------------------------
                | KOMODITAS (FILTER SESUAI BIDANG)
                |--------------------------------------------------------------------------
                */
                
        Select::make('komoditas_id')
                ->relationship(
                    name: 'komoditas',
                    titleAttribute: 'nama'
                )

                // ⭐ INI YANG MEMPERBAIKI ANGKA 6
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->nama)

                ->label(function () {

                    $bidang = strtolower(auth()->user()->bidang->nama ?? '');

                    if (str_contains($bidang,'kesehatan')) return 'Objek Pelayanan';
                    if (str_contains($bidang,'peternakan')) return 'Komoditas Ternak';
                    if (str_contains($bidang,'perikanan')) return 'Komoditas Perikanan';

                    return 'Objek Teknis';
                })

                ->preload()
                ->searchable()
                ->required()
                ->live()
                /*
    |--------------------------------------------------------------------------
    | 🔥 FIX LABEL EDIT MODE (INI YANG MEMPERBAIKI MASALAH ANDA)
    |--------------------------------------------------------------------------
    */
    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nama)

    ->afterStateUpdated(fn ($set) =>
        $set('master_kegiatan_teknis_id', null)
    )

    ->preload()
    ->searchable()
    ->required()
    ->live(),

                /*
                |--------------------------------------------------------------------------
                | JENIS KEGIATAN (FILTER SESUAI KOMODITAS)
                |--------------------------------------------------------------------------
                */
                Select::make('master_kegiatan_teknis_id')
                    ->relationship('kegiatan', 'nama')
                    ->label('Jenis Kegiatan')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live(),
                /*
                |--------------------------------------------------------------------------
                | TAHUN
                |--------------------------------------------------------------------------
                */
                // TextInput::make('tahun')
                //     ->label('Tahun')
                //     ->numeric()
                //     ->required(),

                TextInput::make('tahun')
                    ->label('Tahun Target')
                    ->numeric()
                    ->default(now()->year) // 🔥 otomatis tahun berjalan
                    ->required()
                    ->minValue(2020)
                    ->maxValue(now()->year + 5),

                /*
                |--------------------------------------------------------------------------
                | TARGET
                |--------------------------------------------------------------------------
                */
                TextInput::make('target_jumlah')
                    ->label('Target')
                    ->numeric()
                    ->required(),
            ]);
    }
}