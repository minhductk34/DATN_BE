<?php

namespace App\Http\Controllers;


use App\Models\Exam;
use Illuminate\Http\Request;
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
            'Name' => 'required|string|max:255',
            'TimeStart' => 'required|date',
            'TimeEnd' => 'required|date|after_or_equal:TimeStart',
            'Status' => 'required|in:Scheduled,Ongoing,Completed'
        ]);

        $exam = Exam::create($validated);

        if ($exam) {
            return response()->json($exam, 201);
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
            'Name' => 'sometimes|string|max:255',
            'TimeStart' => 'sometimes|date',
            'TimeEnd' => 'sometimes|date|after_or_equal:TimeStart', // Ensure TimeEnd is valid
            'Status' => 'sometimes|in:Scheduled,Ongoing,Completed'
        ]);

        $exam = Exam::findOrFail($id);
        if (empty($validated)) {
            return response()->json(['message' => 'No valid data provided for update.'], 400);
        }

        $updated = $exam->update($validated);

        if ($updated) {
            return response()->json($exam);
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

            return response()->json(['message' => 'Exam deleted successfully.'], 200);
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
}
