<?php

namespace App\Filament\Resources\DataTeknis\Pages;

use App\Filament\Resources\DataTeknis\DataTeknisResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDataTeknis extends CreateRecord
{
    protected static string $resource = DataTeknisResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['upt_id'] = auth()->user()->upt_id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

}

