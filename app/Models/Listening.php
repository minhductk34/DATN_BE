<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listening extends Model
{
    use HasFactory ,SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'exam_content_id',
        'audio',
        'status',
        'level',
        'name'
    ];

    public function exam_content(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam_content::class);
    }

    public function listening_question()
    {
        return $this->hasMany(Listening_question::class);
    }


}
