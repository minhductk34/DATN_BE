<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listening_questions extends Model
{
    use HasFactory;
    protected $fillable = [
        'listening_id',
        'Title',
        'Answer_P',
        'Answer_F1',
        'Answer_F2',
        'Answer_F3',
        'Status',
        'Level',
    ];
}
