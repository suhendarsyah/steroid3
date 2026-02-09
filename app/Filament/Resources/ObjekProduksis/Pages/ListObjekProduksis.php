<?php

namespace App\Filament\Resources\ObjekProduksis\Pages;

use App\Filament\Resources\ObjekProduksis\ObjekProduksiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListObjekProduksis extends ListRecords
{
    protected static string $resource = ObjekProduksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
