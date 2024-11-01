<?php

namespace App\Http\Controllers;


use App\Models\Exam;
use App\Models\Exam_subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
//use App\Imports\ExamsImport;
class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = Exam::all();
        return response()->json($exams);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'id' => 'required|string|unique:exams,id',
            'name' => 'required|string|max:255',
            'time_start' => 'required|date',
            'time_end' => 'required|date|after_or_equal:TimeStart',

        ]);

        $exam = Exam::create($validated);
        if ($exam) {
            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $exam,
                'message' => 'Create exam successfully.',
            ], 201);
        } else {
            return response()->json([
                'message' => 'Failed to create exam. Please try again.'
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function show($id)
    {
        $exam = Exam::findOrFail($id);
        return response()->json($exam);
    }


    /**
     * Display the specified resource.
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'time_start' => 'sometimes|date',
            'time_end' => 'sometimes|date|after_or_equal:time_start',

        ]);

        $exam = Exam::findOrFail($id);
        if (empty($validated)) {
            return response()->json(['message' => 'No valid data provided for update.'], 400);
        }

        $updated = $exam->update($validated);

        if ($updated) {
            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $exam,
                'message' => 'Update exam successfully.',
            ],200);
        } else {

            return response()->json(['message' => 'Failed to update exam. Please try again.'], 500);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->delete()) {

            return response()->json(['message' => 'Exam deleted successfully.','success' => true,], 200, );
        } else {

            return response()->json(['message' => 'Failed to delete exam. Please try again.'], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function restore($id)
    {
        $exam = Exam::withTrashed()->findOrFail($id);
        if ($exam->trashed()) {
            $exam->restore();
            return response()->json(['message' => 'Exam restored successfully.']);
        }
        return response()->json(['message' => 'Exam is not deleted.'], 400);
    }
    public function getDataShow()
    {
        try {
            $exams = Exam::query()
                ->select('id', 'name')
                ->get();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $exams,
                'message' => '',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'message' => $e->getMessage(),
            ], 422);
        }
    }
    public function getALLExamsWithExamSubjects()
    {
        try {
            $exams = Exam::with('exam_subjects')->has('exam_subjects')->get();

            if ($exams->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'message' => 'Structure not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $exams,
                'message' => 'Data retrieved successfully'
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
    public function getExamSubjectsWithContent($examId)
    {
        try {
            $examSubjects = Exam_subject::with(['exam_content'])
                ->where('exam_id', $examId)
                ->whereHas('exam_content')
                ->get();

            if ($examSubjects->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'message' => 'Structure not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examSubjects,
                'message' => 'Data retrieved successfully'
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
