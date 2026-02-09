<?php

namespace App\Filament\Resources\DataTeknis\Pages;

use App\Filament\Resources\DataTeknis\DataTeknisResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\DataTeknis;

class EditDataTeknis extends EditRecord
{
    protected static string $resource = DataTeknisResource::class;

    protected function authorizeAccess(): void
    {
        $user = auth()->user();
        $record = $this->getRecord();

        // Super admin boleh edit semua
        if ($user->hasRole('super_admin')) {
            return;
        }

        // UPT hanya boleh edit data unit layanannya
        if (
            $user->hasRole('upt') &&
            $record instanceof DataTeknis &&
            $record->objekProduksi &&
            $record->objekProduksi->unitLayanan &&
            $record->objekProduksi->unitLayanan->upt_id === $user->upt_id
        ) {
            return;
        }

        abort(403);
    }
}
