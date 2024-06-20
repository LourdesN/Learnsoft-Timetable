<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewTimetable extends Model
{
    protected $fillable = ['grade_id', 'learning_area_id', 'teacher_id', 'schedule_id', 'day'];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function learningArea()
    {
        return $this->belongsTo(LearningArea::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}
