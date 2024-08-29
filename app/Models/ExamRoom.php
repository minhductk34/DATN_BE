<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamRoom extends Model
{
    use HasFactory;
    protected $fillable = [
        'poetry_id',
        'Name',
        'Quantity',
        'Status',
    ];
    public function poetry()
    {
        return $this->belongsTo(Poetry::class, 'poetry_id');
    }
}
