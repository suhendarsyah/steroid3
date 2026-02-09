<?php

namespace App\Filament\Resources\Targets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TargetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('master_bidang_id')
                    ->relationship('bidang', 'nama')
                    ->label('Bidang')
                    ->searchable()
                    ->required(),

                Select::make('komoditas_id')
                    ->relationship('komoditas', 'nama')
                    ->label('Komoditas')
                    ->searchable()
                    ->required(),

                Select::make('master_kegiatan_teknis_id')
                    ->relationship('kegiatan', 'nama')
                    ->label('Jenis Kegiatan')
                    ->searchable()
                    ->required(),

                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->required(),

                TextInput::make('target_jumlah')
                    ->label('Target')
                    ->numeric()
                    ->required(),
            ]);
    }
}
