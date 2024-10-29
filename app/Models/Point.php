<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Point extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'exam_subject_id',
        'idcode',
        'point',
        'number_of_correct_sentences',
        'time_start',
        'time_end',
    ];
    public function exam_subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam_subject::class);
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
