<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder; use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder {public function run(){foreach(['admin'=>'Администратор','curator'=>'Классный руководитель','teacher'=>'Преподаватель','starosta'=>'Староста'] as $code=>$name)DB::table('roles')->updateOrInsert(['code'=>$code],['name'=>$name,'created_at'=>now(),'updated_at'=>now()]);}}
