<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poetry extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'exam_subject_id',
        'Name',
        'TimeStart',
        'TimeEnd',
        'Status',
    ];
    public function exam_subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExamSubject::class);
    }
    public function examRooms()
    {
        return $this->hasMany(ExamRoom::class);
    }

}
