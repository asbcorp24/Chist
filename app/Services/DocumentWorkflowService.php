<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
class DocumentWorkflowService{
public function event($id,$uid,$event,$details=null){DB::table('official_document_events')->insert(['official_document_id'=>$id,'user_id'=>$uid,'event'=>$event,'details'=>$details,'created_at'=>now(),'updated_at'=>now()]);}
public function canAct($user,$step){return $user->isSuperAdmin()||$user->canDo('edo.approve')||($step->assignee_user_id&&$step->assignee_user_id==$user->id)||($step->assignee_role&&$user->hasRole($step->assignee_role));}
public function activateNext($doc){$active=DB::table('document_routes')->where('official_document_id',$doc)->where('status','active')->exists();if($active)return;$next=DB::table('document_routes')->where('official_document_id',$doc)->where('status','waiting')->orderBy('step')->first();if($next)DB::table('document_routes')->where('id',$next->id)->update(['status'=>'active','updated_at'=>now()]);else DB::table('official_documents')->where('id',$doc)->where('status','routing')->update(['status'=>'approved','updated_at'=>now()]);}
public function immutableSnapshot($doc){$d=DB::table('official_documents')->find($doc);$v=DB::table('document_versions')->where('official_document_id',$doc)->where('version',$d->current_version)->first();$files=DB::table('document_attachments')->where('official_document_id',$doc)->get();$route=DB::table('document_routes')->where('official_document_id',$doc)->orderBy('step')->get();return ['document'=>$d,'version'=>$v,'attachments'=>$files,'route'=>$route,'fixed_at'=>now()->toIso8601String()];}
}