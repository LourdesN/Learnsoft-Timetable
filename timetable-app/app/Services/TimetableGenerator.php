<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\LearningArea;
use App\Models\Grade;
use App\Models\NewTimetable; // Make sure to import the NewTimetable model
use App\Models\Timeslot; // Assuming Timeslot is a model that represents time slots
use App\Models\Breaks; // Assuming Breaks is a model that represents break times
use Illuminate\Support\Facades\DB;

class TimetableGenerator
{
    const DAYS_OF_WEEK = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

    public function generate()
    {

        $teachers = Teacher::all();
        $learningAreas = LearningArea::all();
        $grades = Grade::all();
        $timeslots = Timeslot::all(); // Get all available timeslots
        $breaks = Breaks::all(); // Get all breaks

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
                            break; // Stop if 9 lessons are already scheduled
                        }

                        foreach ($learningAreas as $learningArea) {
                            if ($this->hasReachedWeeklyLessonLimit($grade, $learningArea)) {
                                continue; // Skip if weekly lesson limit for this learning area is reached
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
                                break; // Move to the next timeslot after scheduling a lesson
                            }
                        }
                    }
                }
            }
        }

        return true; // Return true indicating the timetable generation is complete
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
        // Find an available teacher who can teach the given learning area, and is not teaching another class at the same time
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
        // Check if the teacher is available at the given timeslot
        return !NewTimetable::where('teacher_id', $teacher->id)
                            ->where('timeslot_id', $timeslot->id)
                            ->exists();
    }

    private function canTeachLearningArea($teacher, $learningArea)
    {
        // Check if the teacher is qualified to teach the given learning area
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
            'learning_area_id' => null, // No learning area for break
            'teacher_id' => null, // No teacher for break
            'timeslot_id' => $timeslot->id,
            'day' => $day,
            'is_break' => true, // Indicate this entry is a break
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




