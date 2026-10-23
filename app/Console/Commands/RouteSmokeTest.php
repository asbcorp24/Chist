<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Throwable;

class RouteSmokeTest extends Command
{
    protected $signature = 'chist:smoke-routes {--show-ok : Show successful routes} {--stop-on-error : Stop on first 5xx/exception} {--report=storage/logs/route-smoke.txt : Write text report}';
    protected $description = 'Smoke-test all safe GET/HEAD application routes as Super Admin';

    private array $tableMap = [
        'student'=>'students','group'=>'study_groups','lesson'=>'schedule_entries','subject'=>'subjects',
        'teacher'=>'teachers','period'=>'academic_periods','practice'=>'practice_placements',
        'placement'=>'practice_placements','building'=>'dormitory_buildings','room'=>'dormitory_rooms','event'=>'upbringing_events','council'=>'upbringing_councils','reader'=>'library_readers','inventory'=>'library_inventories','act'=>'dormitory_asset_acts','activity'=>'student_activities','document'=>'official_documents',
        'book'=>'library_books','loan'=>'library_loans','user'=>'users','role'=>'roles',
        'sheet'=>'attestation_sheets','attempt'=>'attestation_attempts','gia'=>'gia_campaigns',
        'record'=>'graduation_records','submission'=>'homework_submissions','plan'=>'curriculum_plans',
        'load'=>'teaching_load_plans','order'=>'facility_work_orders','request'=>'service_requests',
        'audit'=>'quality_audits','survey'=>'quality_surveys','meeting'=>'methodical_meetings',
        'case'=>'office_cases','file'=>'service_request_files','menu'=>'meal_menus',
        'campaign'=>'practice_campaigns','organization'=>'practice_organizations',
        'notification'=>'practice_notifications','entry'=>'practice_diary_entries',
    ];

    public function handle(): int
    {
        $user = User::query()->where('is_active',true)
            ->whereHas('roles',fn($q)=>$q->where('code','super_admin'))->orderBy('id')->first();
        if (!$user) { $this->error('No active super_admin user found.'); return self::FAILURE; }
        Auth::login($user);

        $app = app();
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $ok=$redirect=$skip=$error=0; $rows=[];

        foreach (Route::getRoutes() as $route) {
            $methods=$route->methods();
            if (!in_array('GET',$methods,true) && !in_array('HEAD',$methods,true)) continue;
            $uri=$route->uri();
            if (str_starts_with($uri,'_ignition') || str_starts_with($uri,'sanctum/')) continue;

            [$url,$why]=$this->resolveUri($uri);
            if (!$url) { $skip++; $rows[]=['SKIP',$uri,'-',$why]; continue; }

            try {
                $request=Request::create('/'.$url,'GET',[],[],[],[
                    'HTTP_HOST'=>parse_url(config('app.url'),PHP_URL_HOST) ?: 'localhost',
                    'HTTP_ACCEPT'=>'text/html,application/xhtml+xml',
                ]);
                $request->setUserResolver(fn()=>$user);
                $response=$kernel->handle($request);
                $code=$response->getStatusCode();
                $kernel->terminate($request,$response);

                if ($code>=500) {
                    $error++; $status='ERROR';
                    $detail=$this->responseError($response);
                }
                elseif ($code>=300 && $code<400) { $redirect++; $status='REDIRECT'; }
                else { $ok++; $status='OK'; }

                if ($status!=='OK' || $this->option('show-ok')) $rows[]=[$status,$uri,(string)$code,$status==='ERROR'?($url.' | '.$detail):$url];
                if ($status==='ERROR' && $this->option('stop-on-error')) break;
            } catch (Throwable $e) {
                $error++; $rows[]=['EXCEPTION',$uri,'500',get_class($e).': '.$e->getMessage()];
                report($e);
                if ($this->option('stop-on-error')) break;
            }
        }

        $this->table(['Status','Route','HTTP','Resolved / reason'],$rows);
        $this->newLine();
        $this->info("OK: $ok | Redirect: $redirect | Skip: $skip | Errors: $error");
        $report=(string)$this->option('report');
        if ($report) {
            $path=str_starts_with($report,'/')?$report:base_path($report);
            $dir=dirname($path); if(!is_dir($dir)) @mkdir($dir,0775,true);
            $lines=["Chist route smoke test ".date('c'),"OK: $ok | Redirect: $redirect | Skip: $skip | Errors: $error",""];
            foreach($rows as $row)$lines[]=implode("\t",array_map(fn($v)=>str_replace(["\r","\n"],' ',(string)$v),$row));
            file_put_contents($path,implode(PHP_EOL,$lines).PHP_EOL);
            $this->comment("Report: $path");
        }
        if ($skip) $this->comment('SKIP means a required route parameter could not be resolved from the current database.');
        return $error ? self::FAILURE : self::SUCCESS;
    }

    private function responseError($response): string
    {
        try {
            $body=(string)$response->getContent();
            foreach([
                '/<div class="exception-message[^>]*>(.*?)<\\/div>/si',
                '/<title>(.*?)<\\/title>/si',
                '/SQLSTATE\\[[^<\\n]+/i'
            ] as $pattern) {
                if (preg_match($pattern,$body,$m)) {
                    $x=trim(strip_tags(html_entity_decode($m[1]??$m[0])));
                    if ($x!=='') return mb_substr(preg_replace('/\\s+/u',' ',$x),0,500);
                }
            }
        } catch (Throwable $e) {}
        return 'HTTP 500; see storage/logs/laravel.log';
    }

    private function resolveUri(string $uri): array
    {
        $failed=null;
        $resolved=preg_replace_callback('/\{([^}]+)\}/',function($m)use(&$failed){
            $raw=$m[1]; $optional=str_ends_with($raw,'?'); $name=rtrim($raw,'?');
            if($name==='direction') return 'incoming'; if($name==='form') return 'diary'; if($name==='token') { try { return (string)(DB::table('practice_organizations')->whereNotNull('partner_token')->value('partner_token') ?: ''); } catch(Throwable $e){ return ''; } }
            $table=$this->tableMap[$name]??null;
            if (!$table) { if($optional)return ''; $failed="no resolver for {{$name}}"; return $m[0]; }
            try { $id=DB::table($table)->orderBy('id')->value('id'); }
            catch(Throwable $e){ $failed="table $table unavailable"; return $m[0]; }
            if (!$id) { if($optional)return ''; $failed="no row in $table"; return $m[0]; }
            return (string)$id;
        },$uri);
        if ($failed || str_contains($resolved,'{')) return [null,$failed?:'unresolved parameter'];
        return [trim(preg_replace('#/+#','/',$resolved),'/'),null];
    }
}
