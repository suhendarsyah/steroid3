<?php

namespace App\Services\Dashboard\KepalaDinas;

use App\Models\Upt;

class JumlahUptService
{
    public function get(): int
    {
        return Upt::count();
    }
}
