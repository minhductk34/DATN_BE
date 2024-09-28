<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TopicStructure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TopicStructureController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'exam_content_id' => 'required',
                'exam_subject_id' => 'required|exists:exam_subjects,id',
                'level' => 'required|in:Easy,Medium,Difficult',
                'quality' => 'required|integer|between:1,32767',
            ]);

            $topicStructure = TopicStructure::create($validated);

            return response()->json($topicStructure, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create topic structure: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'exam_content_id' => 'required|exists:exam_contents,id',
                'level' => 'required|in:Easy,Medium,Difficult',
                'exam_subject_id' => 'required|exists:exam_subjects,id',
                'quality' => 'required|integer|between:1,32767',
            ]);

            $topicStructure = TopicStructure::findOrFail($id);

            if (empty($validated)) {
                return response()->json(['message' => 'No valid data provided for update.'], 400);
            }

            // Update the topic structure
            $topicStructure->update($validated);

            return response()->json($topicStructure);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic structure not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update topic structure: ' . $e->getMessage()], 500);
        }
    }

    public function getTotal($id)
    {
        // Kiểm tra tính hợp lệ của ID, đảm bảo ID không rỗng và là chuỗi hợp lệ
        if (empty($id) || !is_string($id)) {
            return response()->json([
                'success' => false,
                'status' => "400",
                'data' => [],
                'warning' => 'Invalid Exam Subject ID provided'
            ], 400);
        }

        try {
            // Thực thi câu truy vấn SQL với $id
            $query = "
                WITH Levels AS (
                    SELECT 'Easy' AS Level
                    UNION ALL
                    SELECT 'Medium'
                    UNION ALL
                    SELECT 'Difficult'
                )
                
                SELECT 
                    ec.title, 
                    lv.Level, 
                    COALESCE(SUM(CASE WHEN qsv.Level = lv.Level THEN 1 ELSE 0 END), 0) AS total, 
                    COALESCE(ts.Quality, 0) AS Quantity
                FROM Levels lv
                CROSS JOIN exam_contents ec
                LEFT JOIN topic_structures ts ON ts.exam_subject_id = ec.exam_subject_id AND ts.level = lv.Level
                LEFT JOIN exam_subject_details esd ON esd.exam_subject_id = ts.exam_subject_id
                LEFT JOIN questions qs ON qs.exam_content_id = ec.id
                LEFT JOIN question_versions qsv ON qsv.question_id = qs.id
                WHERE ts.exam_subject_id = ?
                GROUP BY ec.title, lv.Level, ts.Quality
                ORDER BY ec.title, lv.Level;
            ";

            // Lấy dữ liệu từ cơ sở dữ liệu với tham số $id
            $result = DB::select($query, [$id]);

            // Nếu không tìm thấy dữ liệu, trả về thông báo lỗi
            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'warning' => 'Structure not found'
                ], 404);
            }

            // Trả về dữ liệu thành công
            return response()->json([
                'success' => true,
                'status' => "200",
                'data' => $result,
                'message' => 'Data retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi khi thực thi câu truy vấn
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'warning' => 'Internal server error while processing your request'
            ], 500);
        }
    }

    // Phương thức để lấy thông tin TopicStructure theo ID
    public function show($id)
    {
        try {
            // Tìm kiếm TopicStructure theo ID
            $topicStructure = TopicStructure::findOrFail($id);

            return response()->json($topicStructure);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => "404",
                'data' => [],
                'warning' => 'Structure not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve topic structure: ' . $e->getMessage()], 500);
        }
    }

    // Phương thức mới để lấy thông tin theo exam_subject_id
    public function showByExamSubjectId($exam_subject_id)
    {
        try {
            $query = TopicStructure::query()->where('exam_subject_id', $exam_subject_id);
        // dd($query->toSql());

        // Thực thi truy vấn
        $topicStructures = $query->get();

            if ($topicStructures->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => $query->toSql(),
                    'warning' => 'Structure not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => "200",
                'data' => $topicStructures,
                'warning' => ''
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'warning' => 'Failed to retrieve topic structures: ' . $e->getMessage()
            ], 500);
        }
    }
}
