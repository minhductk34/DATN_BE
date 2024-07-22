<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic_structure extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_content_id',
        'Level',
        'Quality',
    ];
    public function exam_content(){
        return $this->belongsTo(Exam_content::class);
    }
}
