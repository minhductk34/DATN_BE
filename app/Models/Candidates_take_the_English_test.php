<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidates_take_the_English_test extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_id',
        'reading_id',
        'listening_id',
        'Idcode',
        'Numerical_order',
        'Answer_P',
        'Answer_Pi',
        'Answer_Temp',
    ];
}
