<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;use App\Services\{CurriculumScheduleBinder,PeriodGuard};
class RectorExchangeController extends Controller{
 private function ok(Request $r){abort_unless($r->user()->isSuperAdmin()||$r->user()->hasRole('admin')||$r->user()->hasRole('academic_head'),403);}
 public function index(Request $r){$this->ok($r);$p=$r->attributes->get('academic_period');$runs=DB::table('rector_exchange_runs')->where('academic_period_id',$p->id)->orderByDesc('id')->limit(50)->get();$missing=['groups'=>DB::table('study_groups')->where('active',1)->whereNull('source_id')->count(),'teachers'=>DB::table('teachers')->where('active',1)->whereNull('source_id')->count(),'subjects'=>DB::table('subjects')->whereNull('source_id')->count(),'types'=>DB::table('study_types')->whereNull('source_id')->count(),'rooms'=>DB::table('rooms')->whereNull('source_id')->count()];return view('academic-office.rector-exchange',compact('p','runs','missing'));}
 public function bind(Request $r,CurriculumScheduleBinder $b){$this->ok($r);$p=$r->attributes->get('academic_period');PeriodGuard::writable($p->id);$x=$b->bindPeriod($p->id,$r->boolean('rebuild'));return back()->with('success',"КТП распределено: программ {$x['plans']}, новых привязок {$x['bound']}, не хватило занятий для {$x['unassigned']} часов/позиций.");}
}