<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimetableSeeder extends Seeder
{
    public function run()
    {
        DB::table('timetables')->insert([
            ['day' => 'Monday', 'time' => '09:00 - 10:00', 'subject' => 'Math', 'teacher' => 'Mr. Smith', 'class' => 'Grade 4'],
            ['day' => 'Monday', 'time' => '10:00 - 11:00', 'subject' => 'English', 'teacher' => 'Ms. Johnson', 'class' => 'Grade 5'],
            ['day' => 'Tuesday', 'time' => '09:00 - 10:00', 'subject' => 'Science', 'teacher' => 'Mr. Brown', 'class' => 'Grade 5'],
            ['day' => 'Tuesday', 'time' => '10:00 - 11:00', 'subject' => 'History', 'teacher' => 'Mrs. Davis', 'class' => 'Grade 4'],
        ]);
    }
}