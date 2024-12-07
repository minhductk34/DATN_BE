<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Exam_room;
use Illuminate\Support\Facades\Storage;
use App\Models\Point;

class RoomStatusController extends Controller
{
    public function index()
    {
        $currentDateTime = now();

        $rooms = Exam_room::with(['detail.exam_subject', 'candidates'])
            ->select('id', 'name', 'exam_id')
            ->whereHas('detail', function ($query) use ($currentDateTime) {
                $query->where('exam_date', '<=', $currentDateTime)
                    ->where('exam_end', '>', $currentDateTime);
            })
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room' => $room->name,
                    'subject' => $room->detail?->exam_subject?->name,
                    'totalStudent' => $room->candidates->count(),
                ];
            });

        return response()->json($rooms);
    }

    // Chi tiết sinh viên trong phòng thi
    public function getStudents($roomId)
    {
        $examRoom = Exam_room::with('detail.exam_subject')->find($roomId);

        $students = Candidate::where('exam_room_id', $roomId)
            ->select('idcode', 'name', 'image', 'status')
            ->whereStatus(1)
            ->get()
            ->map(function ($student) use ($examRoom) {
                $hasPoint = Point::where('idcode', $student->idcode)
                    ->where('exam_subject_id', $examRoom->detail->exam_subject->id)
                    ->exists();

                return [
                    'id' => $student->idcode,
                    'studentName' => $student->name,
                    'image' => Storage::url($student->image),
                    'studentStatus' => $hasPoint ? 2 : 0,
                ];
            });

        return response()->json($students);
    }
}
