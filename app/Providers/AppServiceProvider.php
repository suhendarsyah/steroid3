<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Filament::serving(function () {
        //     Filament::registerUserMenuItems([
        //         UserMenuItem::make()
        //             ->label('Logout')
        //             ->icon('heroicon-o-arrow-right-on-rectangle')
        //             ->url(route('filament.admin.auth.logout'))
        //             ->sort(99),
        //     ]);
        // });
    }
}
