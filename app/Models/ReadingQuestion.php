<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadingQuestion extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'reading_id',
        'Status',
        'current_version_id'
    ];

    public function reading(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Reading::class);
    }

    public function versions()
    {
        return $this->hasMany(ReadingQuestionVersion::class,'question_id');
    }

    public function currentVersion()
    {
        return $this->belongsTo(ReadingQuestionVersion::class, 'current_version_id');
    }
}
