<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'Title',
        'Image_Title',
        'Answer_P',
        'Image_P',
        'Answer_F1',
        'Image_F1',
        'Answer_F2',
        'Image_F2',
        'Answer_F3',
        'Image_F3',
        'Level',
        'version',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
