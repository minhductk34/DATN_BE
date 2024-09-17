<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamSubject\StoreExamSubjectRequest;
use App\Http\Requests\ExamSubject\UpdateExamSubjectRequest;
use App\Models\ExamSubject as ExamSubject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExamSubjectImport;
use Illuminate\Support\Facades\DB;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Collection;

class ExamSubjectController extends Controller
{
    /**
     * Lấy môn thi theo kì thi
     */
    public function index()
    {
        $examSubjects = ExamSubject::all();

        return $examSubjects;
    }
    function  getDataShow()
    {
        try {
            $examSubjects = ExamSubject::query()
                ->select('id', 'name', 'exam_id')
                ->get();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examSubjects,
                'warning' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => $e->getMessage(),
            ], 422);
        }
    }
    public function getSubjectByExam($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return $this->jsonResponse(false, null, 'Invalid exam_subject_id', 400);
            }

            $examSubjects = ExamSubject::query()
                ->select('id', 'exam_id', 'Name', 'Status', 'TimeStart', 'TimeEnd')
                ->where('exam_id', $id)
                ->get();

            if ($examSubjects->isEmpty()) {
                return $this->jsonResponse(false, null, 'No subject found for the given exam_id', 404);
            }

            return $this->jsonResponse(true, $examSubjects, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Thêm môn thi
     */
    public function store(StoreExamSubjectRequest $request)
    {
        try {
            $validatedData = $request->all();

            $examSubject = ExamSubject::create($validatedData);

            return $this->jsonResponse(true, $examSubject, '', 201);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Thêm môn thi bằng exel
     */
    public function importExcel(Request $request)
    {
        $request->validate(
            [
                'file' => 'required|mimes:xlsx,xls',
            ],
            [
                'file.required' => 'Hãy chọn một file để tải lên',
                'file.mimes' => 'File không đúng định dạng ( .xlsx, .xls )',
            ]
        );

        try {
            DB::beginTransaction();

            $import = new ExamSubjectImport();
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

    /**
     * Hiển thị thông tin chi tiết một môn thi
     */
    public function show($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return $this->jsonResponse(false, null, 'Invalid exam_subject_id', 400);
            }

            $examSubject = ExamSubject::with('contents')
                ->select('id', 'exam_id', 'Name', 'Status', 'TimeStart', 'TimeEnd')
                ->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'No content found for the given exam_content_id', 404);
            }

            return $this->jsonResponse(true, $examSubject, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Cập nhật thông tin môn thi
     */
    public function update(UpdateExamSubjectRequest $request, $id)
    {
        try {
            $validatedData = $request->all();

            $examSubject = ExamSubject::select('id')->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'No subject found for the given exam_subject_id', 404);
            }

            $examSubject->update($validatedData);

            return $this->jsonResponse(true, $examSubject, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Xóa môn thi ( xóa mềm )
     */
    public function destroy($id)
    {
        try {
            $examSubject = ExamSubject::query()->select('id')->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'No subject found for the given exam_subject_id', 404);
            }

            $examSubject->delete();

            return response()->json([
                'success' => true,
                'status' => '204',
                'message' => 'Xóa thành công'
            ], 204);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Khôi phục môn thi bị xóa
     */
    public function restore($id)
    {
        try {
            $examSubject = ExamSubject::withTrashed()
                ->select('id', 'exam_id', 'Name', 'Status', 'TimeStart', 'TimeEnd', 'deleted_at')
                ->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'No subject found for the given exam_subject_id', 404);
            }

            if ($examSubject->deleted_at == null) {
                return $this->jsonResponse(false, null, 'The exam has not been deleted', 409);
            }

            $examSubject->restore();

            return $this->jsonResponse(true, $examSubject, 'Khôi phục thành công', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
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
