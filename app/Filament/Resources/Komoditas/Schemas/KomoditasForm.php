<?php

namespace App\Filament\Resources\Komoditas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\Bidang;
use Filament\Forms\Components\Select;

class KomoditasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('master_bidang_id')
                    ->label('Bidang')
                    ->relationship('bidang','nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('kode')
                    // ->required()
                     ->rules(['nullable','string','max:50']),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('satuan_default')
                    ->required(),
                Toggle::make('is_individual')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
