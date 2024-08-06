<?php

namespace App\Http\Controllers;

use App\Imports\ExamContentImport;
use App\Models\Exam_content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ExamContentController extends Controller
{

    public function index()
    {
        //
    }

    public function getContentByExam($id)
    {

        try {
            if (!is_string($id) || empty(trim($id))) {
                return response()->json([
                    'success' => false,
                    'status' => '400',
                    'data' => [],
                    'warning' => 'Mã môn thi không hợp lệ.',
                ], 400);
            }

            $content = Exam_content::query()
                ->where('exam_subject_id', $id)
                ->get();

            if ($content->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'Không tìm thấy nội dung cho mã môn thi đã cho.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $content,
                'warning' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi không mong muốn: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'exam_subject_id' => 'required|string',
                'title' => 'required|string|max:255',
            ], [
                'exam_subject_id.required' => 'Mã môn thi là bắt buộc.',
                'exam_subject_id.string' => 'Mã môn thi phải là một chuỗi ký tự.',
                'title.required' => 'Tiêu đề là bắt buộc.',
                'title.string' => 'Tiêu đề phải là một chuỗi ký tự.',
                'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => '422',
                    'data' => null,
                    'warning' => $validator->errors(),
                ], 422);
            }

            $examSubject = Exam_content::create($validator->validated());

            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $examSubject,
                'warning' => ''
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => null,
                'warning' => 'Lỗi xác thực: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => null,
                'warning' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls',
        ], [
            'file.required' => 'Hãy chọn một file để tải lên.',
            'file.mimes' => 'File không đúng định dạng (.xlsx, .xls).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => $validator->errors(),
            ], 422);
        }

        $import = new ExamContentImport();

        try {
            DB::beginTransaction();

            // Import dữ liệu từ file.
            $import->import($request->file('file'));

            // Lấy danh sách các lỗi nếu có.
            $failures = $import->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $row = $failure->row();
                $errors = $failure->errors();


                if (!isset($errorMessages[$row])) {
                    $errorMessages[$row] = [
                        'row' => $row,
                        'errors' => $errors,
                    ];
                } else {

                    $errorMessages[$row]['errors'] = array_merge($errorMessages[$row]['errors'], $errors);
                }
            }
            $errorMessages = array_values($errorMessages);

            if (count($failures) > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'status' => '422',
                    'data' => [],
                    'warning' => $errorMessages,
                ], 422);
            }

            DB::commit();

            $importedData = $import->getImportedData();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $importedData,
                'warning' => 'Nhập dữ liệu thành công.',
            ], 200);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();

            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = [
                    'row' => $failure->row(),
                    'errors' => $failure->errors(),
                ];
            }

            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => $errorMessages,
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('General Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi, vui lòng thử lại sau.',
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return response()->json([
                    'success' => false,
                    'status' => '400',
                    'data' => [],
                    'warning' => 'Mã nội dung không hợp lệ.',
                ], 400);
            }

            $content = Exam_content::query()
                ->where('id', $id)
                ->get();

            if ($content->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'Không tìm thấy nội dung cho mã nội dung đã cho.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $content,
                'warning' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi không mong muốn: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'exam_subject_id' => 'required|string',
                'title' => 'required|string|max:255',
            ], [
                'exam_subject_id.required' => 'Mã môn thi là bắt buộc.',
                'exam_subject_id.string' => 'Mã môn thi phải là một chuỗi ký tự.',
                'title.required' => 'Tiêu đề là bắt buộc.',
                'title.string' => 'Tiêu đề phải là một chuỗi ký tự.',
                'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => '422',
                    'data' => null,
                    'warning' => $validator->errors(),
                ], 422);
            }

            $examSubject = Exam_content::findOrFail($id);

            $examSubject->update($validator->validated());

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examSubject,
                'warning' => ''
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => null,
                'warning' => 'Lỗi xác thực: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => null,
                'warning' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $content = Exam_content::find($id);

            if (!$content) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'Không tìm thấy nội dung cho mã nội dung đã cho.',
                ], 404);
            }

            $content->delete();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [],
                'message' => 'Xóa nội dung thi thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => null,
                'warning' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
