<?php

namespace App\Http\Controllers;

use App\Models\Exam_subject_detail;
use App\Models\ExamSubjectDetails;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExamSubjectDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show($id)
    {
        try {
            // Lấy thông tin ExamSubjectDetails theo ID
            $examSubjectDetails = Exam_subject_detail::findOrFail($id);

            return response()->json($examSubjectDetails);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Exam subject details not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve exam subject details: ' . $e->getMessage()], 500);
        }
    }

    public function showByExamSubjectId($exam_subject_id)
    {
        try {
            // Lấy thông tin ExamSubjectDetails theo exam_subject_id
            $examSubjectDetails = Exam_subject_detail::where('exam_subject_id', $exam_subject_id)->get();

            if ($examSubjectDetails->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'message' => 'Không tìm thấy thông tin chi tiết về môn thi cho exam_subject_id này.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => "200",
                'data' => $examSubjectDetails,
                'message' => ''
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể truy xuất thông tin chi tiết về môn thi: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam_subject_detail $examSubjectDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'exam_subject_id' => 'required|exists:exam_subjects,id',
                'quantity' => 'required|integer|between:1,32767',
                'time' => 'required|integer|between:1,1440', // Giả sử Time được tính bằng phút
            ]);

            $examSubjectDetails = Exam_subject_detail::findOrFail($id);

            // Cập nhật dữ liệu
            $examSubjectDetails->update($validated);

            return response()->json($examSubjectDetails);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Exam subject details not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update exam subject details: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam_subject_detail $examSubjectDetails)
    {
        //
    }
}
