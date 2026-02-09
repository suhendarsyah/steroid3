<?php

namespace App\Filament\Resources\UnitLayanans\Pages;

use App\Filament\Resources\UnitLayanans\UnitLayananResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnitLayanans extends ListRecords
{
    protected static string $resource = UnitLayananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
