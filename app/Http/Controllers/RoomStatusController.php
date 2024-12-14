<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Exam;
use App\Models\Exam_room;
use App\Models\Exam_room_detail;
use Illuminate\Support\Facades\Storage;
use App\Models\Point;

class RoomStatusController extends Controller
{
    public function index()
    {
        $latestExamId = Exam::orderBy('created_at', 'desc')->value('id');

        if (!$latestExamId) {
            return response()->json(['message' => 'No exams found'], 404);
        }

        $details = Exam_room_detail::with(['exam_subject', 'exam_room.candidates'])
            ->whereHas('exam_room', function ($query) use ($latestExamId) {
                $query->where('exam_id', $latestExamId);
            })
            ->get()
            ->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'room' => $detail->exam_room->name,
                    'roomId' => $detail->exam_room->id,
                    'subject' => $detail->exam_subject?->name,
                    'subjectId' => $detail->exam_subject?->id,
                    'totalStudent' => $detail->exam_room->candidates->count(),
                ];
            });

        return response()->json($details);
    }

    // Chi tiết sinh viên trong phòng thi
    public function getStudents($roomId, $subjectId)
    {
        $students = Candidate::where('exam_room_id', $roomId)
            ->select('idcode', 'name', 'image', 'status')
            ->whereStatus(1)
            ->get()
            ->map(function ($student) use ($subjectId) {
                $hasPoint = Point::where('idcode', $student->idcode)
                    ->where('exam_subject_id', $subjectId)
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
