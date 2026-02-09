<?php

namespace App\Filament\Resources\MasterKegiatanTeknis\Pages;

use App\Filament\Resources\MasterKegiatanTeknis\MasterKegiatanTeknisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterKegiatanTeknis extends ListRecords
{
    protected static string $resource = MasterKegiatanTeknisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
