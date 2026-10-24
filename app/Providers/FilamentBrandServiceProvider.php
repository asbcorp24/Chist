<?php
namespace App\Providers;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
class FilamentBrandServiceProvider extends ServiceProvider{
public function boot():void{
Filament::serving(function(){
Filament::registerStyles(['chist-filament-theme'=>'/css/filament-chist.css']);
Filament::registerRenderHook('body.start', fn (): string => '<a class="chist-filament-return" href="'.route('upbringing.index').'">← Воспитательная работа</a>');
});
}}
