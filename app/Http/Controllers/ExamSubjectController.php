<?php

namespace App\Http\Controllers;

use App\Exports\ExamSubjectsExport;
use App\Http\Requests\ExamSubject\StoreExamSubjectRequest;
use App\Http\Requests\ExamSubject\UpdateExamSubjectRequest;
use App\Imports\ExamSubjectsImport;
use App\Models\Exam_subject;
use App\Models\ExamSubject;
use Illuminate\Http\Request;
use App\Imports\ExamSubjectImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
class ExamSubjectController extends Controller
{
    /**
     * Lấy môn thi theo kì thi
     */
    public function index()
    {
        $examSubjects = Exam_subject::all();

        return $examSubjects;
    }
    function  getDataShow()
    {
        try {
            $examSubjects = Exam_subject::query()
                ->select('id', 'name', 'exam_id')
                ->get();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examSubjects,
                'message' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'message' => $e->getMessage(),
            ], 422);
        }
    }
    public function getSubjectByExam($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return $this->jsonResponse(false, null, 'ID không hợp lệ', 400);
            }

            $examSubjects = Exam_subject::query()
                ->select('id', 'exam_id', 'name', 'status')
                ->where('exam_id', $id)
                ->get();

            if ($examSubjects->isEmpty()) {
                return $this->jsonResponse(false, null, 'Không tìm thấy môn thi', 404);
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

            $examSubject = Exam_subject::create($validatedData);

            return $this->jsonResponse(true, $examSubject, 'Tạo đề thi thành công', 201);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Thêm môn thi bằng exel
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new ExamSubjectsImport(), $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Import thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị thông tin chi tiết một môn thi
     */
    public function show($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return $this->jsonResponse(false, null, 'ID không hợp lệ', 400);
            }

            $examSubject = Exam_subject::with('contents')
                ->select('id', 'exam_id', 'name', 'status')
                ->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'Không tìm thấy môn thi', 404);
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

            $examSubject = Exam_subject::select('id')->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'Không tìm thấy môn thi', 404);
            }

            $examSubject->update($validatedData);

            return $this->jsonResponse(true, $examSubject, 'Cập nhật môn thi thành công', 200);
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
            $examSubject = Exam_subject::query()->select('id')->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'Không tìm thấy môn thi', 404);
            }

            $examSubject->delete();

            return $this->jsonResponse(true, null, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function updateStatus($id)
    {
        try {
            $examSubject = Exam_subject::query()->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'Không tìm thấy môn thi', 404);
            }

            $examSubject->status = $examSubject->status=='true' ? 'false' : 'true';

            $examSubject->save();

            return $this->jsonResponse(true, $examSubject->status, 'Cập nhật trạng thái đề thi thành công', 200);
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
            $examSubject = Exam_subject::withTrashed()
                ->select('id', 'exam_id', 'name', 'status')
                ->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'Không tìm thấy môn thi', 404);
            }

            if ($examSubject->deleted_at == null) {
                return $this->jsonResponse(false, null, 'Môn thi chưa bị xóa', 409);
            }

            $examSubject->restore();

            return $this->jsonResponse(true, $examSubject, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
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
    public function exportExcel(Request $request)
    {
        try {
            $action = $request->input('action', null);
            $id = $request->input('id', null);

            if ($action === 'exam' && !empty($id)) {
                $data = DB::table('exam_subjects')
                    ->select('id', 'exam_id', 'name', 'status')
                    ->where('exam_id', $id)
                    ->orderBy('id')
                    ->get();
            } else {
                $data = DB::table('exam_subjects')
                    ->select('id', 'exam_id', 'name', 'status')
                    ->orderBy('exam_id')
                    ->orderBy('id')
                    ->get();
            }

            $fileName = 'danh_sach_mon_thi_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download(new ExamSubjectsExport($data), $fileName);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
