<?php

namespace App\Filament\Resources\Upts\Pages;

use App\Filament\Resources\Upts\UptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUpts extends ListRecords
{
    protected static string $resource = UptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
