<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam_content extends Model
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
    public function exam_subject(){
        return $this->belongsTo(Exam_subject::class);
    }
}
