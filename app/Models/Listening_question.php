<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listening_question extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'listening_id',
        'status',
        'current_version_id'
    ];

    public function listening()
    {
        return $this->belongsTo(Listening::class);
    }

    public function versions()
    {
        return $this->hasMany(Listening_question_version::class,'question_id');
    }

    public function currentVersion()
    {
        return $this->belongsTo(Listening_question_version::class, 'current_version_id');
    }
    public function english_exam_question()
    {
        return $this->belongsTo(English_exam_question::class);
    }
}
