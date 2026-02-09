<?php

namespace App\Filament\Resources\Pemiliks\Pages;

use App\Filament\Resources\Pemiliks\PemilikResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemilik extends EditRecord
{
    protected static string $resource = PemilikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
