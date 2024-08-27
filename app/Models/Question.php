<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'id',
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
