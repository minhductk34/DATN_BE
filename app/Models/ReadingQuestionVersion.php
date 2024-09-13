<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingQuestionVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'question_id',
        'version',
        'Title',
        'Answer_P',
        'Answer_F1',
        'Answer_F2',
        'Answer_F3',
        'Status',
        'Level',
    ];

    public function question()
    {
        return $this->belongsTo(ReadingQuestion::class);
    }
}
