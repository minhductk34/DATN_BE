<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listening_question_version extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = [
        'listening_question_id',
        'version',
        'title',
        'answer_P',
        'answer_F1',
        'answer_F2',
        'answer_F3',
        'status',
        'level',
    ];

    public function listening_question()
    {
        return $this->belongsTo(Listening_question::class);
    }
}
