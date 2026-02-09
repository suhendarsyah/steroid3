<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DalamAngkaButton extends Widget
{
    protected string $view = 'filament.widgets.dalam-angka-button';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['kepala_dinas', 'super_admin']);
    }
}
