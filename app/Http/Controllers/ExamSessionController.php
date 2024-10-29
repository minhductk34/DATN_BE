<?php

namespace App\Http\Controllers;

use App\Models\Exam_session;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExamSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $examSessions = Exam_session::all();

        return response()->json([
            'success' => true,
            'status' => '200',
            'data' => $examSessions,
            'warning' => '',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Lưu một tài nguyên mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'Name' => 'required|string|max:255',
                'TimeStart' => 'required|date',
                'TimeEnd' => 'required|date',
            ]);

            $examSession = Exam_session::create($validatedData);

            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $examSession,
                'warning' => '',
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => $e->errors(),
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
        $examSession = Exam_session::find($id);

        if (!$examSession) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => [],
                'warning' => 'Exam session does not exist',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => '200',
            'data' => $examSession,
            'warning' => '',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $examSession = Exam_session::find($id);

        if (!$examSession) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => [],
                'warning' => 'Kỳ thi không tồn tại',
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'Name' => 'required|string|max:255',
                'TimeStart' => 'required|date',
                'TimeEnd' => 'required|date',
            ]);

            $examSession->update($validatedData);

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examSession,
                'warning' => '',
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => $e->errors(),
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
        $examSession = Exam_session::find($id);

        if (!$examSession) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => [],
                'warning' => 'Exam session does not exist',
            ], 404);
        }

        $examSession->delete();

        return response()->json([
            'success' => true,
            'status' => '200',
            'data' => [],
            'warning' => '',
        ], 200);
    }
}
