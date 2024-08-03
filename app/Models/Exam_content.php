<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam_content extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_subject_id',
        'title',
        'Status',
    ];
    public function exam_subject()
    {
        return $this->belongsTo(Exam_subject::class);
    }
}
