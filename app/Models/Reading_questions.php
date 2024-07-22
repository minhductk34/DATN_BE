<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading_questions extends Model
{
    use HasFactory;
    protected $fillable = [
        'reading_id',
        'Title',
        'Answer_P',
        'Answer_F1',
        'Answer_F2',
        'Answer_F3',
        'Status',
        'Level',
    ];
    public function reading(){
        return $this->belongsTo(Readings::class);
    }
}
