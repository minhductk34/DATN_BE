<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamContent extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    use HasFactory;
    protected $fillable = [
        'id',
        'exam_subject_id',
        'title',
        'Status',
    ];
    public function exam_subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExamSubject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function readings()
    {
        return $this->hasMany(Reading::class);
    }

    public function listenings()
    {
        return $this->hasMany(Listening::class);
    }
}
