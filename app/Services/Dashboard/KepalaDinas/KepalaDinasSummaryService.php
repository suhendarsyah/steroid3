<?php

namespace App\Services\Dashboard\KepalaDinas;

use App\Models\User;

class KepalaDinasSummaryService
{
    public function get(): array
    {
        return [
            'total_user' => User::count(),
            'updated_at' => now()->toDateTimestring(),
        ];
    }

}
