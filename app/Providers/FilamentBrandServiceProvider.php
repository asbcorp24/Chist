<?php
namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class FilamentBrandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerTheme(mix('css/filament-chist.css'));
        });
    }
}
