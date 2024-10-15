<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading_question_version extends Model
{
    use HasFactory;
    protected $fillable = [
        'reading_question_id',
        'version',
        'title',
        'answer_P',
        'answer_F1',
        'answer_F2',
        'answer_F3',
        'status',
        'level',
    ];

    public function reading_question()
    {
        return $this->belongsTo(Reading_question::class);
    }
}
