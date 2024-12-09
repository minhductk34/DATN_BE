<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LecturersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $lecturers = Lecturer::all();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $lecturers,
                'message' => 'Thí sinh đã được tạo thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'idcode' => 'required|string|unique:lecturers',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:lecturers',
                'profile' => 'file',
            ]);
            $validated['status'] = true;
            $lecturer = Lecturer::create($validated);
            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $lecturer,
                'message' => 'Đã tạo giảng viên thành công!'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'message' => 'Lỗi xác thực',
                'error' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $idcode)
    {
        try {
            $lecturer = Lecturer::find($idcode);

            if (!$lecturer) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'Giảng viên không tồn tại',
                    'error' => '404 not found!'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $lecturer,
                'message' => 'Thí sinh đã trình bày thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $idcode)
    {
        $lecturer = Lecturer::find($idcode);

        if (!$lecturer) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => '',
                'message' => 'Không tìm thấy giảng viên',
                'error' => '404 not found!'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:lecturers,email,' . $idcode . ',idcode',
                'profile' => 'file',
                'status' => 'boolean',
            ]);

            $lecturer->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Giảng viên đã cập nhật thành công!',
                'data' => $lecturer,
                'error' => ''
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => "422",
                'data' => '',
                'error' => $e->getMessage(),
                'message' => 'Validation error'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idcode)
    {
        $lecturer = Lecturer::find($idcode);

        if (!$lecturer) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => '',
                'message' => 'Không tìm thấy giảng viên',
                'error' => '404 not found!'
            ], 404);
        }

        try {
            $lecturer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Giảng viên đã bị xóa thành công!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn'
            ], 500);
        }
    }
}
