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
    try {
        $credentials = $request->only('username', 'password');

        if (empty($credentials['username']) || empty($credentials['password'])) {
            return response()->json([
                'success' => false,
                'status' => 400,
                'data' => [],
                'warning' => 'Username và password là bắt buộc.'
            ], 400);
        }

        $admin = Admin::where('name', $credentials['username'])->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'data' => [],
                'warning' => 'Tài khoản không tồn tại.'
            ], 404);
        }

        if (!Hash::check($credentials['password'], $admin->Password)) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'data' => [],
                'warning' => 'Mật khẩu không chính xác.'
            ], 401);
        }

        // Thêm kiểm tra kết nối Redis
        if (!$this->checkRedisConnection()) {
            return response()->json([
                'success' => false,
                'status' => 503,
                'data' => [],
                'warning' => 'Không thể kết nối đến hệ thống lưu trữ, vui lòng thử lại sau.'
            ], 503);
        }

        // Thực hiện truy vấn với cơ chế retry khi lỗi kết nối Redis xảy ra
        $existingToken = $this->retryRedisOperation(function () use ($admin) {
            return Redis::hget('auth:' . $admin->id, 'token');
        });

        if ($existingToken) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'data' => [],
                'warning' => 'Tài khoản này đã đăng nhập từ nơi khác.'
            ], 403);
        }

        // Tạo token mới
        $token = Str::random(60);
        $expiresAt = now()->addHours(1)->timestamp;
        $ttl = now()->addHours(1)->diffInSeconds(now());

        $tokenData = [
            'user_id' => $admin->id,
            'expires_at' => $expiresAt,
        ];

        // Thêm retry cho thao tác lưu trữ
        $this->retryRedisOperation(function () use ($token, $tokenData, $admin, $ttl) {
            Redis::hmset('tokens:' . $token, $tokenData);
            Redis::hmset('auth:' . $admin->id, ['token' => $token, 'expires_at' => $ttl]);
            Redis::expire('tokens:' . $token, $ttl);
            Redis::expire('auth:' . $admin->id, $ttl);
        });

        $data = [
            'id' => $admin->id,
            'username' => $admin->Name,
        ];

        return response()->json([
            'success' => true,
            'status' => 200,
            'expires_at' => $expiresAt,
            'token' => $token,
            'data' => $data,
        ], 200);

    } catch (\Exception $e) {
        

        return response()->json([
            'success' => false,
            'status' => 500,
            'data' => [],
            'warning' => 'Đã có lỗi xảy ra, vui lòng thử lại sau.',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Kiểm tra kết nối đến Redis
 */
private function checkRedisConnection()
{
    try {
        Redis::ping();
        return true;
    } catch (\Exception $e) {
    
        return false;
    }
}

/**
 * Thực hiện thao tác với Redis và tự động retry khi xảy ra lỗi
 */
private function retryRedisOperation(callable $operation, $maxRetries = 3)
{
    $attempts = 0;
    while ($attempts < $maxRetries) {
        try {
            return $operation();
        } catch (\Exception $e) {
            $attempts++;
            
            if ($attempts >= $maxRetries) {
              
                throw $e;
            }
            // Đợi một thời gian ngắn trước khi thử lại (200ms)
            usleep(200000); // 200,000 microsecond = 200ms
        }
    }
    return null;
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
