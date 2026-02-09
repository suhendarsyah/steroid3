<?php

namespace App\Filament\Resources\Pemiliks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PemilikForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                Textarea::make('alamat')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('desa_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('no_hp')
                    ->default(null),
            ]);
    }
}
