<?php
namespace App\Services;
use Illuminate\Support\Facades\Crypt;use Illuminate\Support\Facades\Hash;
class CredentialService{
public function password(int $length=10):string{$alphabet='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';$s='';for($i=0;$i<$length;$i++)$s.=$alphabet[random_int(0,strlen($alphabet)-1)];return $s;}
public function login(string $last,string $first,int $id):string{$base=$this->slug($last).'.'.$this->slug($first);return trim($base,'.').$id;}
private function slug(string $s):string{$map=['а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya'];$s=strtr(mb_strtolower($s),$map);return preg_replace('/[^a-z0-9]+/','',$s);}
public function pack(string $plain):array{return ['password'=>Hash::make($plain),'password_encrypted'=>Crypt::encryptString($plain),'password_changed_at'=>now(),'must_change_password'=>true];}
public function reveal(?string $cipher):?string{if(!$cipher)return null;try{return Crypt::decryptString($cipher);}catch(\Throwable $e){return null;}}
}