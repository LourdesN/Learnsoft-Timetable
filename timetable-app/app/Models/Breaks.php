<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breaks extends Model
{
    protected $fillable = [
        'name', 'duration_minutes'
    ];

    // Example relationship if needed
    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    // Additional methods or logic can be added here
}
