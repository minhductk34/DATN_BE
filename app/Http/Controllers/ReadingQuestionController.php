<?php

namespace App\Http\Controllers;

use App\Models\ReadingQuestion;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Questions\UpdateReadingQuestionRequest;
use App\Http\Requests\ImportExelRequest;
use App\Http\Requests\Questions\StoreReadingQuestionRequest;
use App\Imports\ReadingQuestionImport;
use App\Models\ReadingQuestionVersion;

class ReadingQuestionController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreReadingQuestionRequest $request)
    {
        $validatedData = $request->validated();

        try {
            return DB::transaction(function () use ($validatedData) {
                $question = ReadingQuestion::create(    
                    [
                        'id' => $validatedData['id'],
                        'reading_id' => $validatedData['reading_id'],
                    ]
                );
                $version = $this->createQuestionVersion($question, $validatedData, 1);
                $question->update(['current_version_id' => $version->id]);

                return $this->jsonResponse(true, $question->load('currentVersion'), '', 201);
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function importExcel(ImportExelRequest $request)
    {
        try {
            DB::beginTransaction();

            $import = new ReadingQuestionImport();
            $import->import($request->file('file'));

            if (count($import->failures()) > 0) {
                $failures = $import->failures();

                foreach ($failures as $failure) {
                    $errorMessages[] = [
                        'row' => $failure->row(),
                        'errors' => $failure->errors(),
                    ];
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

    public function versions($id)
    {
        try {
            $question = ReadingQuestion::with('versions')->find($id);

            if (! $question) {
                return $this->jsonResponse(true, [], 'Không tìm thấy câu hỏi', 404);
            }

            return $this->jsonResponse(true, $question, '', 200);
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

            $question = ReadingQuestion::with('currentVersion')->find($id);

            if (!$question) {
                return $this->jsonResponse(false, null, 'Không tìm thấy câu hỏi', 404);
            }

            return $this->jsonResponse(true, $question, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function update(UpdateReadingQuestionRequest $request, $id)
    {
        $question  = ReadingQuestion::find($id);

        if (!$question) {
            return $this->jsonResponse(false, null, 'Không tìm thấy câu hỏi', 404);
        }

        $validatedData = $request->validated();

        try {
            return DB::transaction(function () use ($question, $validatedData) {
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
                ]);

                return $this->jsonResponse(true, $question->load('currentVersion'), '', 200);
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $question = ReadingQuestion::query()->select('id')->find($id);

            if (!$question) {
                return $this->jsonResponse(false, null, 'Không tìm thấy câu hỏi', 404);
            }

            $question->delete();

            return $this->jsonResponse(true, null, 'Xóa thành công', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    private function createQuestionVersion($question, array $data, int $version)
    {
        return $question->versions()->create([
            'Title' => $data['Title'],
            'Answer_P' => $data['Answer_P'],
            'Answer_F1' => $data['Answer_F1'],
            'Answer_F2' => $data['Answer_F2'],
            'Answer_F3' => $data['Answer_F3'],
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
}
