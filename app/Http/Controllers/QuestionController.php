<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportExelRequest;
use App\Http\Requests\Questions\StoreQuestionRequest;
use App\Http\Requests\Questions\UpdateQuestionRequest;
use App\Imports\QuestionImport;
use App\Imports\QuestionUpdate;
use App\Models\Exam;
use App\Models\ExamContent;
use App\Models\ExamSubject;
use App\Models\Question;
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
    public function index(Request $request)
    {
        try {
            $exam_content_id = $request->input('exam_content_id');

            if (! $exam_content_id) {
                return $this->jsonResponse(false, null, 'Hãy chọn nội dung thi', 400);
            }

            $questions = Question::query()
                ->join('question_versions', function ($join) {
                    $join->on('questions.id', '=', 'question_versions.question_id')
                        ->on('questions.current_version_id', '=', 'question_versions.id');
                })
                ->where('questions.exam_content_id', $exam_content_id)
                ->select('questions.id', 'question_versions.Title', 'questions.Status')
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
        $validatedData = $request->except(['Image_Title', 'Image_P', 'Image_F1', 'Image_F2', 'Image_F3']);
        $imagePaths = [];

        try {
            $imageFields = ['Image_Title', 'Image_P', 'Image_F1', 'Image_F2', 'Image_F3'];

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

    public function update(UpdateQuestionRequest $request, $id)
    {
        $question  = Question::find($id);

        if (!$question) {
            return $this->jsonResponse(false, null, 'Không tìm thấy câu hỏi', 404);
        }

        $validatedData = $request->except(['Image_Title', 'Image_P', 'Image_F1', 'Image_F2', 'Image_F3']);
        $imagePaths = [];

        try {
            $imageFields = ['Image_Title', 'Image_P', 'Image_F1', 'Image_F2', 'Image_F3'];

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
        return $question->versions()->create([
            'Title' => $data['Title'],
            'Image_Title' => $data['Image_Title'] ?? null,
            'Answer_P' => $data['Answer_P'],
            'Image_P' => $data['Image_P'] ?? null,
            'Answer_F1' => $data['Answer_F1'],
            'Image_F1' => $data['Image_F1'] ?? null,
            'Answer_F2' => $data['Answer_F2'],
            'Image_F2' => $data['Image_F2'] ?? null,
            'Answer_F3' => $data['Answer_F3'],
            'Image_F3' => $data['Image_F3'] ?? null,
            'Level' => $data['Level'],
            'version' => $version,
            'is_active' => true,
        ]);
    }

    protected function jsonResponse($success = true, $data = null, $warning = '', $statusCode = 200)
    {
        return response()->json([
            'success' => $success,
            'status' => "$statusCode",
            'data' => $data,
            'warning' => $warning
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
                'warning' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => $e->getMessage(),
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
                    'warning' => 'Exam not found',
                ], 404);
            }

            $examSubject = ExamSubject::where('id', $examSubjectId)
                ->where('exam_id', $examId)
                ->select('id')
                ->first();

            if (!$examSubject) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'ExamSubject not found for the given exam_id',
                ], 404);
            }

            $examContent = ExamContent::where('exam_subject_id', $examSubjectId)
                ->get();

            if ($examContent->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'ExamContent not found for the given exam_subject_id',
                ], 404);
            }

            $questionIds = Question::whereIn('exam_content_id', $examContent->pluck('id'))->pluck('id'); // Lấy tất cả các câu hỏi

            if ($questionIds->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'Questions not found for the given exam_content_id',
                ], 404);
            }

            $questionVersions = QuestionVersion::whereIn('question_id', $questionIds)->get(); // Lấy tất cả các phiên bản câu hỏi

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $questionVersions,
                'warning' => '',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => $e->getMessage(),
            ], 500);
        }
    }


}
