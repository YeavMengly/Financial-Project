<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Traits\ApiReturnFormatTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKeyMiddleware
{
    use ApiReturnFormatTrait;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('apiKey')) {
            $keys = ApiKey::pluck('key')->toArray();
            if (in_array($request->header('apiKey'), $keys)) {
                return $next($request)->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers',
                        'X-Requested-With, Content-Type, X-Token-Auth, Authorization');
            }
        }

        return $this->responseWithError(__('jwt.api_key_invalid'), [], 403);
        //return $next($request);
    }
}
