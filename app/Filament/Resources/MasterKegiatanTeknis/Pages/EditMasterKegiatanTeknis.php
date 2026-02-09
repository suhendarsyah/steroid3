<?php

namespace App\Filament\Resources\MasterKegiatanTeknis\Pages;

use App\Filament\Resources\MasterKegiatanTeknis\MasterKegiatanTeknisResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterKegiatanTeknis extends EditRecord
{
    protected static string $resource = MasterKegiatanTeknisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
