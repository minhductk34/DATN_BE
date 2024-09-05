<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListeningQuestions extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'listening_id',
        'Title',
        'Answer_P',
        'Answer_F1',
        'Answer_F2',
        'Answer_F3',
        'Status',
        'Level',
    ];

    public function listening()
    {
        return $this->belongsTo(Listening::class);
    }
}
