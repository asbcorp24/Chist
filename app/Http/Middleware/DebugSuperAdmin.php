<?php
namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class DebugSuperAdmin
{
    public function handle($request, Closure $next)
    {
        if (config('app.debug') && !Auth::check()) {
            $user = User::query()
                ->where('is_active', true)
                ->whereHas('roles', fn ($q) => $q->where('code', 'super_admin'))
                ->orderBy('id')
                ->first();

            if ($user) {
                Auth::login($user);
                $request->session()->regenerate();
            }
        }

        return $next($request);
    }
}
