<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    public $table = 'timetables';

    public $fillable = [
        'day',
        'time',
        'subject',
        'teacher',
        'class',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'day' => 'string',
        'time' => 'string',
        'subject' => 'string',
        'teacher' => 'string',
        'class' => 'string'
    ];

    public static array $rules = [
        'day' => 'required',
        'time' => 'required',
        'subject' => 'required',
        'teacher' => 'required',
        'class' => 'required',
        'created_at' => 'required',
        'updated_at' => 'required'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function time()
    {
        return $this->belongsTo(Time::class);
    }
}
