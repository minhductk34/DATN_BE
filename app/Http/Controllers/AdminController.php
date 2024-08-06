<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (empty($credentials['username']) || empty($credentials['password'])) {
            return response()->json([
                'success' => false,
                'status' => '400',
                'data' => [],
                'warning' => 'Username và password là bắt buộc'
            ], 400);
        }

        $admin = Admin::where('name', $credentials['username'])->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => [],
                'warning' => 'Tài khoản không tồn tại'
            ], 404);
        }

        if (!Hash::check($credentials['password'], $admin->password)) {
            return response()->json([
                'success' => false,
                'status' => '401',
                'data' => [],
                'warning' => 'Mật khẩu không chính xác'
            ], 401);
        }

        $token = Str::random(60);
        $expiresAt = now()->addHours(1)->timestamp;
        $ttl = now()->addHours(1)->diffInSeconds(now());

        Redis::setex('auth_token_' . $token, $ttl, json_encode([
            'user_id' => $admin->id,
            'expires_at' => $expiresAt,
        ]));

        return response()->json([
            'success' => true,
            'status' => '200',
            'expires_at' => $expiresAt,
            'token' => $token,
            'data' => $admin,
        ], 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
