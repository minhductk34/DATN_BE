<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Exam;
use App\Models\Exam_room;
use App\Models\Exam_room_detail;
use App\Models\Exam_session;
use App\Models\Exam_subject;
use App\Models\ExamRoom;
use App\Models\ExamRoomDetail;
use App\Models\ExamSession;
use App\Models\ExamSubject;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExamRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $examRooms = Exam_room::withCount('candidates')->get();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examRooms,
                'message' => '',
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Không thể lấy dữ liệu từ cơ sở dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'exam_id' => 'required|exists:exams,id',
            ]);

            $examRoom = Exam_room::create($validatedData);

            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $examRoom,
                'message' => '',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'message' => 'validation error',
                'error' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function createRoom($name,$exam_id)
    {
        try {

            $examRoom = Exam_room::create(['name'=>$name,'exam_id'=>$exam_id]);

            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $examRoom,
                'message' => '',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'message' => 'validation error',
                'error' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */

    public function show($id)
    {
        try {
            $examRoom = Exam_room::query()->select('id', 'name')->withCount('candidates')->where('exam_id', $id)->get();

            if (!$examRoom) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'Exam Room không tồn tại',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' =>  $examRoom,
                'message' => '',
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Không thể lấy dữ liệu từ cơ sở dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showDetail($id)
    {
        try {
            // Tìm phòng thi và đếm số lượng thí sinh
            $examRoom = Exam_room::withCount('candidates')->find($id);

            if (!$examRoom) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'Exam Room không tồn tại',
                ], 404);
            }

            // Lấy chi tiết phòng thi
            $examRoomDetails = Exam_room_detail::with(['exam_subject', 'exam_session'])
                ->where('exam_room_id', $id)
                ->get();

            if ($examRoomDetails->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'Exam Room Details không tồn tại',
                ], 404);
            }

            // Lấy danh sách môn thi
            $examSubjectIds = $examRoomDetails->pluck('exam_subject_id')->unique();
            $examSubjects = Exam_subject::query()->whereIn('id', $examSubjectIds)->get();

            // Lấy danh sách ca thi
            $examSessionIds = $examRoomDetails->pluck('exam_session_id')->unique();
            $examSessions = Exam_session::query()
                ->whereIn('id', $examSessionIds)
                ->select('id', 'name', 'time_start', 'time_end')
                ->get();

            // Format dữ liệu cho response
            $formattedSubjects = $examRoomDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'subject_name' => $detail->exam_subject->name,
                    'session_name' => $detail->exam_session->name,
                    'time_start' => $detail->exam_session->time_start,
                    'time_end' => $detail->exam_session->time_end,
                    'exam_date' => $detail->exam_date,
                    'exam_subject_id' => $detail->exam_subject_id,
                    'exam_session_id' => $detail->exam_session_id
                ];
            });

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'examRoom' => [
                        'id' => $examRoom->id,
                        'name' => $examRoom->name,
                        'exam_id' => $examRoom->exam_id,
                        'candidates_count' => $examRoom->candidates_count
                    ],
                    'examSubjects' => $formattedSubjects,
                    'exam_room_details' => $examRoomDetails,
                    'exam_sessions' => $examSessions,
                    'exam_subjects' => $examSubjects,
                ],
                'message' => '',
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Không thể lấy dữ liệu từ cơ sở dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate input
            $request->validate([
                'name' => 'required|string',
                'exam_session_id' => 'required|exists:exam_sessions,id'
            ]);

            // Cập nhật tên phòng thi
            $examRoom = Exam_room::findOrFail($id);
            $examRoom->update([
                'name' => $request->name
            ]);

            // Cập nhật ca thi trong exam_room_detail
            $examRoomDetail = Exam_room_detail::where('exam_room_id', $id)->first();
            if ($examRoomDetail) {
                $examRoomDetail->update([
                    'exam_session_id' => $request->exam_session_id
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'message' => 'Cập nhật thành công',
                'data' => [
                    'examRoom' => $examRoom,
                    'examRoomDetail' => $examRoomDetail
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'message' => 'Không tìm thấy phòng thi',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'message' => 'Lỗi khi cập nhật phòng thi: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $examRoom = Exam_room::find($id);

        if (!$examRoom) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => '',
                'message' => 'Không tìm thấy phòng',
                'error' => '404 not found!'
            ], 404);
        }
        try {
            $examRoom->delete();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [],
                'message' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function dataSelectUpdate($id) {
        try {
            $exam_room = Exam_room::select('exam_rooms.*')
                ->with('examRoomDetail')
                ->join('exams', 'exam_rooms.exam_id', '=', 'exams.id')
                ->leftJoin('exam_subjects', 'exams.id', '=', 'exam_subjects.exam_id')
                ->where('exam_rooms.id', $id)
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'exam_room' => $exam_room,
                    'exam' => Exam::select('id', 'name')->get(),
                    'exam_sessions' => Exam_session::select('id', 'name')->get(),
                    'exam_subjects' => Exam_subject::where('exam_id', $exam_room->exam_id)
                        ->select('id', 'name')
                        ->get()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
