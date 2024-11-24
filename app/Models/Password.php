<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Password extends Model
{
    use HasFactory,softDeletes;
    protected $fillable = [
        'idcode',
        'password',
    ];
    public function candidate()
    {
        return $this->hasOne(Candidate::class, 'idcode', 'idcode'); 
    }
}
