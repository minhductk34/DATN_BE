<?php

namespace App\Http\Controllers;

use App\Http\Resources\AutherResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
            Log::debug('Đã nhận được yêu cầu đăng nhập', ['request' => $request->all()]);

            $credentials = $request->only('username', 'password');

            if (empty($credentials['username']) || empty($credentials['password'])) {
                // Log::message('Missing username or password');
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'data' => [],
                    'message' => 'username và password là bắt buộc.'
                ], 400);
            }

            Log::debug('Tìm kiếm quản trị viên', ['username' => $credentials['username']]);
            $admin = Admin::where('name', $credentials['username'])->first();

            if (!$admin) {
                // Log::message('Admin not found', ['username' => $credentials['username']]);
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'data' => [],
                    'message' => 'Tài khoản không tồn tại.'
                ], 404);
            }

            if (!Hash::check($credentials['password'], $admin->password)) {
                // Log::message('Incorrect password', ['username' => $credentials['username']]);
                return response()->json([
                    'success' => false,
                    'status' => 401,
                    'data' => [],
                    'message' => 'Mật khẩu không chính xác.'
                ], 401);
            }

            if (!$this->checkRedisConnection()) {
                Log::error('Kết nối Redis không thành công');
                return response()->json([
                    'success' => false,
                    'status' => 503,
                    'data' => [],
                    'message' => 'Không thể kết nối đến hệ thống lưu trữ, vui lòng thử lại sau.'
                ], 503);
            }

            Log::debug('Kiểm tra mã thông báo hiện có trong Redis');
            $existingToken = $this->retryRedisOperation(function () use ($admin) {
                return Redis::hget('auth:' . $admin->id, 'token');
            });

            if ($existingToken) {
                // Log::message('User already logged in from another location', ['user_id' => $admin->id]);
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'data' => [],
                    'message' => 'Tài khoản này đã đăng nhập từ nơi khác.'
                ], 403);
            }

            $token = Str::random(60);
            $expiresAt = now()->addHours(1)->timestamp;
            $ttl = now()->addHours(1)->diffInSeconds(now());

            $tokenData = [
                'user_id' => $admin->id,
                'expires_at' => $expiresAt,
            ];

            Log::debug('Lưu trữ token trong Redis', ['token' => $token, 'ttl' => $ttl]);
            $this->retryRedisOperation(function () use ($token, $tokenData, $admin, $ttl) {
                Redis::hmset('tokens:' . $token, $tokenData);
                Redis::hmset('auth:' . $admin->id, ['token' => $token, 'expires_at' => $ttl]);
                Redis::expire('tokens:' . $token, $ttl);
                Redis::expire('auth:' . $admin->id, $ttl);
            });

            $data = [
                'id' => $admin->id,
                'username' => $admin->name,
            ];

            Log::info('Người dùng đã đăng nhập thành công', ['user_id' => $admin->id]);
            return response()->json([
                'success' => true,
                'status' => 200,
                'expires_at' => $expiresAt,
                'token' => $token,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Login failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'status' => 500,
                'data' => [],
                'message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout() {
        try {
            Redis::flushall();

            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Logout error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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
