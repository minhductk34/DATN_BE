<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Models\Lecturers;
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
                'warning'=> '',
                'error' => ''
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
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
        try {
            $validated = $request->validate([
                'Idcode' => 'required|string|unique:lecturers',
                'Fullname' => 'required|string|max:255',
                'Email' => 'required|string|email|max:255|unique:lecturers',
                'Profile' => 'nullable|string',
                'Status' => 'required|in:Active,Inactive',
            ]);

            // Tạo mới một Lecturer
            $lecturer = Lecturer::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Lecturer created successfully!',
                'data' => $lecturer,
                'warning'=> '',
                'error' => ''
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => 'validation error',
                'error' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $lecturer = Lecturer::find($id);

            if (!$lecturer) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'giảng viên không tồn tại',
                    'error' => '404 not found!'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $lecturer,
                'warning'=> '',
                'error' => ''
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lecturer $lecturers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lecturer = Lecturer::find($id);

        if (!$lecturer) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => '',
                'warning'=> 'Không tìm thấy giảng viên',
                'error' => '404 not found!'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'Fullname' => 'required|string|max:255',
                'Email' => 'required|string|email|max:255|unique:lecturers,Email,' . $id . ',Idcode',
                'Profile' => 'nullable|string',
                'Status' => 'required|in:Active,Inactive',
            ]);

            $lecturer->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Lecturer updated successfully!',
                'data' => $lecturer,
                'warning'=> '',
                'error' => ''
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => 'validation error',
                'error' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lecturer = Lecturer::find($id);

        if (!$lecturer) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => '',
                'warning'=> 'Không tìm thấy giảng viên',
                'error' => '404 not found!'
            ], 404);
        }

        try {
            $lecturer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lecturer deleted successfully!',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
