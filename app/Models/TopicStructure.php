<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicStructure extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'exam_content_id',
        'exam_subject_id',
        'Level',
        'Quality',
    ];
    public function exam_content(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExamContent::class);
    }
    public function exam_subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExamSubject::class);
    }
}
