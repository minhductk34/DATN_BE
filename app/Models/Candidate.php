<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'Idcode';
    protected $keyType = 'string';

    protected $fillable = [
        'exam_id',
        'Fullname',
        'Image',
        'DOB',
        'Address',
        'Examination_room',
        'Password',
        'Email',
        'Status'
    ];
    public function exam(){
        return $this->belongsTo(Exam::class);
    }
}
