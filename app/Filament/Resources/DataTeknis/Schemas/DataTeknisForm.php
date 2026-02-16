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
                Select::make('objek_produksi_id')
                    ->label('Unit Usaha')
                    ->relationship(
                        name: 'objekProduksi',
                        titleAttribute: 'nama',
                        modifyQueryUsing: function ($query) {

                            $user = auth()->user();

                            // 🔵 ROLE UPT → hanya unit usaha miliknya
                            if ($user?->hasRole('upt') && $user->upt_id) {
                                $query->where('upt_id', $user->upt_id);
                            }

                            // 🔵 ROLE ADMIN / KADIS → semua boleh
                            return $query;
                        }
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),


                Select::make('kegiatan_id')
                    ->label('Jenis Kegiatan')
                    ->relationship(
                        name: 'kegiatan',
                        titleAttribute: 'nama',
                        modifyQueryUsing: fn ($query) => 
                            $query->whereNotNull('bidang_id') // ⭐ hanya kegiatan yang punya bidang
                    )
                    ->searchable()
                    ->preload()
                    ->required(),


                DatePicker::make('tanggal')
                    ->required(),

                // TextInput::make('nilai')
                //     ->numeric()
                //     ->required(),

                TextInput::make('nilai')
                    ->label('Jumlah Realisasi')
                    ->required()
                    ->numeric()
                    ->live() // ⭐ supaya otomatis berubah saat unit usaha diganti

                    /**
                     * 🔵 FORMAT INPUT OTOMATIS
                     */
                    ->step(function (callable $get) {

                        $objekId = $get('objek_produksi_id');

                        if (!$objekId) return 0.01;

                        $objek = \App\Models\ObjekProduksi::with('komoditas')->find($objekId);

                        // individu → tidak boleh desimal
                        if ($objek?->komoditas?->is_individual) {
                            return 1;
                        }

                        // produksi → boleh desimal
                        return 0.01;
                    })

                    /**
                     * 🔵 VALIDASI DINAMIS
                     */
                    ->rule(function (callable $get) {

                        $objekId = $get('objek_produksi_id');

                        if (!$objekId) return 'numeric';

                        $objek = \App\Models\ObjekProduksi::with('komoditas')->find($objekId);

                        if ($objek?->komoditas?->is_individual) {
                            return 'integer';
                        }

                        return 'numeric';
                    })

                    /**
                     * 🔵 SATUAN OTOMATIS (UX DINAS)
                     */
                    ->suffix(function (callable $get) {

                        $objekId = $get('objek_produksi_id');

                        if (!$objekId) return null;

                        $objek = \App\Models\ObjekProduksi::with('komoditas')->find($objekId);

                        return $objek?->komoditas?->satuan_default;
                    })

                    /**
                     * 🔵 HINT BIAR USER PAHAM
                     */
                    ->hint(function (callable $get) {

                        $objekId = $get('objek_produksi_id');

                        if (!$objekId) return null;

                        $objek = \App\Models\ObjekProduksi::with('komoditas')->find($objekId);

                        if (!$objek?->komoditas) return null;

                        return $objek->komoditas->is_individual
                            ? 'Masukkan angka bulat (satuan individu)'
                            : 'Masukkan jumlah produksi sesuai satuan';
                    }),


                Textarea::make('keterangan')
                    ->rows(3),

            ]);
    }
}
