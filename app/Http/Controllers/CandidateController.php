<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\ExamRoom;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CandidatesExport;
use App\Imports\CandidatesImport;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class CandidateController extends Controller
{
    /**
     * Lấy danh sách tất cả các ứng viên.
     */
    public function index()
    {
        $candidates = Candidate::all();
        return $this->jsonResponse(true, $candidates, '', 200);
    }

    /**
     * Lưu một ứng viên mới.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu từ request
        $validated = $request->validate([
            'Idcode' => 'required|string|max:255|unique:candidates',
            'exam_id' => 'required|string|exists:exams,id',
            'Fullname' => 'required|string|max:255',
            'Image' => 'nullable|string',
            'DOB' => 'required|date',
            'Address' => 'required|string|max:255',
            'Examination_room' => 'required|string|max:255',
            'Password' => 'required|string|min:8',
            'Email' => 'required|string|email|max:255|unique:candidates',
        ], $this->validationMessages());

        $candidate = Candidate::create($validated);
        return $this->jsonResponse(true, $candidate, 'Thêm ứng viên thành công.', 201);
    }

    /**
     * Hiển thị thông tin một ứng viên cụ thể.
     */
    public function show($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            return $this->jsonResponse(true, $candidate, '', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->jsonResponse(false, null, 'Ứng viên không tìm thấy.', 404);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, 'Đã xảy ra lỗi: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cập nhật thông tin của ứng viên.
     */
    public function update($id, Request $request)
    {

        try {
            $candidate = Candidate::query()
            ->where('Idcode', $id)
            ->first();

            // Validate dữ liệu từ request
            $validated = $request->validate([
                'Idcode' => "sometimes|required|string|max:255|unique:candidates,Idcode,{$candidate->Idcode}",
                'exam_id' => 'sometimes|required|integer',
                'Fullname' => 'sometimes|required|string|max:255',
                'Image' => 'sometimes|nullable|string',
                'DOB' => 'sometimes|required|date',
                'Address' => 'sometimes|required|string|max:255',
                'Examination_room' => 'sometimes|required|string|max:255',
                'Password' => 'sometimes|required|string|min:8',
                'Email' => "sometimes|required|string|email|max:255|unique:candidates,Email,{$candidate->Idcode}",
                'Status' => 'sometimes|required|string|max:50',
            ], $this->validationMessages());

            $candidate->update($validated);
            return $this->jsonResponse(true, $candidate, 'Cập nhật ứng viên thành công.', 200);
        } catch (ValidationException $e) {
            return $this->jsonResponse(false, null, $e->errors(), 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->jsonResponse(false, null, 'Ứng viên không tìm thấy.', 404);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, 'Đã xảy ra lỗi: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Xóa một ứng viên khỏi cơ sở dữ liệu.
     */
    public function destroy($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            $candidate->delete();
            return $this->jsonResponse(true, null, 'Xóa ứng viên thành công.', 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->jsonResponse(false, null, 'Ứng viên không tìm thấy.', 404);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, 'Đã xảy ra lỗi: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Import danh sách ứng viên từ file Excel.
     */
    public function importExcel(Request $request)
    {
        // Validate file upload
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ], [
            'file.required' => 'Hãy chọn một file để tải lên.',
            'file.mimes' => 'File không đúng định dạng (.xlsx, .xls).',
        ]);

        try {
            Excel::import(new CandidatesImport, $request->file('file'));
            return $this->jsonResponse(true, null, 'Nhập dữ liệu thành công.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
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

            return $this->jsonResponse(false, $errorMessages, 'Có lỗi xảy ra khi nhập dữ liệu.', 422);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, 'Đã xảy ra lỗi khi nhập dữ liệu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Export danh sách ứng viên ra file Excel.
     */
    public function exportExcel(Request $request)
    {
        try {
            // Lấy dữ liệu từ cơ sở dữ liệu và nhóm theo phòng thi
            $candidatesByRoom = Candidate::all()->groupBy('exam_room_id');
           
            if ($candidatesByRoom->isEmpty() || $candidatesByRoom == []) {
                return response()->json(['error' => 'Không có dữ liệu'], 400);
            }
    
            $fileName = 'danh_sach_ung_vien_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
           
            return Excel::download(new CandidatesExport($candidatesByRoom), $fileName);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Validation messages in Vietnamese.
     */
    protected function validationMessages()
    {
        return [
            'Idcode.required' => 'Mã ID là bắt buộc.',
            'Idcode.unique' => 'Mã ID đã tồn tại.',
            'exam_id.required' => 'Mã kỳ thi là bắt buộc.',
            'exam_id.exists' => 'Mã kỳ thi không tồn tại.',
            'Fullname.required' => 'Họ tên là bắt buộc.',
            'DOB.required' => 'Ngày sinh là bắt buộc.',
            'Address.required' => 'Địa chỉ là bắt buộc.',
            'Examination_room.required' => 'Phòng thi là bắt buộc.',
            'Password.required' => 'Mật khẩu là bắt buộc.',
            'Password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'Email.required' => 'Email là bắt buộc.',
            'Email.email' => 'Email không hợp lệ.',
            'Email.unique' => 'Email đã tồn tại.',
            'Status.required' => 'Trạng thái là bắt buộc.',
        ];

    }
    public function countCandidateForExamRoom($examRoomId)
    {
        try {
            $candidateCount = Candidate::where('exam_room_id', $examRoomId)->count();
            $examRoom =ExamRoom::query()->where('id', $examRoomId)->select('id','Name')->first();
            return response()->json([
                'success' => true,
                'status' => '200',
                'exam_room_id' => $examRoom->id,
                'exam_room_name' => $examRoom->Name,
                'candidate_count' => $candidateCount,
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'message' => 'Không thể lấy dữ liệu từ cơ sở dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'message' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
