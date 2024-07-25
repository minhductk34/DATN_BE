<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam_subject extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'id',
        'exam_id',
        'Name',
        'Status',
        'TimeStart',
        'TimeEnd'
    ];

    public function exam(){
        return $this->belongsTo(Exam::class);
    }

    public function contents(){
        return $this->hasMany(Exam_content::class);
    }

    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
    ];

    public $incrementing = false;

    protected $dates = ['updated_at','deleted_at'];
}
