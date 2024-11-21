<?php

namespace App\Http\Controllers;

use App\Models\Active;
use App\Models\Candidate;
use App\Models\Exam;
use App\Models\Exam_room;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CandidatesExport;
use App\Imports\CandidatesImport;
use Illuminate\Support\Facades\DB;
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
        try {
            $validated = $request->validate([
                'idcode' => 'required|string|max:255|unique:candidates',
                'name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'dob' => 'required|date',
                'address' => 'required|string|max:255',
                'password' => 'required|string|min:8',
                'email' => 'required|string|email|max:255|unique:candidates',
            ], $this->validationMessages());

            $exam = Exam::query()->select('id')
                ->orderBy('created_at', 'desc')
                ->first();
            if (!$exam) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'No available exam'
                ], 404);
            }

            $examRoom = Exam_room::withCount('candidates')
                ->where('exam_id', $exam->id)
                ->having('candidates_count', '<', 35)
                ->first();
            if (!$examRoom) {
                $ExamRoomController = new ExamRoomController();
                $examRoom = $ExamRoomController->createRoom('Phòng tự sinh', $exam->id);
            }

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('img/candidate');
                $imagePath = str_replace('public/', 'storage/', $imagePath);
            } else {
                $imagePath = 'default/user.png';
            }

            $candidate = Candidate::create([
                'idcode' => $validated['idcode'],
                'exam_id' => $exam->id,
                'exam_room_id' => $examRoom->id,
                'name' => $validated['name'],
                'image' => $imagePath,
                'dob' => $validated['dob'],
                'address' => $validated['address'],
                'email' => $validated['email'],
                'status' => true,
                'password' => Crypt::encrypt($validated['password'])
            ]);

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'candidate' => $candidate,
                ],
                'message' => 'Candidate created successfully'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => "422",
                'data' => [$request->all()],
                'error' => $e->getMessage(),
                'message' => 'Validation error'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Internal server error while processing your request'
            ], 500);
        }
    }


    /**
     * Hiển thị thông tin một ứng viên cụ thể.
     */
    public function show($id)
    {
        try {
            $candidate = Candidate::query()->with('exam','exam_room')->find($id);
            if (!$candidate) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'message' => 'Candidate not found'
                ], 404);
            }

            if ($candidate->image && $candidate->image != 'default/user.png') {
                $candidate->image = Storage::url($candidate->image);
            }

            $actives = Active::query()
                ->where('idcode', $candidate->idcode)
                ->with('exam_subject')
                ->get();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'candidate'=>$candidate,
                    'actives' => $actives
                ],
                'message' => 'Data retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Internal server error while processing your request'
            ], 500);
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
            $validated = $request->validate([
                'action' => 'nullable|string|max:20',
                'id' => 'nullable|string|max:100',
            ]);
            if ($validated['action'] == 'exam' && !empty($validated['id'])) {
                $exam = Exam::find($validated['id']);
                if (!$exam){
                    return response()->json([
                        'success' => false,
                        'status' => "404",
                        'data' => [],
                        'message' => 'Exam not found'
                    ], 404);
                }
                $data = DB::table('candidates')
                ->join('passwords', 'passwords.idcode', '=', 'candidates.idcode')
                ->join('exam_rooms', 'exam_rooms.id', '=', 'candidates.exam_room_id')
                ->select(
                    'exam_rooms.name as name_room',
                    'passwords.idcode',
                    'candidates.name as name_candidate',
                    'passwords.password'
                )
                ->where('candidates.exam_id', $validated['id'])
                ->orderBy('candidates.exam_room_id')
                ->get();
                $data->each(function ($item) {
                    $item->password = Crypt::decrypt($item->password);
                });
            } elseif ($validated['action'] == 'exam_room' && !empty($validated['id'])) {
                $exam = Exam_room::find($validated['id']);
                if (!$exam){
                    return response()->json([
                        'success' => false,
                        'status' => "404",
                        'data' => [],
                        'message' => 'Exam room not found'
                    ], 404);
                }
                $data = DB::table('candidates')
                ->join('passwords', 'passwords.idcode', '=', 'candidates.idcode')
                ->join('exam_rooms', 'exam_rooms.id', '=', 'candidates.exam_room_id')
                ->select(
                    'exam_rooms.name as name_room',
                    'passwords.idcode',
                    'candidates.name as name_candidate',
                    'passwords.password'
                )->where('candidates.exam_room_id', $validated['id'])
                ->orderBy('candidates.exam_room_id')
                ->get();
                $data->each(function ($item) {
                    $item->password = Crypt::decrypt($item->password);
                });

            } elseif (empty($validated['action']) && empty($validated['id'])) {
                $data = DB::table('candidates')
                ->join('passwords', 'passwords.idcode', '=', 'candidates.idcode')
                ->join('exam_rooms', 'exam_rooms.id', '=', 'candidates.exam_room_id')
                ->select(
                    'exam_rooms.name as name_room',
                    'passwords.idcode',
                    'candidates.name as name_candidate',
                    'passwords.password'
                )
                ->orderBy('candidates.exam_room_id')
                ->get();
                $data->each(function ($item) {
                    $item->password = Crypt::decrypt($item->password);
                });

            } else {
                return response()->json([
                    'success' => false,
                    'status' => '422',
                    'data' => [],
                    'message' => 'validation error',
                    'error' => "wrong action passed in, there are actions like 'exam id + action', 'exam room id + action' and 'empty + empty'",
                ], 422);
            }

            $fileName = 'danh_sach_ung_vien_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
//            \Log::info('Total data count: ' . $data->count());
//            \Log::info('Data first few items: ' . json_encode($data->take(5)));
            return Excel::download(new CandidatesExport($data), $fileName);
//            return response()->json($data);
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
            $examRoom =Exam_room::query()->where('id', $examRoomId)->select('id','Name')->first();
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
    public function CandidateInExamRoom($examRoomId)
    {
        try {
            $candidates = Candidate::query()
                ->select('idcode', 'name', 'image', 'dob', 'address')
                ->where('exam_room_id', $examRoomId)
                ->get();

            // Kiểm tra xem có ứng viên nào không
            if ($candidates->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'message' => 'Structure not found'
                ], 404);
            }

            $candidates->transform(function($candidate) {
                $candidate->image = Storage::url($candidate->image);
                return $candidate;
            });

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $candidates,
                'message' => 'Data retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Internal server error while processing your request'
            ], 500);
        }
    }
}
