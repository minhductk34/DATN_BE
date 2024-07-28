<?php

namespace App\Http\Controllers;

use App\Models\Exam_content;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;

class ExamContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function getContentBgExam($id)
    {
        try {

            if (!is_string($id) || empty(trim($id))) {
                return response()->json([
                    'success' => false,
                    'status' => '400',
                    'data' => [],
                    'warning' => 'Invalid exam_subject_id',
                ], 400);
            }

            $content = Exam_content::query()
                ->where('exam_subject_id', $id)
                ->get();

            if ($content->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'No content found for the given exam_subject_id',
                ], 404);
            }


            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $content,
                'warning' => '',
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'An unexpected error occurred: ' . $e->getMessage(),
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
            $validatedData = $request->validate([
                'exam_subject_id' => 'required|string',
                'title' => 'required|string|max:255',
            ]);

            $examSubject = Exam_content::create($validatedData);

            return response()->json([
                'status' => 'success',
                'data' => $examSubject,
                'warning' => ''
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'warning' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'warning' => $e->getMessage() // Use getMessage() for a readable error message
            ], 500);
        }
    }

    public function importFile(Request $request)
    {
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

            if (!is_string($id) || empty(trim($id))) {
                return response()->json([
                    'success' => false,
                    'status' => '400',
                    'data' => [],
                    'warning' => 'Invalid exam_content_id',
                ], 400);
            }

            $content = Exam_content::query()
                ->where('id', $id)
                ->get();

            if ($content->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'No content found for the given exam_content_id',
                ], 404);
            }


            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $content,
                'warning' => '',
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam_content $exam_content)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        try {
            $validatedData = $request->validate([
                'exam_subject_id' => 'required|string',
                'title' => 'required|string|max:255',
            ]);

            $examSubject = Exam_content::findOrFail($id);

            $examSubject->update($validatedData);

            $response = [
                'status' => 'success',
                'data' => $examSubject,
                'warning' => ''
            ];

            return response()->json($response, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'warning' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'warning' => $e
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam_content $exam_content)
    {
        //
    }
}
