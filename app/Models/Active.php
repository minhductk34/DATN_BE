<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Active extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_subject_id',
        'Idcode',
        'Active'
    ];
    public function exam_subject(){
        return $this->belongsTo(Exam_subject::class);
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
