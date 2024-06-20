<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = ['first_name', 'surname', 'title', 'phone_number', 'tsc_number','email']; 

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function timetables()
    {
        return $this->hasMany(NewTimetable::class);
    }
}
