<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Broadcast;

class CustomBroadcastController extends Controller
{
    public function authenticateClient(Request $request)
    {
        // Lấy token từ header
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token is missing'], 401);
        }

        // Kiểm tra token trong Redis
        $tokenData = Redis::hgetall('tokens:' . $token);

        if (empty($tokenData)) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $user = Candidate::find($tokenData['id_code']);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        auth()->setUser($user);

        return Broadcast::auth($request, $user);
    }

    public function authenticateAdmin(Request $request)
    {
        // Lấy token từ header
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token is missing'], 401);
        }

        // Kiểm tra token trong Redis
        $tokenData = Redis::hgetall('tokens:' . $token);

        if (empty($tokenData)) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        // Lấy thông tin admin
        $user = Admin::where('id', $tokenData['user_id'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        auth()->setUser($user);

        return Broadcast::auth($request, $user);
    }
}
