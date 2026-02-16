<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // protected function afterCreate(): void
    // {
    //     $this->record->syncRoles([$this->data['role_name']]);
    // }
protected function afterCreate(): void
{
    if ($this->roleName) {
        $this->record->syncRoles([$this->roleName]);
    }
}

   protected function mutateFormDataBeforeCreate(array $data): array
{
    // 🔥 Ambil role lalu hapus dari data model
    $this->roleName = $data['role_name'] ?? null;

    unset($data['role_name']);

    return $data;
}



}
