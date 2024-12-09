<?php

namespace App\Http\Controllers;


use App\Models\Exam;
use App\Models\Exam_room;
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
    public function getAllWithStatusTrue()
    {
        try {
            $exams = Exam::query()
                ->select('id','name','time_start','time_end')
//                ->where('status', '=', 1)
//                ->where('deleted_at',null)
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $exams,
                'message' => 'Dữ liệu đã được lấy thành công'
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn'
            ], 500);
        }
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
        if (strtotime($validated['time_start']) > strtotime($validated['time_end'])) {
            return response()->json([
                'message' => 'Thời gian bắt đầu phải nhỏ hơn hoặc bằng thời gian kết thúc.'
            ], 422);
        }
        $exam = Exam::create($validated);
        if ($exam) {
            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $exam,
                'message' => 'Tạo kỳ thi thành công.',
            ], 201);
        } else {
            return response()->json([
                'message' => 'Tạo kỳ thi thất bại. Vui lòng thử lại.'
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function show($id)
    {
        $exam = Exam::findOrFail($id);
        return response()->json("show");
    }


    /**
     * Display the specified resource.
     */
    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'time_start' => 'sometimes|date',
            'time_end' => 'sometimes|date',
        ]);


        if (!empty($validated['time_start']) && !empty($validated['time_end'])
            && strtotime($validated['time_start']) > strtotime($validated['time_end'])) {
            return response()->json([
                'message' => 'Thời gian bắt đầu phải nhỏ hơn hoặc bằng thời gian kết thúc.'
            ], 422);
        }

        $exam = Exam::findOrFail($id);

        if (empty($validated)) {
            return response()->json(['message' => 'Không có dữ liệu hợp lệ để cập nhật.'], 400);
        }

        $updated = $exam->update($validated);

        if ($updated) {
            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $exam,
                'message' => 'Cập nhật kỳ thi thành công.',
            ], 200);
        } else {
            return response()->json(['message' => 'Cập nhật kỳ thi thất bại. Vui lòng thử lại.'], 500);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->delete()) {

            return response()->json(['message' => 'Đã xóa bài kiểm tra thành công.','success' => true,], 200, );
        } else {

            return response()->json(['message' => 'Không xóa được bài kiểm tra. Vui lòng thử lại.'], 500);
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
            return response()->json(['message' => 'Đã khôi phục kỳ thi thành công.']);
        }
        return response()->json(['message' => 'Bài kiểm tra không bị xóa.'], 400);
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
                    'message' => 'Cấu trúc không tìm thấy'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $exams,
                'message' => 'Dữ liệu đã được lấy thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn'
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
                    'message' => 'Cấu trúc không tìm thấy'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examSubjects,
                'message' => 'Dữ liệu đã được lấy thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn'
            ], 500);
        }
    }

    public function getALLExamsWithExamSubjectsById($id)
    {
        try {
            $currentDateTime = now();

            $exams = Exam::query()->where('id','=',$id)
                ->where('time_start', '<=', $currentDateTime)
                ->where('time_end', '>', $currentDateTime)
                ->with('exam_subjects')->has('exam_subjects')->get();

            if ($exams->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'message' => 'Không tìm thấy kỳ thi phù hợp'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $exams,
                'message' => 'Dữ liệu đã được lấy thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn'
            ], 500);
        }
    }

    public function getExamRoomsInExams($examId)
    {
        try {
            $exam_rooms = Exam_room::query()->where('exam_id', $examId)->orderBy('created_at', 'desc')->get();
            if (!$exam_rooms) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'message' => 'Cấu trúc không tìm thấy'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $exam_rooms,
                'message' => 'Dữ liệu đã được lấy thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn'
            ], 500);
        }
    }

}
