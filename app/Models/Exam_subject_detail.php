<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam_subject_detail extends Model
{
    use HasFactory,softDeletes;
    protected $fillable = [
        'exam_subject_id',
        'quantity',
        'time'
    ];
    public function exam_subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam_subject::class);
    }
}
