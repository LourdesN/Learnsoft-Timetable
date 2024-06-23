<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['day', 'start_time', 'end_time'];
    public const DAYS_OF_WEEK = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }


    public function learningArea()
    {
        return $this->belongsTo(LearningArea::class);
    }

    public function timetables()
    {
        return $this->hasMany(NewTimetable::class);
    }
}
