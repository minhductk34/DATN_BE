<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam_subject extends Model
{
    use HasFactory;
    use HasFactory,SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'exam_id',
        'name',
        'create_by',
        'status',
    ];

    public function exam(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function exam_content()
    {
        return $this->hasOne(Exam_content::class, 'exam_subject_id');
    }
    public function exam_room_detail()
    {
        return $this->belongsTo(Exam_room_detail::class);
    }
    public function exam_subject_detail()
    {
        return $this->belongsTo(Exam_subject_detail::class);
    }
    public function point()
    {
        return $this->belongsTo(Point::class);
    }
    public function exam_structure()
    {
        return $this->belongsTo(Exam_structure::class);
    }
    public function history()
    {
        return $this->belongsTo(History::class);
    }
    public function active()
    {
        return $this->belongsTo(Active::class);
    }

}
