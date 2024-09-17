<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListeningQuestion extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'listening_id',
        'Status',
        'current_version_id'
    ];

    public function listening()
    {
        return $this->belongsTo(Listening::class);
    }

    public function versions()
    {
        return $this->hasMany(ListeningQuestionVersion::class,'question_id');
    }

    public function currentVersion()
    {
        return $this->belongsTo(ListeningQuestionVersion::class, 'current_version_id');
    }
}
