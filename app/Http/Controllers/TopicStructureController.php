<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam_content;
use App\Models\Exam_structure;
use App\Models\Exam_subject_detail;
use App\Models\ExamContent;
use App\Models\ExamSubjectDetails;
use App\Models\TopicStructure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TopicStructureController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Xác thực request theo cấu trúc interface reqStructure
            $validated = $request->validate([
                'time' => 'required|numeric',
                'total' => 'required|numeric',
                'modules' => 'required|array',
                'checkCreateStruct' => 'required|boolean',
                'subject' => 'required|exists:exam_subjects,id',
                'exam' => 'required|exists:exams,id'
            ]);

            // Kiểm tra điều kiện `checkCreateStruct`
            if ($validated['checkCreateStruct']) {
                // Tìm bản ghi với `exam_subject_id` để cập nhật
                $topicStructure = Exam_subject_detail::where('exam_subject_id', $validated['subject'])->first();

                if (!$topicStructure) {
                    return response()->json([
                        'error' => 'Topic structure not found for the given exam_subject_id.',
                        'data' => $validated['subject']
                    ], 404);
                }

                // Cập nhật bản ghi hiện tại
                $topicStructure->update([
                    'Time' => $validated['time'],
                    'Quantity' => $validated['total'],
                ]);


            } else {
                // Nếu là tạo mới (checkCreateStruct = false), tạo bản ghi mới
                $topicStructure = Exam_subject_detail::create([
                    'exam_subject_id' => $validated['subject'], // Thêm exam_subject_id để liên kết
                   'Time' => $validated['time'],
                    'Quantity' => $validated['total'],
                ]);

            }
            // Tạo mảng để chứa tất cả các levels từ modules
            $allLevels = [];

            // Duyệt qua từng module và thêm các levels vào mảng `$allLevels`
            foreach ($validated['modules'] as $module) {
                if (isset($module['levels'])) {
                    // Dùng array_merge để kết hợp các mảng levels từ từng module
                    $allLevels = array_merge($allLevels, $module['levels']);
                }
            }

            // Mảng để lưu trữ các topic_structures liên quan và tránh trùng lặp
            $relatedTopicStructures = [];

            // Sử dụng mảng để theo dõi các `exam_content_id` đã thêm
            $examContentIds = [];

            // Lặp qua từng level để truy vấn các topic_structures liên quan
            foreach ($allLevels as $level => $value) {
                // Truy vấn dữ liệu liên quan từ `topic_structures`
                $relatedTopics = DB::table('topic_structures')
                    ->join('exam_contents', 'exam_contents.id', '=', 'topic_structures.exam_content_id')
                    ->select('topic_structures.*') // Lấy tất cả các trường từ topic_structures
                    ->where('exam_contents.title', $value['title']) // Điều kiện: tiêu đề phải khớp
                    ->where('topic_structures.Level', $value['Level']) // Điều kiện: level phải khớp
                    ->get(); // Thực thi truy vấn và lấy kết quả

                // Kiểm tra nếu có dữ liệu trong $relatedTopics
                if ($relatedTopics->isEmpty()) {
                    // Không có dữ liệu, thực hiện hành động khác (ví dụ: tạo mới)
                    // Bạn có thể thêm logic tạo mới ở đây
                    $content = Exam_content::query()
                    ->where('title',$value['title'])
                    ->where('exam_subject_id',$validated['subject'])
                    ->first();
                    Exam_structure::create([
                        'exam_content_id' => $content->id,
                        'exam_subject_id' => $validated['subject'],
                        'Level' => $value['Level'],
                        'Quality' => $value['Quantity'],
                    ]);
                } else {
                    // Duyệt qua từng topic đã tìm thấy
                    foreach ($relatedTopics as $topic) {
                        $existingTopic = Exam_structure::query()
                            ->where('exam_content_id', $topic->exam_content_id)
                            ->where('Level', $topic->Level)
                            ->first();

                        if ($existingTopic) {
                            // Cập nhật số lượng nếu topic đã tồn tại
                            $existingTopic->update([
                                'Quality' => $value['Quantity'],
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'message' => 'Topic structure updated successfully.',
                'data' => $topicStructure,
                'related_topics' => $relatedTopicStructures
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process topic structure: ' . $e->getMessage()], 500);
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

            $topicStructure = Exam_structure::findOrFail($id);

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
        // Kiểm tra tính hợp lệ của ID
        if (empty($id) || !is_string($id)) {
            return response()->json([
                'success' => false,
                'status' => "400",
                'data' => [],
                'message' => 'Invalid Exam Subject ID provided'
            ], 400);
        }

        try {
            // Truy vấn SQL
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
                lv.level,
                COALESCE(SUM(CASE WHEN qsv.Level = lv.Level THEN 1 ELSE 0 END), 0) AS total,
                COALESCE(MAX(CASE WHEN ts.exam_content_id = ec.id AND ts.level = lv.level THEN ts.quantity ELSE 0 END), 0) AS quantity
            FROM Levels lv
            CROSS JOIN exam_contents ec
            LEFT JOIN exam_structures ts ON ts.exam_subject_id = ec.exam_subject_id AND ts.level = lv.Level
            LEFT JOIN exam_subject_details esd ON esd.exam_subject_id = ts.exam_subject_id
            LEFT JOIN questions qs ON qs.exam_content_id = ec.id
            LEFT JOIN question_versions qsv ON qsv.question_id = qs.id 
            WHERE (ts.exam_subject_id = ? OR (ts.exam_subject_id IS NULL AND ec.exam_subject_id = ?))
            GROUP BY ec.title, lv.level
            ORDER BY ec.title, lv.level;
        ";

            // Lấy dữ liệu từ cơ sở dữ liệu
            $result = DB::select($query, [$id, $id]);

            // Nếu không có dữ liệu
            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'message' => 'Structure not found'
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
                'message' => 'Internal server error while processing your request'
            ], 500);
        }
    }

    // Phương thức để lấy thông tin TopicStructure theo ID
    public function show($id)
    {
        try {
            // Tìm kiếm TopicStructure theo ID
            $topicStructure = Exam_structure::findOrFail($id);

            return response()->json($topicStructure);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => "404",
                'data' => [],
                'message' => 'Structure not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve topic structure: ' . $e->getMessage()], 500);
        }
    }

    // Phương thức mới để lấy thông tin theo exam_subject_id
    public function showByExamSubjectId($exam_subject_id)
    {
        try {
            $query = Exam_structure::query()->where('exam_subject_id', $exam_subject_id);
            // dd($query->toSql());

            // Thực thi truy vấn
            $topicStructures = $query->get();

            if ($topicStructures->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => $query->toSql(),
                    'message' => 'Structure not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => "200",
                'data' => $topicStructures,
                'message' => ''
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'message' => 'Failed to retrieve topic structures: ' . $e->getMessage()
            ], 500);
        }
    }
}
