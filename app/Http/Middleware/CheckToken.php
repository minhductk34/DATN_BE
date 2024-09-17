<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();



        if (!$token) {
            return response()->json([
                'success' => false,
                'status' => '401',
                'message' => 'You are not logged in or the token is not provided.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $userId = Redis::hget('tokens:' . $token, 'user_id');
        $expiresAt = Redis::hget('tokens:' . $token, 'expires_at');

        if (!$userId || !$expiresAt || now()->timestamp > $expiresAt) {
            return response()->json([
                'success' => false,
                'status' => '403',
                'message' => 'Invalid or expired tokens'
            ], Response::HTTP_FORBIDDEN);
        }

        $storedToken = Redis::hget('auth:' . $userId, 'token');
        if ($storedToken !== $token) {
            return response()->json([
                'success' => false,
                'status' => '403',
                'message' => 'The login session was canceled because you logged in from elsewhere'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

}
