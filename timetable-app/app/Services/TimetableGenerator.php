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
        ini_set('max_execution_time', 300); // Increase max execution time

        $teachers = Teacher::with('learningAreas')->get();
        $learningAreas = LearningArea::all();
        $grades = Grade::all();
        $timeslots = Timeslot::all();
        $breaks = Breaks::all();

        $timetableEntries = [];

        foreach ($grades as $grade) {
            foreach (self::DAYS_OF_WEEK as $day) {
                $lessonCount = 0;
                $breakCount = 0;

                foreach ($timeslots as $timeslot) {
                    if ($this->isBreakTime($timeslot, $breaks)) {
                        if ($breakCount < 3) {
                            $breakCount++;
                            $this->createBreakTimetableEntry($timetableEntries, $grade, $timeslot, $day);
                        }
                    } else {
                        if ($lessonCount >= 9) {
                            break; 
                        }

                        foreach ($learningAreas as $learningArea) {
                            if ($this->hasReachedDailyLessonLimit($grade, $learningArea, $day, $timetableEntries) || 
                                $this->hasReachedWeeklyLessonLimit($grade, $learningArea, $timetableEntries)) {
                                continue;
                            }

                            $teacher = $this->findAvailableTeacher($teachers, $learningArea, $grade, $timeslot, $timetableEntries);

                            if ($teacher) {
                                $timetableEntry = [
                                    'grade_id' => $grade->id,
                                    'learning_area_id' => $learningArea->id,
                                    'teacher_id' => $teacher->id,
                                    'timeslot_id' => $timeslot->id,
                                    'day' => $day,
                                ];

                                $timetableEntries[] = $timetableEntry;

                                $this->markScheduleAsOccupied($teacher, $timeslot, $timetableEntries);
                                $lessonCount++;
                                break; 
                            }
                        }
                    }
                }
            }
        }

        NewTimetable::insert($timetableEntries);

        return true;
    }

    private function isBreakTime($timeslot, $breaks)
    {
        foreach ($breaks as $break) {
            if ($timeslot->start_time >= $break->start_time && $timeslot->end_time <= $break->end_time) {
                return true;
            }
        }
        return false;
    }

    private function findAvailableTeacher($teachers, $learningArea, $grade, $timeslot, $timetableEntries)
    {
        foreach ($teachers as $teacher) {
            if ($this->isTeacherAvailable($teacher, $timeslot, $timetableEntries) &&
                $this->canTeachLearningArea($teacher, $learningArea)) {
                return $teacher;
            }
        }
        return null;
    }

    private function isTeacherAvailable($teacher, $timeslot, $timetableEntries)
    {
        foreach ($timetableEntries as $entry) {
            if ($entry['teacher_id'] == $teacher->id && $entry['timeslot_id'] == $timeslot->id) {
                return false;
            }
        }
        return true;
    }

    private function canTeachLearningArea($teacher, $learningArea)
    {
        return DB::table('teachers_learning_areas')
                ->where('teacher_id', $teacher->id)
                ->where('learning_area_id', $learningArea->id)
                ->exists();
    }

    private function createBreakTimetableEntry(&$timetableEntries, $grade, $timeslot, $day)
    {
        $timetableEntry = [
            'grade_id' => $grade->id,
            'learning_area_id' => null, 
            'teacher_id' => null, 
            'timeslot_id' => $timeslot->id,
            'day' => $day,
            'is_break' => true, 
        ];

        $timetableEntries[] = $timetableEntry;
    }

    private function markScheduleAsOccupied($teacher, $timeslot, &$timetableEntries)
    {
        // Logic to mark the timeslot as occupied for the teacher
    }

    private function hasReachedDailyLessonLimit($grade, $learningArea, $day, $timetableEntries)
    {
        $dailyLessonCount = 0;
        foreach ($timetableEntries as $entry) {
            if ($entry['grade_id'] == $grade->id &&
                $entry['learning_area_id'] == $learningArea->id &&
                $entry['day'] == $day) {
                $dailyLessonCount++;
            }
        }
        return $dailyLessonCount >= 2;
    }

    private function hasReachedWeeklyLessonLimit($grade, $learningArea, $timetableEntries)
    {
        $weeklyLessonCount = 0;
        foreach ($timetableEntries as $entry) {
            if ($entry['grade_id'] == $grade->id &&
                $entry['learning_area_id'] == $learningArea->id) {
                $weeklyLessonCount++;
            }
        }
        return $weeklyLessonCount >= $learningArea->number_of_lessons;
    }
}





