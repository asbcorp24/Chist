<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable; use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
 use Notifiable;
 protected $fillable=['name','email','password','teacher_id','student_id','is_active'];
 protected $hidden=['password','remember_token']; protected $casts=['is_active'=>'boolean'];
 public function roles(){return $this->belongsToMany(Role::class);}
 public function teacher(){return $this->belongsTo(Teacher::class);}
 public function student(){return $this->belongsTo(Student::class);}
 public function hasRole(string $code):bool{return $this->roles()->where('code',$code)->exists();}
 public function isSuperAdmin():bool{return $this->hasRole('super_admin');}
 public function canDo(string $permission):bool {
   if($this->isSuperAdmin()) return true;
   return $this->roles()->whereHas('permissions',fn($q)=>$q->where('code',$permission))->exists();
 }
 public function maxRoleLevel():int{return (int)$this->roles()->max('level');}
}
