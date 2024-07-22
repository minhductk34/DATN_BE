<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate_question extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_id',
        'Idcode',
        'Numerical_order',
        'Answer_P',
        'Answer_Pi',
        'Answer_Temp',
    ];
    public function question(){
        return $this->belongsTo(Question::class);
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
