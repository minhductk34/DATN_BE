<?php

namespace App\Http\Controllers;

use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PoetryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $poetries = ExamSession::all();

        return response()->json([
            'success' => true,
            'status' => '200',
            'data' => $poetries,
            'warning' => '',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'exam_subject_id' => 'required|string|exists:exam_subjects,id',
                'Name' => 'required|string|max:255',
                'TimeStart' => 'required|date',
                'TimeEnd' => 'required|date',
                'Status' => 'required|in:true,false',
            ]);

            $poetry = ExamSession::create($validatedData);

            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $poetry,
                'warning' => '',
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $poetry = ExamSession::find($id);

        if (!$poetry) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => [],
                'warning' => 'Poetry không tồn tại',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => '200',
            'data' => $poetry,
            'warning' => '',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $poetry = ExamSession::find($id);

        if (!$poetry) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => [],
                'warning' => 'Poetry không tồn tại',
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'exam_subject_id' => 'required|string|exists:exam_subjects,id',
                'Name' => 'required|string|max:255',
                'TimeStart' => 'required|date',
                'TimeEnd' => 'required|date',
                'Status' => 'required|in:true,false',
            ]);

            $poetry->update($validatedData);

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $poetry,
                'warning' => '',
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $poetry = ExamSession::find($id);

        if (!$poetry) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => [],
                'warning' => 'Poetry không tồn tại',
            ], 404);
        }

        $poetry->delete();

        return response()->json([
            'success' => true,
            'status' => '200',
            'data' => [],
            'warning' => '',
        ], 200);
    }
}
