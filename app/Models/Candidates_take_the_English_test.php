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
    public function question(){
        return $this->belongsTo(Question::class);
    }
    public function reading(){
        return $this->belongsTo(Readings::class);
    }
    public function listening(){
        return $this->belongsTo(Listening::class);
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
