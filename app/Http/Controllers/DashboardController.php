<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller {
 public function index(){return view('dashboard',['period'=>DB::table('academic_periods')->join('academic_years','academic_years.id','=','academic_periods.academic_year_id')->select('academic_periods.*','academic_years.name as year_name')->orderByDesc('starts_on')->first(),'groups'=>DB::table('study_groups')->where('active',1)->orderBy('name')->get()]);}
}
