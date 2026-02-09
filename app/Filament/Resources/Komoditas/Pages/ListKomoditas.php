<?php

namespace App\Filament\Resources\Komoditas\Pages;

use App\Filament\Resources\Komoditas\KomoditasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKomoditas extends ListRecords
{
    protected static string $resource = KomoditasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
