<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PermissionCheckerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (auth()->user()->role_id == 1) {
                return $next($request);
            }
            $permissions = collect(auth()->user()->permissions);
            if ($permissions->contains($request->route()->getName())) {
                return $next($request);
            } else {
                Log::warning('User permission denied! username: '.auth()->user()->username.' --- Url: '.$request->url());
                abort(403);
            }
        }
    }
}
