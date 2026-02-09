<?php

namespace App\Filament\Resources\ObjekProduksis\Pages;

use App\Filament\Resources\ObjekProduksis\ObjekProduksiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditObjekProduksi extends EditRecord
{
    protected static string $resource = ObjekProduksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
