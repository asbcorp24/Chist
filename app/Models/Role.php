<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Role extends Model {
 protected $fillable=['code','name','is_system','level'];
 protected $casts=['is_system'=>'boolean'];
 public function permissions(){return $this->belongsToMany(Permission::class);}
 public function users(){return $this->belongsToMany(User::class);}
}
