<?php

namespace App\Http\Middleware;

use App\Traits\ApiReturnFormatTrait;
use Closure;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    use ApiReturnFormatTrait;

    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return $this->responseWithError(__('jwt.invalid_token'), [], 401);
            } elseif ($e instanceof TokenExpiredException) {
                return $this->responseWithError(__('jwt.token_is_expired'), [], 401);
            } else {
                return $this->responseWithError(__('jwt.authorization_toke_not_found'), [], 401);
            }
        }
        if ($user) {
            return $next($request);
        } else {
            return $this->responseWithError(__('jwt.invalid_token'), [], 401);
        }
    }
}
