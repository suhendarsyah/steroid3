<?php

namespace App\Filament\Resources\Komoditas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KomoditasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('master_bidang_id')
                    ->required()
                    ->numeric(),
                TextInput::make('kode')
                    ->required(),
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
