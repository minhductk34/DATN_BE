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
            $examRoom = Exam_room::withCount('candidates')->find($id);

            if (!$examRoom) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'Exam Room không tồn tại',
                ], 404);
            }

            $examRoomDetails = Exam_room_detail::query()->where('exam_room_id', $id)->get();

            if ($examRoomDetails->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'Exam Room Details không tồn tại',
                ], 404);
            }

            $examSubjectIds = $examRoomDetails->pluck('exam_subject_id')->unique();
            $examSubjects = Exam_subject::query()->whereIn('id', $examSubjectIds)->get();

            $examSessionIds = $examRoomDetails->pluck('exam_session_id')->unique();
            $examSessions = Exam_session::query()
                ->whereIn('id', $examSessionIds)
                ->select('id', 'name', 'time_start', 'time_end')
                ->get();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'examRoom' => $examRoom,
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
            $validatedData = $request->validate([
                'Name' => 'required|max:255',
                'exam_id' => 'required|exists:exams,id'
            ]);

            $examRoom->update($validatedData);

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examRoom,
                'message' => '',
            ], 200);
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
}
