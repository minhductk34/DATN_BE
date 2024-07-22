<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_subject_id',
        'Idcode',
        'Answer',
        'Time'
    ];
    public function exam_subject(){
        return $this->belongsTo(Exam_subject::class);
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
