<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listening extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_content_id',
        'url_listenning',
        'Status',
        'Level',
    ];
    public function exam_content(){
        return $this->belongsTo(Exam_content::class);
    }
}
