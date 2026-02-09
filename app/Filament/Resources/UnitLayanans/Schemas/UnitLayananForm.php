<?php

namespace App\Filament\Resources\UnitLayanans\Schemas;

use App\Models\Upt;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MultiSelect;

class UnitLayananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('upt_id')
                ->label('UPT Induk')
                ->options(
                    Upt::query()->pluck('nama', 'id')->toArray()
                )
                ->searchable()
                ->required()
                ->helperText('UPT organisasi yang membawahi unit layanan ini'),

            TextInput::make('nama')
                ->label('Nama Unit Layanan')
                ->required(),

            MultiSelect::make('kecamatans')
                ->label('Wilayah Kecamatan')
                ->relationship('kecamatans', 'nama')
                ->searchable()
                ->helperText('Pilih kecamatan yang masuk dalam cakupan pelayanan unit ini'),


            Textarea::make('keterangan')
                ->label('Keterangan')
                ->nullable()
                ->columnSpanFull(),
        ]);
    }
}
