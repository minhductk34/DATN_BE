<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam_subject extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
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
}
