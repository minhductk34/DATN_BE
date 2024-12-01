<?php

namespace App\Http\Controllers;

use App\Imports\ExamContentImport;
use App\Models\Exam_content;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamContentController extends Controller
{
    public function getQuestionCounts($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'data' => [],
                    'message' => 'Invalid exam_content_id',
                ], 400);
            }

            $content = Exam_content::find($id);

            if (!$content) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'data' => [],
                    'message' => 'No content found for the given content_id',
                ], 404);
            }

            $questionCounts = $content->questions()
                ->join('question_versions', function ($join) {
                    $join->on('questions.current_version_id', '=', 'question_versions.id');
                })
                ->select('question_versions.level', DB::raw('count(*) as count'))
                ->groupBy('question_versions.level')
                ->pluck('count', 'level')
                ->toArray();

            $data = [
                'easy' => $questionCounts['Easy'] ?? 0,
                'medium' => $questionCounts['Medium'] ?? 0,
                'difficult' => $questionCounts['Difficult'] ?? 0,
            ];

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $data,
                'message' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'data' => [],
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getContentByExam($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'data' => [],
                    'message' => 'Invalid exam_subject_id',
                ], 400);
            }

            $content = Exam_content::query()
                ->select('id', 'exam_subject_id', 'title', 'status', 'url_listening', 'description')
                ->where('exam_subject_id', $id)
                ->get();

            if ($content->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'data' => [],
                    'message' => 'No content found for the given exam_subject_id',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $content,
                'message' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'data' => [],
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|string',
                'exam_subject_id' => 'required|string',
                'title' => 'required|string|max:255',
                'url_listening' => 'required|string',
                'description' => 'required|string',
            ]);

            $examContent = Exam_content::create($validatedData);

            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => $examContent,
                'message' => 'Create exam content successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'data' => null,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'data' => null,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'data' => [],
                    'message' => 'Invalid exam_content_id',
                ], 400);
            }

            $content = Exam_content::query()
                ->where('id', $id)
                ->first();

            if (!$content) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'data' => [],
                    'message' => 'No content found for the given exam_content_id',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $content,
                'message' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'data' => [],
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'url_listening' => 'required|string',
                'description' => 'required|string',
            ]);

            $examContent = Exam_content::find($id);

            if (!$examContent) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'data' => [],
                    'message' => 'No content found for the given exam_content_id'
                ], 404);
            }

            $examContent->update($validatedData);

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $examContent,
                'message' => 'Update exam content successfully'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'data' => null,
                'message' => $e->getMessage()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'data' => null,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus($id)
    {
        try {
            $examContent = Exam_content::find($id);

            if (!$examContent) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'data' => null,
                    'message' => 'No content found for the given exam_content_id'
                ], 404);
            }

            $examContent->status = !$examContent->status;
            $examContent->save();

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $examContent->status,
                'message' => 'Update status exam content successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'data' => null,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ], [
            'file.required' => 'Hãy chọn một file để tải lên.',
            'file.mimes' => 'File không đúng định dạng ( .xlsx, .xls ).',
        ]);

        DB::beginTransaction();

        try {
            $import = new ExamContentImport();
            $import->import($request->file('file'));

            if (count($import->failures()) > 0) {
                $failures = $import->failures();
                $errorMessages = [];

                foreach ($failures as $failure) {
                    $errorMessages[] = [
                        'row' => $failure->row(),
                        'attribute' => $failure->attribute(),
                        'errors' => $failure->errors(),
                        'values' => $failure->values(),
                    ];
                }

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'status' => 422,
                    'data' => $errorMessages,
                    'message' => 'Có lỗi xảy ra trong quá trình nhập dữ liệu. Vui lòng kiểm tra lại file và thử lại.',
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => [],
                'message' => 'Nhập dữ liệu thành công.',
            ], 200);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();

            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }

            return response()->json([
                'success' => false,
                'status' => 422,
                'data' => $errorMessages,
                'message' => 'Có lỗi xảy ra trong quá trình nhập dữ liệu. Vui lòng kiểm tra lại file và thử lại.',
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'status' => 500,
                'data' => [],
                'message' => 'Đã xảy ra lỗi, vui lòng thử lại sau.',
            ], 500);
        }
    }
}
