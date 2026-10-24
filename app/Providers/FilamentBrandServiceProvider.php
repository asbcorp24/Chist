<?php
namespace App\Providers;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
class FilamentBrandServiceProvider extends ServiceProvider{
public function boot():void{
Filament::serving(function(){
Filament::registerStyles(['bootstrap'=>'/vendor/bootstrap/bootstrap.min.css','chist-ui'=>'/css/chist-ui.css','chist-filament-theme'=>'/css/filament-chist.css']);
Filament::registerScripts(['bootstrap'=>'/vendor/bootstrap/bootstrap.bundle.min.js']);
Filament::registerRenderHook('body.start',fn():string=>view('filament.chist-shell')->render());
});
}}
