<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question_version extends Model
{
    use HasFactory,softDeletes;
    protected $fillable = [
        'question_id',
        'title',
        'image_title',
        'answer_P',
        'image_P',
        'answer_F1',
        'image_F1',
        'answer_F2',
        'image_F2',
        'answer_F3',
        'image_F3',
        'level',
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
