<?php

namespace App\Filament\Resources\Lokasis\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class LokasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('desa_id')
                //     ->required()
                //     ->numeric(),
                    Select::make('desa_id')
    ->label('Desa')
    ->options(\App\Models\Desa::pluck('nama','id'))
    ->searchable()
    ->required(),
                TextInput::make('nama')
                    ->required(),
                Textarea::make('keterangan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
