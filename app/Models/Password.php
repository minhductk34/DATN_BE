<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Password extends Model
{
    use HasFactory;
    protected $fillable = [
        'Idcode',
        'Password',
    ];
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
