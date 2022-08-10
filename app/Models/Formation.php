<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $fillable = [
        'name',
        'identification',
        'status'
    ];

    use HasFactory;

    public function formationTeachers()
    {
        return $this->hasMany(FormationTeacher::class);
    }

    public function formationStudents()
    {
        return $this->hasMany(FormationStudent::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
