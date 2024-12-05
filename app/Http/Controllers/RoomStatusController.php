<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Exam_room;
use Illuminate\Support\Facades\Storage;

class RoomStatusController extends Controller
{
    // Lấy danh sách các phòng thi
    public function index()
    {
        $rooms = Exam_room::with(['detail.exam_subject'])
            ->select('id', 'name', 'exam_id')
            ->limit(5)
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room' => $room->name,
                    'subject' => $room->detail?->exam_subject?->name,
                    'totalStudent' => $room->candidates()->count(),
                    'notStarted' => $room->candidates()->where('status', 0)->count(),
                    'inProgress' => $room->candidates()->where('status', 1)->count(),
                    'completed' => $room->candidates()->where('status', 2)->count(),
                    'forbidden' => $room->candidates()->where('status', 3)->count()
                ];
            });

        return response()->json($rooms);
    }

    // Chi tiết sinh viên trong phòng thi
    public function getStudents($roomId)
    {
        $students = Candidate::where('exam_room_id', $roomId)
            ->select('idcode', 'name', 'image', 'status')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->idcode,
                    'studentName' => $student->name,
                    'image' => Storage::url($student->image),
                    'studentStatus' => $student->status,
                ];
            });

        return response()->json($students);
    }
}
