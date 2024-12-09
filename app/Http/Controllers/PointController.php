<?php

namespace App\Http\Controllers;

use App\Models\Exam_subject;
use App\Models\Point;
use Illuminate\Http\Request;
use Nette\Schema\ValidationException;

class PointController extends Controller
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
    public function getStudentPointsByExam($idcode, $examId) {
        try {
            $examSubjects = Exam_subject::query()
                ->where('exam_id', $examId)
                ->get();

            $points = Point::query()
                ->with(['exam_subject'])
                ->where('idcode', $idcode)
                ->get()
                ->keyBy('exam_subject_id');

            $formattedPoints = $examSubjects->map(function($subject) use ($points) {
                $point = $points[$subject->id] ?? null;
                return [
                    'subject_name' => $subject->name,
                    'point' => $point ? $point->point : 0,
                    'correct_answers' => $point ? $point->number_of_correct_sentences : 0,
                    'exam_date' => $point ? $point->created_at->format('Y-m-d') : null
                ];
            });

            $averagePoint = $formattedPoints->avg('point');

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'points' => $formattedPoints,
                    'average_point' => round($averagePoint, 2)
                ],
                'message' => '',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi khi lấy điểm',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getStudentPoints($idcode) {
        try {
            $points = Point::query()
                ->with(['exam_subject']) // Load thông tin môn thi
                ->where('idcode', $idcode)
                ->get();

            if ($points->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'Không tìm thấy điểm của thí sinh này',
                ], 404);
            }

            $formattedPoints = $points->map(function($point) {
                return [
                    'subject_name' => $point->exam_subject->name,
                    'point' => $point->point,
                    'correct_answers' => $point->number_of_correct_sentences,
                    'exam_date' => $point->created_at->format('Y-m-d')
                ];
            });

            // Tính điểm trung bình
            $averagePoint = $points->avg('point');

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'points' => $formattedPoints,
                    'average_point' => round($averagePoint, 2)
                ],
                'message' => '',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi khi lấy điểm',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Thêm điểm cho thí sinh
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'exam_subject_id' => 'required|exists:exam_subjects,id',
                'idcode' => 'required|exists:candidates,idcode',
                'point' => 'required|numeric|between:0,100',
                'number_of_correct_sentences' => 'required|integer|min:0'
            ]);

            $point = Point::create($validated);

            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $point,
                'message' => 'Thêm điểm thành công'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi khi thêm điểm',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Point $point)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Point $point)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Point $point)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Point $point)
    {
        //
    }
}
