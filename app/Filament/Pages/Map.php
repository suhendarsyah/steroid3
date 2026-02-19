<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Map extends Page
{
    protected string $view = 'filament.pages.map';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Peta Monitoring';

    public static function canAccess(): bool
    {
        return Auth::user()?->hasAnyRole([
            'super_admin',
            'kepala_dinas',
        ]);
    }
}
