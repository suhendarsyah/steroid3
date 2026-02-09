<?php

namespace App\Filament\Resources\Pemiliks\Pages;

use App\Filament\Resources\Pemiliks\PemilikResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemiliks extends ListRecords
{
    protected static string $resource = PemilikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
