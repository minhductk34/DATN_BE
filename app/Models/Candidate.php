<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Candidate extends Authenticatable
{
    use HasFactory, SoftDeletes;
    public $incrementing = false;
    protected $primaryKey = 'idcode';
    protected $keyType = 'string';

    protected $fillable = [
        'idcode',
        'exam_room_id',
        'exam_id',
        'name',
        'image',
        'dob',
        'address',
        'email',
        'status'
    ];
    public function exam(){
        return $this->belongsTo(Exam::class);
    }
    public function exam_room(){
        return $this->belongsTo(Exam_room::class);
    }
    public function password()
    {
        return $this->belongsTo(Password::class);
    }
    public function point()
    {
        return $this->hasMany(Point::class);
    }
    public function english_exam_question()
    {
        return $this->hasMany(English_exam_question::class);
    }
    public function candidate_questions()
    {
        return $this->hasMany(Candidate_question::class);
    }
    public function history()
    {
        return $this->hasMany(History::class);
    }
}
