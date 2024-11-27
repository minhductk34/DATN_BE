<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'current_version_id',
        'exam_content_id',
        'status',
    ];

    public function exam_content(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam_content::class);
    }

    public function version()
    {
        return $this->belongsTo(Question_version::class);
    }
    public function currentVersion()
    {
        return $this->hasMany(Question_version::class, 'question_id');
    }
}
