<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormationTeacher extends Model
{
    protected $fillable = [
      'formation_id',
      'teacher_id'
    ];

    use HasFactory;

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
