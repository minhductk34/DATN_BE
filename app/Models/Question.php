<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'current_version_id',
        'exam_content_id',
        'Status',
    ];

    public function exam_content(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExamContent::class);
    }

    public function versions()
    {
        return $this->hasMany(QuestionVersion::class);
    }

    public function currentVersion()
    {
        return $this->belongsTo(QuestionVersion::class, 'current_version_id');
    }
}
