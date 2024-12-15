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

        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json([
                'success' => false,
                'status' => '401',
                'message' => 'Bạn đang thiếu token đăng nhập.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
