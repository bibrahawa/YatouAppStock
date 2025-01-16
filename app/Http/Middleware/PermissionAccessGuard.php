<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\FeatureNotAllowedException;
use Auth;

class PermissionAccessGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (Auth::check() && Auth::user()->can($permission)) {
            return $next($request);
        }

        abort(403, 'Action non autorisée');
    }
}
