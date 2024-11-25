<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate_question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_id',
        'idcode',
        'subject_id',
        'numerical_order',
        'answer_P',
        'answer_Pi',
        'answer_Temp',
    ];

    public function question(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function candidate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
