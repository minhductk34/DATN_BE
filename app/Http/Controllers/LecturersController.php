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
                'data' => [
                    'candidate' => $lecturers,
                ],
                'message' => 'Candidate created successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Internal server error while processing your request'
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
                'profile' => 'nullable|string',
                'status' => 'boolean',
            ]);

            $lecturer = Lecturer::create($validated);
            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'candidate' => $lecturer,
                ],
                'message' => 'Lecturer created successfully!'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'message' => 'Validation error',
                'error' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Internal server error while processing your request'
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
                'data' => [
                    'candidate' => $lecturer,
                ],
                'message' => 'Candidate show successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Internal server error while processing your request'
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
                'profile' => 'nullable|string',
                'status' => 'boolean',
            ]);

            $lecturer->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Lecturer updated successfully!',
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
                'message' => 'Internal server error while processing your request'
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
                'message' => 'Lecturer deleted successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Internal server error while processing your request'
            ], 500);
        }
    }
}
