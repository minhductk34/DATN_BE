<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reading_question extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'reading_id',
        'status',
        'current_version_id'
    ];

    public function reading(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Reading::class);
    }

    public function versions()
    {
        return $this->hasMany(Reading_question_version::class,'question_id');
    }

    public function currentVersion()
    {
        return $this->belongsTo(Reading_question_version::class, 'current_version_id');
    }
    public function english_exam_question()
    {
        return $this->belongsTo(English_exam_question::class);
    }
}
