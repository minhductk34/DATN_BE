<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam_room extends Model
{
    use HasFactory,softDeletes;
    protected $fillable = [
        'exam_id',
        'name',
    ];
    public function exam(){
        return $this->belongsTo(Exam::class);
    }
    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function detail()
    {
        return $this->hasOne(Exam_room_detail::class,'exam_room_id','id');
    }
    
    public function examRoomDetail() {
        return $this->hasMany(Exam_room_detail::class, 'exam_room_id');
    }
}
