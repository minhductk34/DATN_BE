<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'time_start',
        'time_end',
        'status'
    ];
    public function candidate()
    {
        return $this->hasMany(Candidate::class,'exam_id', 'id');
    }
    public function exam_subjects()
    {
        return $this->hasMany(Exam_subject::class, 'exam_id', 'id');
    }
    public function exam_rooms()
    {
        return $this->hasMany(Exam_room::class, 'exam_id', 'id');
    }
}
