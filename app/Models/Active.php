<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Active extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'exam_subject_id',
        'idcode',
        'active',
        'reason',
        'admin_id',
    ];
    public function exam_subject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam_subject::class);
    }
    public function candidate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
