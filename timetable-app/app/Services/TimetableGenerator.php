<?php

namespace App\Services;

use App\Models\Breaks;
use App\Models\Teacher;
use App\Models\LearningArea;
use App\Models\Schedule;
use App\Models\Grade;
use App\Models\NewTimetable;
use Illuminate\Support\Facades\DB;

class TimetableGenerator
{
    public function generate()
    {
        $teachers = Teacher::all();
        $schedules = Schedule::all();
        $learningAreas = LearningArea::all();
        $grades = Grade::all();
        $breaks = Breaks::all(); // Get all break times

        $timetable = [];

        foreach ($grades as $grade) {
            foreach ($learningAreas as $learningArea) {
                if ($this->isGradeLearningArea($grade->id, $learningArea->id)) {
                    foreach ($schedules as $schedule) {
                        if ($this->isBreakTime($schedule, $breaks)) {
                            continue; // Skip breaks
                        }

                        $teacher = $this->findAvailableTeacher($teachers, $learningArea, $schedule);

                        if ($teacher) {
                            $timetableEntry = [
                                'grade_id' => $grade->id,
                                'learning_area_id' => $learningArea->id,
                                'teacher_id' => $teacher->id,
                                'schedule_id' => $schedule->id,
                                'day' => $schedule->day,
                            ];

                            // Save timetable entry using NewTimetable model
                            NewTimetable::create($timetableEntry);

                            // Optionally, you can add the saved entry to the $timetable array
                            $timetable[] = $timetableEntry;

                            // Mark this schedule as occupied for the teacher
                            $this->markScheduleAsOccupied($teacher, $schedule);
                        }
                    }
                }
            }
        }

        return $timetable;
    }

    private function isGradeLearningArea($gradeId, $learningAreaId)
    {
        // Check if the learning area is associated with the grade
        return DB::table('grade_learning_areas')
                 ->where('grade_id', $gradeId)
                 ->where('learning_area_id', $learningAreaId)
                 ->exists();
    }

    private function isBreakTime($schedule, $breaks)
    {
        // Check if the given schedule falls within break times
        foreach ($breaks as $break) {
            if ($schedule->start_time >= $break->start_time && $schedule->end_time <= $break->end_time) {
                return true;
            }
        }
        return false;
    }

    private function findAvailableTeacher($teachers, $learningArea, $schedule)
    {
        // Logic to find an available teacher who can teach the given learning area
        foreach ($teachers as $teacher) {
            if ($this->isTeacherAvailable($teacher, $schedule) && $this->canTeachLearningArea($teacher, $learningArea)) {
                return $teacher;
            }
        }
        return null;
    }

    private function isTeacherAvailable($teacher, $schedule)
    {
        // Check if the teacher is available at the given schedule
        // Here you can check if the teacher is already assigned to another schedule at the same time
        return !NewTimetable::where('teacher_id', $teacher->id)
                            ->where('schedule_id', $schedule->id)
                            ->exists();
    }

    private function canTeachLearningArea($teacher, $learningArea)
    {
        // Check if the teacher is assigned to teach the given learning area
        return DB::table('teachers_learning_areas')
                 ->where('teacher_id', $teacher->id)
                 ->where('learning_area_id', $learningArea->id)
                 ->exists();
    }

    private function markScheduleAsOccupied($teacher, $schedule)
    {
        // Logic to mark the schedule as occupied for the teacher
        // You can add your logic here, e.g., adding a record to a pivot table to track teacher's schedules
    }
}


