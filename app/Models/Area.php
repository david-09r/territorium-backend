<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
