<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningArea extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'number_of_lessons', 'double_lessons'];

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function timetables()
    {
        return $this->hasMany(NewTimetable::class);
    }
}

