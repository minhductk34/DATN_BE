<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class English_exam_question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_id',
        'reading_question_id',
        'listening_question_id',
        'idcode',
        'numerical_order',
        'answer_P',
        'answer_Pi',
        'answer_Temp',
    ];

    public function question(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function reading_question(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Reading_question::class);
    }

    public function listening_question(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Listening_question::class);
    }

    public function candidate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
