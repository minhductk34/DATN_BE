<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam_session extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'time_start',
        'time_end',
    ];
    public function exam_room_detail()
    {
        return $this->belongsTo(Exam_room_detail::class);
    }
}
