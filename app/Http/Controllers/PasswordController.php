<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Exam;
use App\Models\Exam_room;
use App\Models\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function actionExport(Request $request)
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
                return response()->json([
                    'success' => true,
                    'status' => '200',
                    'data' => $data,
                    'message' => '',
                ], 200);
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
                return response()->json([
                    'success' => true,
                    'status' => '200',
                    'data' => $data,
                    'message' => '',
                ], 200);
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
                return response()->json([
                    'success' => true,
                    'status' => '200',
                    'data' => $data,
                    'message' => '',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => '422',
                    'data' => [],
                    'message' => 'validation error',
                    'error' => "wrong action passed in, there are actions like 'exam id + action', 'exam room id + action' and 'empty + empty'",
                ], 422);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => [],
                'message' => 'validation error',
                'error' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "500",
                'data' => [],
                'error' => $e->getMessage(),
                'message' => 'Lỗi máy chủ nội bộ khi xử lý yêu cầu của bạn',
            ], 500);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Password $password)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Password $password)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Password $password)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Password $password)
    {
        //
    }
}
