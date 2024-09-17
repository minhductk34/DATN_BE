<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicStructure extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'exam_content_id',
        'Level',
        'Quality',
    ];
    public function exam_content(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExamContent::class);
    }
}
