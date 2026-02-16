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
                    ->label('Nama Unit Usaha')
                    ->placeholder('Contoh: Produksi Daging Sapi')
                    ->required(),
                // Select::make('objek_produksi_id')
                //     ->label('Unit Usaha')
                //     ->relationship('objekProduksi', 'nama')
                //     ->searchable()
                //     ->preload(),

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
                    ->preload()
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
                    ->required()
                    ->live(),

                /*
                |--------------------------------------------------------------------------
                | KOMODITAS
                |--------------------------------------------------------------------------
                */
                Select::make('komoditas_id')
                    ->label('Komoditas')
                    ->relationship(
                            name: 'komoditas',
                            titleAttribute: 'nama',
                            modifyQueryUsing: function ($query, callable $get) {

                                $pemilikId = $get('pemilik_id');

                                if ($pemilikId) {

                                    $query->where(function ($q) use ($pemilikId) {

                                        $q->whereIn('id', function ($sub) use ($pemilikId) {
                                            $sub->select('komoditas_id')
                                                ->from('objek_produksis')
                                                ->where('pemilik_id', $pemilikId);
                                        })
                                        ->orWhereNotExists(function ($sub) use ($pemilikId) {
                                            $sub->selectRaw(1)
                                                ->from('objek_produksis')
                                                ->where('pemilik_id', $pemilikId);
                                        });

                                    });
                                }
                            }
                        )

                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),


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
                    ->required()
                    ->live() // ⭐ supaya update saat komoditas berubah

                    /**
                     * 🔵 STEP OTOMATIS
                     * individu = 1
                     * produksi = 0.01
                     */
                    ->step(function (callable $get) {

                        $komoditasId = $get('komoditas_id');

                        if (!$komoditasId) return 0.01;

                        $komoditas = \App\Models\Komoditas::find($komoditasId);

                        if ($komoditas?->is_individual) {
                            return 1;
                        }

                        return 0.01;
                    })

                    /**
                     * 🔵 VALIDASI DINAMIS
                     */
                    ->rule(function (callable $get) {

                        $komoditasId = $get('komoditas_id');

                        if (!$komoditasId) return 'numeric';

                        $komoditas = \App\Models\Komoditas::find($komoditasId);

                        if ($komoditas?->is_individual) {
                            return 'integer';
                        }

                        return 'numeric';
                    })

                    /**
                     * 🔵 SUFFIX SATUAN OTOMATIS
                     */
                    ->suffix(function (callable $get) {

                        $komoditasId = $get('komoditas_id');

                        if (!$komoditasId) return null;

                        return \App\Models\Komoditas::find($komoditasId)?->satuan_default;
                    })

                    /**
                     * 🔵 HINT BIAR USER PAHAM
                     */
                    ->hint(function (callable $get) {

                        $komoditasId = $get('komoditas_id');

                        if (!$komoditasId) return null;

                        $komoditas = \App\Models\Komoditas::find($komoditasId);

                        return $komoditas?->is_individual
                            ? 'Jumlah individu (harus bilangan bulat)'
                            : 'Jumlah produksi sesuai satuan komoditas';
                    }),



                TextInput::make('populasi_awal')
                    ->label('Populasi Awal (ekor)')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
