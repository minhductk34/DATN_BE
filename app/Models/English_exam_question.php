<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class English_exam_question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_id',
        'reading_id',
        'listening_id',
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

    public function reading(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Reading::class);
    }

    public function listening(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Listening::class);
    }

    public function candidate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
