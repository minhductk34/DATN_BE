<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamRoomDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_room_id',
        'exam_subject_id',
        'exam_session_id',
        'exam_date',
    ];
    public function exam_room(){
        return $this->belongsTo(ExamRoom::class);
    }
    public function exam_subject(){
        return $this->belongsTo(ExamSubject::class);
    }
    public function exam_session(){
        return $this->belongsToMany(ExamSession::class);
    }
}
