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
        // Nếu cần, có thể thêm logic để trả về danh sách tất cả nội dung
    }

    public function getContentByExam($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return $this->jsonResponse(false, [], 'Mã môn thi không hợp lệ.', 400);
            }

            $content = Exam_content::where('exam_subject_id', $id)->get();

            if ($content->isEmpty()) {
                return $this->jsonResponse(false, [], 'Không tìm thấy nội dung cho mã môn thi đã cho.', 404);
            }

            return $this->jsonResponse(true, $content);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, [], 'Đã xảy ra lỗi không mong muốn: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
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
            return $this->jsonResponse(false, null, $validator->errors(), 422);
        }

        try {
            $examSubject = Exam_content::create($validator->validated());

            return $this->jsonResponse(true, $examSubject, 'Thêm mới thành công', 201);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, 'Đã xảy ra lỗi: ' . $e->getMessage(), 500);
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
            return $this->jsonResponse(false, [], $validator->errors(), 422);
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
                return $this->jsonResponse(false, [], $errorMessages, 422);
            }

            DB::commit();

            $importedData = $import->getImportedData();

            return $this->jsonResponse(true, $importedData, 'Nhập dữ liệu thành công.');
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

            return $this->jsonResponse(false, [], $errorMessages, 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('General Error: ' . $e->getMessage());

            return $this->jsonResponse(false, [], 'Đã xảy ra lỗi, vui lòng thử lại sau.', 500);
        }
    }

    public function show($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return $this->jsonResponse(false, [], 'Mã nội dung không hợp lệ.', 400);
            }

            $content = Exam_content::findOrFail($id);

            return $this->jsonResponse(true, $content);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, [], 'Đã xảy ra lỗi không mong muốn: ' . $e->getMessage(), 500);
        }
    }

    public function update($id, Request $request)
    {
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
            return $this->jsonResponse(false, null, $validator->errors(), 422);
        }

        try {
            $examSubject = Exam_content::findOrFail($id);
            $examSubject->update($validator->validated());

            return $this->jsonResponse(true, $examSubject,'Sửa đổi thông tin thành công',200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, 'Đã xảy ra lỗi: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $content = Exam_content::findOrFail($id);
            $content->delete();

            return $this->jsonResponse(true, [], 'Xóa nội dung thi thành công');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, 'Đã xảy ra lỗi: ' . $e->getMessage(), 500);
        }
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
