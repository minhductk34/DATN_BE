<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
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
            $examRooms = ExamRoom::all();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examRooms,
                'warning' => '',
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Không thể lấy dữ liệu từ cơ sở dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi không xác định',
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
                'poetry_id' => 'required|exists:poetries,id',
                'Name' => 'required|max:255',
                'Quantity' => 'required|integer',
                'Status' => 'required|in:active,inactive',
            ]);

            $examRoom = ExamRoom::create($validatedData);

            return response()->json([
                'success' => true,
                'status' => '201',
                'data' => $examRoom,
                'warning' => '',
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $examRoom = ExamRoom::find($id);
            $examRoomDetails = ExamRoomDetail::query()->where('exam_room_id', $id)->get();
            $countCandidate = Candidate::query()->where('exam_room_id', $id)->count();

            if ($examRoomDetails instanceof \Illuminate\Support\Collection && $examRoomDetails->count() > 1) {
                $examRoomDetails = $examRoomDetails->first();
            } elseif ($examRoomDetails->count() === 1) {
                $examRoomDetails = $examRoomDetails->first();
            } else {
                $examRoomDetails = null;
            }

            $examSubjectName = ExamSubject::query()->where('id',$examRoomDetails->exam_subject_id)->first()->Name;
            $examSession = ExamSession::query()->where('id',$examRoomDetails->exam_session_id)->select('Name','TimeStart','TimeEnd')->first();

            $examSessionName = $examSession->Name;
            $examSessionTimeStart = $examSession->TimeStart;
            $examSessionTimeEnd = $examSession->TimeEnd;


            if (!$examRoom) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'Exam Room không tồn tại',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [
                    'examRoom' => $examRoom,
                    'exam_session_name' => $examSessionName,
                    'examSessionTimeStart' => $examSessionTimeStart,
                    'examSessionTimeEnd' => $examSessionTimeEnd,
                    'exam_subject_name'=> $examSubjectName,
                    'countCandidate' => $countCandidate,
                ],
                'warning' => '',
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Không thể lấy dữ liệu từ cơ sở dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi không xác định',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $examRoom = ExamRoom::find($id);

        if (!$examRoom) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => [],
                'warning' => 'Exam Room không tồn tại',
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'poetry_id' => 'required|exists:poetries,id',
            ]);

            $examRoom->update($validatedData);

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examRoom,
                'warning' => '',
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'warning' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $examRoom = ExamRoom::find($id);

        if (!$examRoom) {
            return response()->json([
                'success' => false,
                'status' => '404',
                'data' => [],
                'warning' => 'Exam Room không tồn tại',
            ], 404);
        }

        $examRoom->delete();

        return response()->json([
            'success' => true,
            'status' => '200',
            'data' => [],
            'warning' => '',
        ], 200);
    }
}
