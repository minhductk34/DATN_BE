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
    public function createRoom($name, $exam_id)
    {
        try {

            $examRoom = Exam_room::create(['name' => $name, 'exam_id' => $exam_id]);

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

            $examRoomDetails = Exam_room_detail::with(['exam_subject', 'exam_session'])
                ->whereHas('exam_subject', function ($query) use ($examRoom) {
                    $query->where('exam_id', $examRoom->exam_id);
                })
                ->where('exam_room_id', $id)
                ->get();
            $examSubjects = Exam_subject::query()->where('exam_id', $examRoom->exam_id)->get();
            $formattedExamSubjects = [];
            foreach ($examSubjects as $subject) {
                $isSubjectInRoomDetails = $examRoomDetails->firstWhere('exam_subject_id', $subject->id);

                if ($isSubjectInRoomDetails) {
                    if ($isSubjectInRoomDetails->exam_session_id != null) {
                        $formattedExamSubjects[] = [
                            'id' => $subject->id,
                            'name' => $subject->name,
                            'time_start' => $isSubjectInRoomDetails->exam_session->time_start ?? null,
                            'time_end' => $isSubjectInRoomDetails->exam_session->time_end ?? null,
                            'exam_date' => $isSubjectInRoomDetails->exam_date ?? null,
                            'exam_end' => $isSubjectInRoomDetails->exam_end ?? null,
                        ];
                    } else {
                        $formattedExamSubjects[] = [
                            'id' => $subject->id,
                            'name' => $subject->name,
                            'time_start' => null,
                            'time_end' => null,
                            'exam_date' => $isSubjectInRoomDetails->exam_date ?? null,
                            'exam_end' => $isSubjectInRoomDetails->exam_end ?? null,
                        ];
                    }
                } else {
                    $formattedExamSubjects[] = [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'time_start' => null,
                        'time_end' => null,
                        'exam_date' => null,
                        'exam_end' => null,
                    ];
                }
            }

            // Lấy danh sách ca thi
            $examSessions = Exam_session::all();

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
                    'exam_room_details' => $examRoomDetails,
                    'exam_sessions' => $examSessions,
                    'exam_subjects' => $formattedExamSubjects,
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
            $validatedData = $request->validate([
                'exam_subject_id' => 'required|exists:exam_subjects,id',
                'exam_date' => 'required|date',
                'exam_session_id' => 'nullable|exists:exam_sessions,id',
                'exam_end' => 'nullable|date|after_or_equal:exam_date',
            ]);

            $examRoom = Exam_room::findOrFail($id);

            $updateData = [
                'exam_date' => $validatedData['exam_date'],
                'exam_session_id' => $validatedData['exam_session_id'] ?? null,
                'exam_end' => $validatedData['exam_end'] ?? null,
            ];

            $examRoomDetail = Exam_room_detail::updateOrCreate(
                [
                    'exam_room_id' => $id,
                    'exam_subject_id' => $validatedData['exam_subject_id']
                ],
                $updateData
            );

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Cập nhật thành công',
                'data' => [
                    'exam_room_detail' => $examRoomDetail
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Không tìm thấy phòng thi',
                'data' => null
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
                'data' => null
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Lỗi khi cập nhật phòng thi: ' . $e->getMessage(),
                'data' => null
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

    public function dataSelectUpdate($exam_room_id, $exam_subject_id)
    {
        try {
            $exam_room_detail = Exam_room_detail::query()
                ->where('exam_room_id', $exam_room_id)
                ->where('exam_subject_id', $exam_subject_id)
                ->first();

            $exam_session = optional($exam_room_detail)->exam_session;
            return response()->json([
                'success' => true,
                'data' => [
                    'exam_session' => $exam_session ?? null,
                    'exam_date' => $exam_room_detail->exam_date ?? null,
                    'exam_end' => $exam_room_detail->exam_end ?? null,
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
    public function getExamRoomsByExam($examId)
    {
        try {
            $examRooms = Exam_room::query()
                ->where('exam_id', $examId)
                ->withCount('candidates')
                ->get();

            if ($examRooms->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'message' => 'Không tìm thấy phòng thi cho kỳ thi này',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $examRooms,
                'message' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'message' => 'Đã xảy ra lỗi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
