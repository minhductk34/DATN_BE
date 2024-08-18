<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Readings extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    use HasFactory;
    protected $fillable = [
        'id',
        'exam_content_id',
        'Title',
        'Status',
        'Level'
    ];
    public function exam_content(){
        return $this->belongsTo(Exam_content::class);
    }
}
