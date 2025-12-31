<?php

namespace app\Http\Middleware;

use app\Support\Auth;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        Auth::check();

        // Continua para o controller
        return $next($request);
    }
}