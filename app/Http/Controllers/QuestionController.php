<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportExelRequest;
use App\Http\Requests\Questions\StoreQuestionRequest;
use App\Http\Requests\Questions\UpdateQuestionRequest;
use App\Imports\QuestionImport;
use App\Imports\QuestionUpdate;
use App\Models\Exam;
use App\Models\Exam_content;
use App\Models\Exam_subject;
use App\Models\ExamContent;
use App\Models\ExamSubject;
use App\Models\Question;
use App\Models\Question_version;
use App\Models\QuestionVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamSubjectController;
use App\Http\Controllers\ExamContentController;

class QuestionController extends Controller
{
    //Quản lý câu hỏi
    public function index($id,Request $request)
    {
        try {
        
            if (!$id) {
                return $this->jsonResponse(false, null, 'Hãy chọn nội dung thi', 400);
            }

            $questions = Question::query()
                ->join('question_versions', function ($join) {
                    $join->on('questions.id', '=', 'question_versions.question_id')
                        ->on('questions.current_version_id', '=', 'question_versions.id');
                })
                ->where('questions.exam_content_id', $id)
                ->select('questions.id', 'question_versions.title', 'questions.status')
                ->get();

            if ($questions->isEmpty()) {
                return $this->jsonResponse(true, [], 'Không tìm thấy câu hỏi', 404);
            }

            return $this->jsonResponse(true, $questions, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function versions($id)
    {
        try {
            $question = Question::with('versions')->find($id);

            if (! $question) {
                return $this->jsonResponse(true, [], 'Không tìm thấy câu hỏi', 404);
            }

            return $this->jsonResponse(true, $question, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function store(StoreQuestionRequest $request)
    {
        $validatedData = $request->except(['image_Title', 'image_P', 'image_F1', 'image_F2', 'image_F3']);
        $imagePaths = [];

        try {
            $imageFields = ['image_Title', 'image_P', 'image_F1', 'image_F2', 'image_F3'];

            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $imagePaths[$field] = $request->file($field)->store('questions', 'public');
                }
            }

            $validatedData = array_merge($validatedData, $imagePaths);

            return DB::transaction(function () use ($validatedData) {
                $question = Question::create(
                    [
                        'id' => $validatedData['id'],
                        'exam_content_id' => $validatedData['exam_content_id'],
                    ]
                );
                $version = $this->createQuestionVersion($question, $validatedData, 1);
                $question->update(['current_version_id' => $version->id]);

                return $this->jsonResponse(true, $question->load('currentVersion'), '', 201);
            });
        } catch (\Exception $e) {
            // xóa ảnh đã tải lên nếu có lỗi
            foreach ($imagePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    // Thêm câu hỏi bằng exel
    public function importExcel(ImportExelRequest $request)
    {
        try {
            DB::beginTransaction();

            $import = new QuestionImport();
            $import->import($request->file('file'));

            if (count($import->failures()) > 0) {
                $failures = $import->failures();

                foreach ($failures as $failure) {
                    $errorMessages[] = [
                        'row' => $failure->row(),
                        'errors' => $failure->errors(),
                    ];
                }

                foreach ($import->imageTmp as $img) {
                    if (Storage::disk('public')->exists($img)) {
                        Storage::disk('public')->delete($img);
                    }
                }

                DB::rollBack();
                return $this->jsonResponse(false, null, $errorMessages, 422);
            }

            DB::commit();

            return $this->jsonResponse(true, [], '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return $this->jsonResponse(false, null, 'ID câu hỏi không hợp lệ', 400);
            }

            $question = Question::with('currentVersion')->find($id);

            if (!$question) {
                return $this->jsonResponse(false, null, 'Không tìm thấy câu hỏi', 404);
            }

            return $this->jsonResponse(true, $question, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function showByIdContent($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return $this->jsonResponse(false, null, 'ID nội dung thi không hợp lệ', 400);
            }

            $question = Question::with('currentVersion')->where('exam_content_id', $id)->get();

            if (!$question) {
                return $this->jsonResponse(false, $id, 'Không tìm thấy câu hỏi', 404);
            }

            return $this->jsonResponse(true, $question, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function update(UpdateQuestionRequest $request, $id)
    {
        $question  = Question::find($id);

        if (!$question) {
            return $this->jsonResponse(false, null, 'Không tìm thấy câu hỏi', 404);
        }

        $validatedData = $request->except(['image_Title', 'image_P', 'image_F1', 'image_F2', 'image_F3']);
        $imagePaths = [];

        try {
            $imageFields = ['image_Title', 'image_P', 'image_F1', 'image_F2', 'image_F3'];

            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $imagePaths[$field] = $request->file($field)->store('questions', 'public');
                }
            }

            $validatedData = array_merge($validatedData, $imagePaths);

            return DB::transaction(function () use ($question, $validatedData) {
                // Đánh dấu phiên bản cũ là không hoạt động
                $question->currentVersion->update(['is_active' => false]);

                // Tạo phiên bản mới
                $newVersion = $this->createQuestionVersion(
                    $question,
                    $validatedData,
                    $question->versions()->max('version') + 1
                );

                // Cập nhật question
                $question->update([
                    'id' => $validatedData['id'],
                    'current_version_id' => $newVersion->id,
                    'exam_content_id' => $validatedData['exam_content_id']
                ]);

                return $this->jsonResponse(true, $question->load('currentVersion'), '', 200);
            });
        } catch (\Exception $e) {
            foreach ($imagePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function updateStatus($id)
    {
        try {
            $examSubject = Question::query()->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'Không tìm thấy câu hỏi', 404);
            }

            $examSubject->status = $examSubject->status == 'true' ? 'false' : 'true';

            $examSubject->save();

            return $this->jsonResponse(true, $examSubject->status, 'update status question successfully', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function updateExcel(ImportExelRequest $request)
    {
        try {
            DB::beginTransaction();

            $import = new QuestionUpdate();
            $import->import($request->file('file'));

            if (count($import->failures()) > 0) {
                $failures = $import->failures();

                foreach ($failures as $failure) {
                    $errorMessages[] = [
                        'row' => $failure->row(),
                        'errors' => $failure->errors(),
                    ];
                }

                foreach ($import->imageTmp as $img) {
                    if (Storage::disk('public')->exists($img)) {
                        Storage::disk('public')->delete($img);
                    }
                }

                DB::rollBack();
                return $this->jsonResponse(false, null, $errorMessages, 422);
            }

            DB::commit();

            return $this->jsonResponse(true, [], '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $question = Question::query()->select('id')->find($id);

            if (!$question) {
                return $this->jsonResponse(false, null, 'Không tìm thấy câu hỏi', 404);
            }

            $question->delete();

            return $this->jsonResponse(true, null, 'Xóa thành công', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function restore($id)
    {
        try {
            $question = Question::withTrashed()
                ->select('id', 'deleted_at')
                ->find($id);

            if (!$question) {
                return $this->jsonResponse(false, null, 'Không tìm thấy câu hỏi', 404);
            }

            if ($question->deleted_at == null) {
                return $this->jsonResponse(false, null, 'Câu hỏi chưa bị xóa', 409);
            }

            $question->restore();

            return $this->jsonResponse(true, $question, 'Khôi phục thành công', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    private function createQuestionVersion(Question $question, array $data, int $version)
    {
        return $question->version()->create([
            'title' => $data['title'],
            'question_id'=>$question->id,
            'image_Title' => $data['image_Title'] ?? null,
            'answer_P' => $data['answer_P'],
            'image_P' => $data['image_P'] ?? null,
            'answer_F1' => $data['answer_F1'],
            'image_F1' => $data['image_F1'] ?? null,
            'answer_F2' => $data['answer_F2'],
            'image_F2' => $data['image_F2'] ?? null,
            'answer_F3' => $data['answer_F3'],
            'image_F3' => $data['image_F3'] ?? null,
            'level' => $data['level'],
            'version' => $version,
            'is_active' => true,
        ]);
    }

    protected function jsonResponse($success = true, $data = null, $message = '', $statusCode = 200)
    {
        return response()->json([
            'success' => $success,
            'status' => "$statusCode",
            'data' => $data,
            'message' => $message
        ], $statusCode);
    }

    public function dataOptions()
    {
        try {
            $examController = new ExamController();
            $examSubjectController = new ExamSubjectController();

            $dataExam = $examController->getDataShow()->getData()->data;
            $dataExamSubjects = $examSubjectController->getDataShow()->getData()->data;

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'exams' => $dataExam,
                    'subjects' => $dataExamSubjects
                ],
                'message' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function dataQuestion($examId, $examSubjectId)
    {
        try {
            $exam = Exam::find($examId);
            if (!$exam) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'Exam not found',
                ], 404);
            }

            $examSubject = Exam_subject::where('id', $examSubjectId)
                ->where('exam_id', $examId)
                ->select('id')
                ->first();

            if (!$examSubject) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'ExamSubject not found for the given exam_id',
                ], 404);
            }

            $examContent = Exam_content::where('exam_subject_id', $examSubjectId)
                ->get();

            if ($examContent->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'ExamContent not found for the given exam_subject_id',
                ], 404);
            }

            $questionIds = Question::whereIn('exam_content_id', $examContent->pluck('id'))->pluck('id'); // Lấy tất cả các câu hỏi

            if ($questionIds->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'Questions not found for the given exam_content_id',
                ], 404);
            }

            $questionVersions = Question_version::whereIn('question_id', $questionIds)->get(); // Lấy tất cả các phiên bản câu hỏi

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $questionVersions,
                'message' => '',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => $e->getMessage(),
            ], 500);
        }
    }


}
