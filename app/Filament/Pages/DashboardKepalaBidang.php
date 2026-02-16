<?php

namespace App\Filament\Pages;
use UnitEnum;
use BackedEnum;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Page;

class DashboardKepalaBidang extends Page

{
    

    protected static UnitEnum|string|null $navigationGroup = 'Dashboard';


    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Dashboard Bidang';

    protected static ?int $navigationSort = 2;

    public function getView(): string
{
    return 'filament.pages.dashboard-kepala-bidang';
}

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('kepala_bidang');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('kepala_bidang') ?? false;
    }

    public function getTitle(): string
    {
        return 'Dashboard Kepala Bidang';
    }

    public function getSubheading(): ?string
    {
        return 'Monitoring aktivitas UPT pada bidang Anda.';
    }
}
