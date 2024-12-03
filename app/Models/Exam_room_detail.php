<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam_room_detail extends Model
{
    use HasFactory,softDeletes;
    protected $fillable = [
        'exam_room_id',
        'exam_subject_id',
        'exam_session_id',
        'exam_date',
        'exam_end',
        'create_by'
    ];
    public function exam_room(){
        return $this->belongsTo(Exam_room::class);
    }
    public function exam_subject(){
        return $this->belongsTo(Exam_subject::class,'exam_subject_id','id');
    }
    public function exam_session(){
        return $this->belongsTo(Exam_session::class);
    }
}
