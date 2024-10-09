<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamRoom extends Model
{
    use HasFactory;
    protected $fillable = [
        'Name',
        'exam_id'
    ];
    public function exam(){
        return $this->belongsTo(Exam::class);
    }
    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'exam_room_id');
    }
}
