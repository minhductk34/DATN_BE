<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Models\Exam;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function getExam()
    {
        try {
            $candidate = auth()->user();

            $exam = $candidate->exam;

            if (!$exam) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'data' => [],
                    'warning' => 'Không có kì thi nào cho người dùng.',
                ], 404);
            }

            $subjectCount = $exam->subjects()->count();

            $data = [
                'exam_id' => $exam->id,
                'name' => $exam->Name,
                'time_start' => $exam->TimeStart,
                'time_end' => $exam->TimeEnd,
                'subject_count' => $subjectCount
            ];

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $data,
                'warning' => '',
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getSubjectByExam($examId)
    {
        try {
            if (!$examId || !is_string($examId)) {
                return response()->json([
                    'success' => false,
                    'status'  => 400,
                    'data'    => [],
                    'warning' => 'ID kỳ thi không hợp lệ.',
                ], 400);
            }

            $cacheKey = "exam_subjects_{$examId}";
            $data = Cache::remember($cacheKey, 60, function () use ($examId) {
                $exam = Exam::with([
                    'subjects.contents.questions',
                    'subjects.contents.readings.questions',
                    'subjects.contents.listenings.questions'
                ])->find($examId);

                if (!$exam) {
                    return null;
                }

                if ($exam->subjects->isEmpty()) {
                    return [];
                }

                return SubjectResource::collection($exam->subjects);
            });

            if (is_null($data)) {
                return response()->json([
                    'success' => false,
                    'status'  => 404,
                    'data'    => [],
                    'warning' => 'Không tìm thấy kỳ thi với ID đã cung cấp.',
                ], 404);
            }

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status'  => 404,
                    'data'    => [],
                    'warning' => 'Kỳ thi không có môn học nào.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status'  => 200,
                'data'    => $data,
                'warning' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 500,
                'data'    => [],
                'warning' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }
}
