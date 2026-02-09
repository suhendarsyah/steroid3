<?php

namespace App\Filament\Resources\UnitLayanans\Pages;

use App\Filament\Resources\UnitLayanans\UnitLayananResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUnitLayanan extends EditRecord
{
    protected static string $resource = UnitLayananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
