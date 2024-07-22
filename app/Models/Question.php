<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_content_id',
        'Title',
        'Image_Title',
        'Answer_P',
        'Image_P',
        'Answer_F1',
        'Image_F1',
        'Answer_F2',
        'Image_F2',
        'Answer_F3',
        'Image_F3',
        'Level',
        'Status',
    ];
    public function exam_content(){
        return $this->belongsTo(Exam_content::class);
    }
}
