<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lecturer extends Model
{
    use HasFactory,softDeletes;
    public $incrementing = false;
    protected $primaryKey = 'idcode';
    protected $keyType = 'string';

    protected $fillable = [
        'idcode',
        'name',
        'profile',
        'email',
        'status',
    ];
}
