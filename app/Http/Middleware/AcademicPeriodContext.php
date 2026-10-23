<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Illuminate\Support\Facades\{DB,View};
class AcademicPeriodContext {
 public function handle(Request $r,Closure $next){$periods=DB::table('academic_periods as p')->join('academic_years as y','y.id','=','p.academic_year_id')->select('p.*','y.name as year_name')->orderByDesc('p.starts_on')->get();$id=(int)$r->session()->get('academic_period_id',0);$current=$periods->firstWhere('id',$id)?:$periods->firstWhere('is_current',1)?:$periods->first();if($current)$r->session()->put('academic_period_id',$current->id);View::share('academicPeriods',$periods);View::share('academicPeriod',$current);$r->attributes->set('academic_period',$current);return $next($r);}
}
