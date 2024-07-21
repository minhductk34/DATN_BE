<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam_subject extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_id',
        'Name',
        'Status',
        'TimeStart',
        'TimeEnd'

    ];
}
