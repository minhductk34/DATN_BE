<?php

namespace App\Http\Controllers;

use App\Http\Resources\AutherResource;
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

    if (!Hash::check($credentials['password'], $admin->Password)) {
        return response()->json([
            'success' => false,
            'status' => '401',
            'data' => [],
            'warning' => 'Mật khẩu không chính xác'
        ], 401);
    }

    // Kiểm tra xem người dùng đã có token hợp lệ chưa
    $existingToken = Redis::hget('auth:' . $admin->id, 'token');
    if ($existingToken) {
        return response()->json([
            'success' => false,
            'status' => '403',
            'data' => [],
            'warning' => 'Tài khoản này đã đăng nhập từ nơi khác'
        ], 403);
    }

    // Tạo khóa lưu trữ token và thông tin người dùng
    $token = Str::random(60);
    $expiresAt = now()->addHours(1)->timestamp;
    $ttl = now()->addHours(1)->diffInSeconds(now());

    // Lưu thông tin token vào Redis hash
    Redis::hset('tokens:' . $token, 'user_id', $admin->id);
    Redis::hset('tokens:' . $token, 'expires_at', $expiresAt);

    // Lưu token vào Redis hash với TTL
    Redis::hset('auth:' . $admin->id, 'token', $token);
    Redis::hset('auth:' . $admin->id, 'expires_at', $expiresAt);

    // Đặt TTL cho các hash
    Redis::expire('tokens:' . $token, $ttl);
    Redis::expire('auth:' . $admin->id, $ttl);
    $data = [
        'id' => $admin->id,
        'username' => $admin->Name,
    ];

    return response()->json([
        'success' => true,
        'status' => '200',
        'expires_at' => $expiresAt,
        'token' => $token,
        'data' =>$data,
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
