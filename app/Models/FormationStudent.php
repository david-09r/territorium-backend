<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormationStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'formation_id',
        'student_id'
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
