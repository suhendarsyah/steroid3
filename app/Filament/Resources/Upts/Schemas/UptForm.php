<?php

namespace App\Filament\Resources\Upts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class UptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                Select::make('jenis')
            ->options([
                'wilayah' => 'UPT Wilayah',
                'tematis' => 'UPT Tematis',
            ])
            ->required()
            ]);
    }
}
