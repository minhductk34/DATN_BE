<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam_structure extends Model
{
    use HasFactory,softDeletes;
    protected $fillable = [
        'exam_subject_id',
        'exam_content_id',
        'level',
        'quantity',
    ];
    public function exam_subject(){
        return $this->belongsTo(Exam_subject::class);
    }
    public function exam_content(){
        return $this->belongsTo(Exam_content::class);
    }
}
