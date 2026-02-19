<?php

namespace App\Filament\Resources\UnitLayanans\Schemas;

use App\Models\Upt;
use App\Models\UnitLayanan;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class UnitLayananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | PILIH UPT INDUK
            |--------------------------------------------------------------------------
            */
            Select::make('upt_id')
                ->label('UPT Induk')
                ->options(Upt::query()->pluck('nama', 'id')->toArray())
                ->searchable()
                ->required()
                ->preload()
                ->reactive() // ⭐ WAJIB supaya form berubah realtime
                ->helperText('UPT organisasi yang membawahi unit layanan ini'),

            /*
            |--------------------------------------------------------------------------
            | INFO WILAYAH / UNIT YANG SUDAH ADA
            |--------------------------------------------------------------------------
            */
            Placeholder::make('info_upt')
                ->label('Info Wilayah / Unit')
                ->content(function (callable $get) {

                    $uptId = $get('upt_id');

                    if (!$uptId) {
                        return new HtmlString('<span style="color:gray">Pilih UPT terlebih dahulu</span>');
                    }

                    $upt = Upt::find($uptId);

                    if (!$upt) {
                        return '-';
                    }

                    /*
                    ----------------------------------------------------------
                    🔵 UPT WILAYAH → tampilkan kecamatan
                    ----------------------------------------------------------
                    */
                    if ($upt->jenis_upt === 'wilayah') {

                        $kecamatans = \DB::table('unit_layanans')
                            ->join('unit_layanan_kecamatan', 'unit_layanans.id', '=', 'unit_layanan_kecamatan.unit_layanan_id')
                            ->join('kecamatans', 'kecamatans.id', '=', 'unit_layanan_kecamatan.kecamatan_id')
                            ->where('unit_layanans.upt_id', $uptId)
                            ->pluck('kecamatans.nama')
                            ->unique()
                            ->toArray();

                        if (empty($kecamatans)) {
                            return new HtmlString('<span style="color:#ef4444">Belum ada kecamatan terdaftar</span>');
                        }

                        return new HtmlString(
                            '<b>Kecamatan yang sudah ada:</b><br>' .
                                implode('<br>', $kecamatans)
                        );
                    }

                    /*
                    ----------------------------------------------------------
                    🟣 UPT TEMATIK → tampilkan unit layanan
                    ----------------------------------------------------------
                    */
                    if ($upt->jenis_upt === 'tematis') {

                        $units = UnitLayanan::where('upt_id', $uptId)
                            ->pluck('nama')
                            ->toArray();

                        if (empty($units)) {
                            return new HtmlString('<span style="color:#ef4444">Belum ada unit layanan</span>');
                        }

                        return new HtmlString(
                            '<b>Unit layanan yang sudah ada:</b><br>' .
                                implode('<br>', $units)
                        );
                    }

                    return '-';
                })
                ->reactive(),

            /*
            |--------------------------------------------------------------------------
            | NAMA UNIT LAYANAN
            |--------------------------------------------------------------------------
            */
            TextInput::make('nama')
                ->label('Nama Unit Layanan')
                ->required(),

            /*
            |--------------------------------------------------------------------------
            | KHUSUS UPT WILAYAH → PILIH KECAMATAN
            |--------------------------------------------------------------------------
            */
            MultiSelect::make('kecamatans')
                ->label('Wilayah Kecamatan')
                ->relationship('kecamatans', 'nama')
                ->searchable()
                ->preload()

                ->visible(function (callable $get) {

                    $uptId = $get('upt_id');

                    if (!$uptId) return false;

                    $upt = Upt::find($uptId);

                    return $upt && $upt->jenis_upt === 'wilayah';
                })

                ->helperText(function (callable $get) {

                    $uptId = $get('upt_id');

                    if (!$uptId) return null;

                    $upt = Upt::find($uptId);

                    return $upt?->jenis_upt === 'wilayah'
                        ? 'Pilih kecamatan yang masuk wilayah UPT'
                        : null;
                }),

            /*
            |--------------------------------------------------------------------------
            | KETERANGAN
            |--------------------------------------------------------------------------
            */
            Textarea::make('keterangan')
                ->label('Keterangan')
                ->nullable()
                ->columnSpanFull(),
        ]);
    }
}
