<?php

namespace App\Http\Controllers;

use App\Events\StudentSubmitted;
use App\Models\Active;
use App\Models\Candidate;
use App\Models\Exam;
use App\Models\Exam_room;
use App\Models\Exam_subject;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CandidatesExport;
use App\Imports\CandidatesImport;
use App\Models\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class CandidateController extends Controller
{
    /**
     * Lấy danh sách tất cả các ứng viên.
     */

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('username', 'password');

            if (empty($credentials['username']) || empty($credentials['password'])) {
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'data' => [],
                    'message' => 'username và password là bắt buộc.'
                ], 400);
            }

            $admin = Password::query()->with('candidate')->where('idcode', $credentials['username'])->first();

            if (!$admin) {

                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'data' => [],
                    'message' => 'Tài khoản không tồn tại.'
                ], 404);
            }


                    $decryptedPassword = Crypt::decrypt($admin->password);
                    if ($credentials['password'] !== $decryptedPassword) {
                        return response()->json([
                            'success' => false,
                            'status' => 401,
                            'data' => [],
                            'message' => 'Mật khẩu không chính xác.'
                        ], 401);
                    }


            if (!$this->checkRedisConnection()) {
                return response()->json([
                    'success' => false,
                    'status' => 503,
                    'data' => [],
                    'message' => 'Không thể kết nối đến hệ thống lưu trữ, vui lòng thử lại sau.'
                ], 503);
            }

            $existingToken = $this->retryRedisOperation(function () use ($admin) {
                return Redis::hget('auth:' . $admin->idcode, 'token');
            });

            // dd($existingToken);

            if ($existingToken) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'data' => [],
                    'message' => 'Tài khoản này đã đăng nhập từ nơi khác.'
                ], 403);
            }


            $token = str::random(60);
            $expiresAt = now()->addHours(1)->timestamp;
            $ttl = now()->addHours(1)->diffInSeconds(now());

            $tokenData = [
                'id_code' => $admin->idcode,
                'id_exam'=>$admin->candidate->exam_id,
                'expires_at' => $expiresAt,
            ];

            $this->retryRedisOperation(function () use ($token, $tokenData, $admin, $ttl) {
                Redis::hmset('tokens:' . $token, $tokenData);
                Redis::hmset('auth:' . $admin->idcode, ['token' => $token, 'expires_at' => $ttl]);
                Redis::expire('tokens:' . $token, $ttl);
                Redis::expire('auth:' . $admin->idcode, $ttl);
            });

            $data = [
                'idcode' => $admin->idcode,
                'id_exam'=>$admin->candidate->exam_id,
            ];

            return response()->json([
                'success' => true,
                'status' => 200,
                'expires_at' => $expiresAt,
                'token' => $token,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'status' => 500,
                'data' => [],
                'message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function checkRedisConnection()
    {
        try {
            Redis::ping();
            return true;
        } catch (\Exception $e) {

            return false;
        }
    }

    private function retryRedisOperation(callable $operation, $maxRetries = 3)
    {
        $attempts = 0;
        while ($attempts < $maxRetries) {
            try {
                return $operation();
            } catch (\Exception $e) {
                $attempts++;

                if ($attempts >= $maxRetries) {

                    throw $e;
                }

                usleep(200000);
            }
        }
        return null;
    }

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
                'create_by' => 'nullable|string',
            ], $this->validationMessages());

            $exam = Exam::query()->select('id')
                ->orderBy('created_at', 'desc')
                ->first();
                Log::debug('Storing token in Redis', ['token' => $exam]);
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
                'create_by' => $validated['create_by'] ?? null,
            ]);
            Password::create([
                'idcode' => $candidate->idcode,
                'password' => Crypt::encrypt($validated['password'])]);
            $data = [
                'idcode' => $validated['idcode'],
                'exam_id' => $exam->id,
                'exam_room_id' => $examRoom->id,
                'name' => $validated['name'],
                'image' => $imagePath,
                'dob' => $validated['dob'],
                'address' => $validated['address'],
                'email' => $validated['email'],
                'status' => true,
                'password' => $validated['password'],
            ];
            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'candidate' => $data,
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
            $candidate = Candidate::query()->with('exam', 'exam_room')->find($id);
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

            $exam_subject = Exam_subject::query()->where('exam_id',$candidate->exam_id)->get();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'candidate' => $candidate,
                    'actives' => $actives,
                    'exam_subject' => $exam_subject,
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
    public function toggleActiveStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'exam_subject_id' => 'required|exists:exam_subjects,id',
                'idcode' => 'required|exists:candidates,idcode',
            ]);

            // Tìm active record nếu đã tồn tại
            $active = Active::where('exam_subject_id', $validated['exam_subject_id'])
                ->where('idcode', $validated['idcode'])
                ->first();

            if ($active) {
                $active->status = !$active->status;
                $active->save();
            } else {
                // Nếu chưa tồn tại thì tạo mới với status = true
                $active = Active::create([
                    'exam_subject_id' => $validated['exam_subject_id'],
                    'idcode' => $validated['idcode'],
                    'status' => true
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $active
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function info($id){
        try {
            $candidate = Candidate::query()->find($id);
            if (!$candidate) {
                return response()->json([
                    'success' => false,
                    'status' => "404",
                    'data' => [],
                    'message' => 'Candidate not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $candidate,
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
                ->where('idcode', $id)
                ->first();

            $validated = $request->validate([
                'idcode' => "sometimes|required|string|max:255|unique:candidates,idcode,{$candidate->idcode}",
                'exam_id' => 'sometimes|required|string',
                'exam_room_id' => 'sometimes|required|exists:exam_rooms,id',
                'name' => 'sometimes|required|string|max:255',
                'image' => 'sometimes|nullable|string',
                'dob' => 'sometimes|required|date',
                'address' => 'sometimes|nullable|string|max:255',
                'email' => "sometimes|required|string|email|max:255|unique:candidates,email,{$candidate->idcode}",
                'status' => 'sometimes|required|boolean',
                'create_by' => 'sometimes|nullable|string',
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
                if (!$exam) {
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
                if (!$exam) {
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
            \Log::error('Export Excel Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Validation messages in Vietnamese.
     */
    protected function validationMessages()
    {
        return [
            'idcode.required' => 'Mã ID là bắt buộc.',
            'idcode.unique' => 'Mã ID đã tồn tại.',
            'exam_id.required' => 'Mã kỳ thi là bắt buộc.',
            'exam_room_id.required' => 'Mã phòng thi là bắt buộc.',
            'exam_room_id.exists' => 'Phòng thi không tồn tại.',
            'name.required' => 'Họ tên là bắt buộc.',
            'dob.required' => 'Ngày sinh là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.boolean' => 'Trạng thái phải là true hoặc false.',
        ];
    }

    public function countCandidateForExamRoom($examRoomId)
    {
        try {
            $candidateCount = Candidate::where('exam_room_id', $examRoomId)->count();
            $examRoom = Exam_room::query()->where('id', $examRoomId)->select('id', 'Name')->first();
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

            $candidates->transform(function ($candidate) {
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

    public function finish(Candidate $candidate)
    {
        broadcast(new StudentSubmitted($candidate->exam_room_id, $candidate))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công.',
            'data' => [],
        ]);
    }
}
