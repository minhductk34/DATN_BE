<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_subject_id',
        'Idcode',
        'Point',
        'Number_of_correct_sentences',
        'TimeStart',
        'TimeEnd',
    ];
}
