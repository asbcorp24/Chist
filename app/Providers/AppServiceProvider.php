<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider {
 public function register(){}
 public function boot(){
  Paginator::useBootstrapFive();
  View::composer('reports.*',function($view){
   $organizationSettings=null;
   if(Schema::hasTable('organization_settings')) $organizationSettings=DB::table('organization_settings')->first();
   $view->with('organizationSettings',$organizationSettings);
  });
 }
}