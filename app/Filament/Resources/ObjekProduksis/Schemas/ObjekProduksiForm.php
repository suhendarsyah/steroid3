<?php

namespace App\Filament\Resources\ObjekProduksis\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use App\Models\UnitLayanan;
use App\Models\Upt;
use App\Models\Komoditas;

class ObjekProduksiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | AUTO DETECT UPT LOGIN (ENGINE STEROID)
            |--------------------------------------------------------------------------
            */
            Hidden::make('upt_id')
                ->default(fn() => auth()->user()->upt_id)
                ->dehydrated(true)
                ->required(),

            /*
            |--------------------------------------------------------------------------
            | NAMA UNIT USAHA
            |--------------------------------------------------------------------------
            */
            TextInput::make('nama')
                ->label('Nama Unit Usaha')
                ->required(),

            /*
            |--------------------------------------------------------------------------
            | UNIT LAYANAN (AUTO MODE STEROID)
            |--------------------------------------------------------------------------
            */
            Select::make('unit_layanan_id')
                ->label('Unit Layanan')
                ->options(function () {

                    $uptId = auth()->user()->upt_id;

                    return UnitLayanan::query()
                        ->where('upt_id', $uptId)
                        ->pluck('nama', 'id')
                        ->toArray();
                })
                ->searchable()
                ->preload()
                ->required(function () {

                    $upt = Upt::find(auth()->user()->upt_id);
                    return $upt?->jenis_upt === 'tematis';
                })
                ->dehydrated(fn($state) => filled($state))
                ->helperText(function () {

                    $upt = Upt::find(auth()->user()->upt_id);

                    return $upt?->jenis_upt === 'tematis'
                        ? 'UPT Tematik wajib memilih Unit Layanan'
                        : 'UPT Wilayah boleh dikosongkan';
                }),

            /*
            |--------------------------------------------------------------------------
            | PEMILIK
            |--------------------------------------------------------------------------
            */
            Select::make('pemilik_id')
                ->relationship('pemilik', 'nama')
                ->searchable()
                ->preload()
                ->required()
                ->live(),

            /*
            |--------------------------------------------------------------------------
            | KOMODITAS / OBJEK PELAYANAN (LABEL DINAMIS STEROID)
            |--------------------------------------------------------------------------
            */
            Select::make('komoditas_id')

                ->label(function () {

                    $bidang = strtolower(auth()->user()->bidang->nama ?? '');

                    if (str_contains($bidang, 'kesehatan')) {
                        return 'Objek Pelayanan';
                    }

                    if (str_contains($bidang, 'peternakan')) {
                        return 'Komoditas Ternak';
                    }

                    if (str_contains($bidang, 'perikanan')) {
                        return 'Komoditas Perikanan';
                    }

                    return 'Komoditas';
                })

                ->relationship(
                    name: 'komoditas',
                    titleAttribute: 'nama',
                    modifyQueryUsing: function ($query, callable $get) {

                        $pemilikId = $get('pemilik_id');

                        if (!$pemilikId) {
                            return;
                        }

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
            TextInput::make('kode_identitas')->nullable(),

            /*
            |--------------------------------------------------------------------------
            | JUMLAH (SMART STEP ENGINE)
            |--------------------------------------------------------------------------
            */
            TextInput::make('jumlah')
                ->numeric()
                ->default(1)
                ->required()
                ->live()
                ->step(function (callable $get) {

                    $komoditas = Komoditas::find($get('komoditas_id'));
                    return $komoditas?->is_individual ? 1 : 0.01;
                })
                ->rule(function (callable $get) {

                    $komoditas = Komoditas::find($get('komoditas_id'));
                    return $komoditas?->is_individual ? 'integer' : 'numeric';
                })
                ->suffix(function (callable $get) {

                    return Komoditas::find($get('komoditas_id'))?->satuan_default;
                })
                ->hint(function (callable $get) {

                    $komoditas = Komoditas::find($get('komoditas_id'));

                    return $komoditas?->is_individual
                        ? 'Jumlah individu (bilangan bulat)'
                        : 'Jumlah produksi sesuai satuan';
                }),

            /*
            |--------------------------------------------------------------------------
            | POPULASI AWAL
            |--------------------------------------------------------------------------
            */
            TextInput::make('populasi_awal')
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }
}