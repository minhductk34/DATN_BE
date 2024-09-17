<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidatesTakeTheEnglishTest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_id',
        'reading_id',
        'listening_id',
        'Idcode',
        'Numerical_order',
        'Answer_P',
        'Answer_Pi',
        'Answer_Temp',
    ];

    public function question(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function reading(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Readings::class);
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
