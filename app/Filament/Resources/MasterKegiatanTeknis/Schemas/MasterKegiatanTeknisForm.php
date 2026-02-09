<?php

namespace App\Filament\Resources\MasterKegiatanTeknis\Schemas;

use App\Models\Bidang;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterKegiatanTeknisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Kegiatan')
                    ->required(),

                Select::make('bidang_id')
                    ->label('Bidang / Urusan')
                    ->options(
                        Bidang::query()->pluck('nama', 'id')
                    )
                    ->required()
                    ->searchable(),
            ]);
    }
}
