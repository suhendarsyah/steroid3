<?php

namespace App\Filament\Resources\DataTeknis\Pages;

use App\Filament\Resources\DataTeknis\DataTeknisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDataTeknis extends ListRecords
{
    protected static string $resource = DataTeknisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->visible(fn () => auth()->user()->hasRole(['perencanaan','super_admin','upt'])),
        ];
    }
}
