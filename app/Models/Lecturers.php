<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturers extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $primaryKey = 'Idcode';
    protected $keyType = 'string';

    protected $fillable = [
        'Idcode',
        'Fullname',
        'Profile',
        'Email',
        'Status',
    ];
}
