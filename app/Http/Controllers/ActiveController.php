<?php

namespace App\Http\Controllers;

use App\Models\Active;
use Illuminate\Http\Request;

class ActiveController extends Controller
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
    public function show(Active $active)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Active $active)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Active $active)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Active $active)
    {
        //
    }
    public function updateStatus($candidate_id, $exam_subject_id)
    {
        try {
            $active = Active::query()
                ->where('idcode', $candidate_id)
                ->where('exam_subject_id', $exam_subject_id)
                ->first();

            if (!$active) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'message' => 'Structure not found'
                ], 404);
            }
            $active->status = !$active->status;

            $active->save();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $active,
                'message' => 'Update status successfully'
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
