<?php

namespace App\Filament\Resources\Upts\Pages;

use App\Filament\Resources\Upts\UptResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUpt extends EditRecord
{
    protected static string $resource = UptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
