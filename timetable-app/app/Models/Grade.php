<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    public $table = 'grades';

    public $fillable = [
        'grade',
        'created_by'
    ];

    protected $casts = [
        'grade' => 'string'
    ];

    public static array $rules = [
        'grade' => 'required|string|max:191',
        'created_by' => 'required',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];
      
    public function timetables()
    {
        return $this->hasMany(NewTimetable::class);
    }

    public function LearningAreas()
    {
        return $this->belongsToMany(LearningArea::class, 'name', 'number_of_lessons', 'learning_areas_id');
    }
    
}
