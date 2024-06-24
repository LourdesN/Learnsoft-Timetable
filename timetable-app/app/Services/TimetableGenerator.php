<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\LearningArea;
use App\Models\Grade;
use App\Models\NewTimetable;
use App\Models\Timeslot; 
use App\Models\Breaks; 
use Illuminate\Support\Facades\DB;

class TimetableGenerator
{
    const DAYS_OF_WEEK = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

    public function generate()
    {

        $teachers = Teacher::all();
        $learningAreas = LearningArea::all();
        $grades = Grade::all();
        $timeslots = Timeslot::all(); 
        $breaks = Breaks::all();

        foreach ($grades as $grade) {
            foreach (self::DAYS_OF_WEEK as $day) {
                // Ensure there are 9 lessons and 3 breaks each day
                $lessonCount = 0;
                $breakCount = 0;

                foreach ($timeslots as $timeslot) {
                    if ($this->isBreakTime($timeslot, $breaks)) {
                        if ($breakCount < 3) {
                            $breakCount++;
                            $this->createBreakTimetableEntry($grade, $timeslot, $day);
                        }
                    } else {
                        if ($lessonCount >= 9) {
                            break; 
                        }

                        foreach ($learningAreas as $learningArea) {
                            if ($this->hasReachedWeeklyLessonLimit($grade, $learningArea)) {
                                continue;
                            }

                            $teacher = $this->findAvailableTeacher($teachers, $learningArea, $grade, $timeslot);

                            if ($teacher) {
                                $timetableEntry = [
                                    'grade_id' => $grade->id,
                                    'learning_area_id' => $learningArea->id,
                                    'teacher_id' => $teacher->id,
                                    'timeslot_id' => $timeslot->id,
                                    'day' => $day,
                                ];

                                // Save timetable entry using NewTimetable model
                                NewTimetable::create($timetableEntry);

                                // Mark this schedule and timeslot as occupied for the teacher
                                $this->markScheduleAsOccupied($teacher, $timeslot);
                                $lessonCount++;
                                break; 
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    private function isBreakTime($timeslot, $breaks)
    {
        // Check if the given timeslot falls within break times
        foreach ($breaks as $break) {
            if ($timeslot->start_time >= $break->start_time && $timeslot->end_time <= $break->end_time) {
                return true;
            }
        }
        return false;
    }

    private function findAvailableTeacher($teachers, $learningArea, $grade, $timeslot)
    {
        foreach ($teachers as $teacher) {
            if ($this->isTeacherAvailable($teacher, $timeslot) &&
                $this->canTeachLearningArea($teacher, $learningArea)){
                return $teacher;
            }
        }
        return null;
    }

    private function isTeacherAvailable($teacher, $timeslot)
    {
        return !NewTimetable::where('teacher_id', $teacher->id)
                            ->where('timeslot_id', $timeslot->id)
                            ->exists();
    }

    private function canTeachLearningArea($teacher, $learningArea)
    {
        return DB::table('teachers_learning_areas')
                ->where('teacher_id', $teacher->id)
                ->where('learning_area_id', $learningArea->id)
                ->exists();
    }

    private function createBreakTimetableEntry($grade, $timeslot, $day)
    {
        // Create a timetable entry for break
        $timetableEntry = [
            'grade_id' => $grade->id,
            'learning_area_id' => null, 
            'teacher_id' => null, 
            'timeslot_id' => $timeslot->id,
            'day' => $day,
            'is_break' => true, 
        ];

        // Save timetable entry using NewTimetable model
        NewTimetable::create($timetableEntry);
    }

    private function markScheduleAsOccupied($teacher, $timeslot)
    {
        // Logic to mark the timeslot as occupied for the teacher
        // You can add your logic here, e.g., adding a record to a pivot table to track teacher's schedules
    }

    private function hasReachedWeeklyLessonLimit($grade, $learningArea)
    {
        $weeklyLessonCount = NewTimetable::where('grade_id', $grade->id)
                                         ->where('learning_area_id', $learningArea->id)
                                         ->count();
        return $weeklyLessonCount >= $learningArea->number_of_lessons;
    }
}




