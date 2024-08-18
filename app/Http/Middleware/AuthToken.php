<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class AuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = str::random(60);
        Redis::set('auth_token_' . $token, json_encode([
            'user_id' => $request->user()->id,
            'expires_at' => now()->addHours(1),
        ]));
        $response = $next($request);
        $response->headers->set('Authorization', 'Bearer ' . $token);
        return $response;
    }
}
