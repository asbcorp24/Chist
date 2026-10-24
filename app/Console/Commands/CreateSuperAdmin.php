<?php
namespace App\Console\Commands;
use App\Models\{User,Role}; use Illuminate\Console\Command; use Illuminate\Support\Facades\Hash;
class CreateSuperAdmin extends Command {
 protected $signature='chist:superadmin {--name=} {--email=}'; protected $description='Создать или назначить первого Super Admin';
 public function handle(){
  $name=$this->option('name')?:$this->ask('Имя');$email=$this->option('email')?:$this->ask('E-mail');$password=$this->secret('Пароль (минимум 8 символов)');
  if(strlen((string)$password)<8){$this->error('Пароль слишком короткий');return self::FAILURE;}
  $user=User::firstOrNew(['email'=>$email]);$user->name=$name;$user->password=Hash::make($password);$user->is_active=true;$user->save();
  $role=Role::firstOrCreate(['code'=>'super_admin'],['name'=>'Super Admin','is_system'=>true,'level'=>100]);
  if(!$role->is_system||$role->level<100){$role->forceFill(['is_system'=>true,'level'=>100])->save();}
  $user->roles()->syncWithoutDetaching([$role->id]);
  $this->info('Super Admin готов: '.$email);return self::SUCCESS;
 }
}
