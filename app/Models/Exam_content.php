<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam_content extends Model
{
    use HasFactory,softDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'exam_subject_id',
        'title',
        'status',
        'url_listening',
        'description',
    ];
    public function exam_subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam_subject::class);
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }
    public function reading()
    {
        return $this->hasMany(Reading::class);
    }
    public function listening()
    {
        return $this->hasMany(Listening::class);
    }

    public function exam_structure()
    {
        return $this->belongsTo(Exam_structure::class);
    }
}
