<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSubject extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'id',
        'exam_id',
        'Name',
        'Status',
        'TimeStart',
        'TimeEnd'
    ];

    public function exam(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function contents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExamContent::class);
    }
}