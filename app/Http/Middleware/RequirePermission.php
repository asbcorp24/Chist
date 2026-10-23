<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
class RequirePermission {public function handle(Request $r,Closure $next,string $permission){abort_unless($r->user()&&$r->user()->canDo($permission),403);return $next($r);}}
