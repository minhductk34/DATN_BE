<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poetry extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_subject_id',
        'Name',
        'TimeStart',
        'TimeEnd',
        'Status',

    ];
}
