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
                    'time' => $validated['time'],
                    'quantity' => $validated['total'],
                ]);
            } else {
                // Nếu là tạo mới (checkCreateStruct = false), tạo bản ghi mới
                $topicStructure = Exam_subject_detail::create([
                    'exam_subject_id' => $validated['subject'], // Thêm exam_subject_id để liên kết
                    'time' => $validated['time'],
                    'quantity' => $validated['total'],
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
                $relatedTopics = DB::table('exam_structures')
                    ->join('exam_contents', 'exam_contents.id', '=', 'exam_structures.exam_content_id')
                    ->select('exam_structures.*') // Lấy tất cả các trường từ topic_structures
                    ->where('exam_contents.title', $value['title']) // Điều kiện: tiêu đề phải khớp
                    ->where('exam_structures.level', $value['level']) // Điều kiện: level phải khớp
                    ->get(); // Thực thi truy vấn và lấy kết quả

                // Kiểm tra nếu có dữ liệu trong $relatedTopics
                if ($relatedTopics->isEmpty()) {
                    // Không có dữ liệu, thực hiện hành động khác (ví dụ: tạo mới)
                    // Bạn có thể thêm logic tạo mới ở đây
                    $content = Exam_content::query()
                        ->where('title', $value['title'])
                        ->where('exam_subject_id', $validated['subject'])
                        ->first();
                    Exam_structure::create([
                        'exam_content_id' => $content->id,
                        'exam_subject_id' => $validated['subject'],
                        'level' => $value['level'],
                        'quantity' => $value['quantity'],
                    ]);
                } else {
                    // Duyệt qua từng topic đã tìm thấy
                    foreach ($relatedTopics as $topic) {
                        $existingTopic = Exam_structure::query()
                            ->where('exam_content_id', $topic->exam_content_id)
                            ->where('level', $topic->Level)
                            ->first();

                        if ($existingTopic) {
                            // Cập nhật số lượng nếu topic đã tồn tại
                            $existingTopic->update([
                                'quantyity' => $value['Quantity'],
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
),
FilteredQuestions AS (
    -- Lấy câu hỏi duy nhất với phiên bản mới nhất
    SELECT 
        qs.exam_content_id,
        qsv.level, -- Sử dụng level từ question_versions
        COUNT(DISTINCT qs.id) AS question_count -- Đếm số lượng câu hỏi duy nhất
    FROM 
        questions qs
    LEFT JOIN question_versions qsv 
        ON qsv.question_id = qs.id
    LEFT JOIN (
        SELECT 
            question_id, 
            MAX(version) AS latest_version
        FROM 
            question_versions
        GROUP BY 
            question_id
    ) latest_qsv 
        ON latest_qsv.question_id = qsv.question_id
        AND qsv.version = latest_qsv.latest_version
    GROUP BY 
        qs.exam_content_id, 
        qsv.level
)
SELECT
    ec.title,
    lv.level,
    -- Lấy trực tiếp giá trị từ FilteredQuestions
    COALESCE(fq.question_count, 0) AS total,
    -- Lấy số lượng câu hỏi cần thiết từ bảng exam_structures
    COALESCE(MAX(CASE WHEN ts.exam_content_id = ec.id AND ts.level = lv.level THEN ts.quantity ELSE 0 END), 0) AS quantity
FROM Levels lv
CROSS JOIN exam_contents ec -- Kết hợp tất cả các mức độ với nội dung thi
LEFT JOIN exam_structures ts 
    ON ts.exam_subject_id = ec.exam_subject_id 
    AND ts.level = lv.Level
LEFT JOIN FilteredQuestions fq 
    ON fq.exam_content_id = ec.id 
    AND fq.level = lv.Level 
    WHERE (ts.exam_subject_id = ? OR (ts.exam_subject_id IS NULL AND ec.exam_subject_id = ?))
GROUP BY 
    ec.title, 
    lv.level, 
    fq.question_count
ORDER BY 
    ec.title, 
    lv.level;


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
