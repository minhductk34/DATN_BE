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
        $validated = $request->validate([
            'id' => 'required|string|unique:exams,id',
            'Name' => 'required|string|max:255',
            'TimeStart' => 'required|date',
            'TimeEnd' => 'required|date',
            'Status' => 'required|in:Scheduled,Ongoing,Completed'
        ]);

        $exam = Exam::create([
            'id' => $request->id,
            'Name' => $request->Name,
            'TimeStart' => $request->TimeStart,
            'TimeEnd' => $request->TimeEnd,
            'Status' => $request->Status,
        ]);
        return response()->json($exam, 201);
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
        $validated = $request->validate([
            'Name' => 'sometimes|string|max:255',
            'TimeStart' => 'sometimes|date',
            'TimeEnd' => 'sometimes|date',
            'Status' => 'sometimes|in:Scheduled,Ongoing,Completed'
        ]);

        $exam = Exam::findOrFail($id);
        $exam->update($validated);
        return response()->json($exam);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();
        return response()->json(null, 204);
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
