<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\LearningArea;
use App\Models\Grade;
use App\Models\NewTimetable;
use App\Models\Timeslot;
use App\Models\Breaks;
use App\Models\Stream;
use Illuminate\Support\Facades\DB;

class TimetableGenerator
{
    const DAYS_OF_WEEK = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

    public function generate()
    {
        ini_set('max_execution_time', 300);

        $teachers = Teacher::with('learningAreas')->get();
        $learningAreas = LearningArea::all();
        $grades = Grade::all();
        $timeslots = Timeslot::all();
        $breaks = Breaks::all();

        foreach ($grades as $grade) {
            $this->generateForGrade($grade, null, $teachers, $learningAreas, $timeslots, $breaks);
        }

        return true;
    }

    public function generateForGrade($grade, $stream = null, $teachers = null, $learningAreas = null, $timeslots = null, $breaks = null)
    {
        ini_set('max_execution_time', 300); 
        $teachers = $teachers ?? Teacher::with('learningAreas')->get();
        $learningAreas = $learningAreas ?? LearningArea::all();
        $timeslots = $timeslots ?? Timeslot::all();
        $breaks = $breaks ?? Breaks::all();

        $gradeLearningAreas = $this->getLearningAreasForGrade($grade);

        $gradeLearningAreas = $gradeLearningAreas->shuffle();

        $distributedLearningAreas = $this->distributeLearningAreas($gradeLearningAreas, self::DAYS_OF_WEEK);

        $timetableEntries = [];

        foreach (self::DAYS_OF_WEEK as $day) {
            $lessonCount = 0;
            $breakCount = 0;
            $dailyAssignedLearningAreas = [];

            foreach ($timeslots as $timeslot) {
                if ($this->isBreakTime($timeslot, $breaks)) {
                    if ($breakCount < 3) {
                        $breakCount++;
                        $this->createBreakTimetableEntry($timetableEntries, $grade, $stream, $timeslot, $day);
                    }
                } else {
                    if ($lessonCount >= 9) {
                        break;
                    }

                    $learningArea = $this->getLearningAreaForDayAndSlot($distributedLearningAreas, $day, $timeslot, $dailyAssignedLearningAreas);

                    if (!$learningArea) {
                        $learningArea = $gradeLearningAreas->random();
                    }

                    $teacher = $this->findAvailableTeacher($teachers, $learningArea, $grade, $timeslot, $timetableEntries);

                    if (!$teacher) {
                        $teacher = $teachers->random();
                    }

                    $timetableEntry = [
                        'grade_id' => $grade->id,
                        'stream_id' => $stream ? $stream->id : null,
                        'learning_area_id' => $learningArea->id,
                        'teacher_id' => $teacher->id,
                        'timeslot_id' => $timeslot->id,
                        'day' => $day,
                    ];

                    $timetableEntries[] = $timetableEntry;

                    $this->markScheduleAsOccupied($teacher, $timeslot, $timetableEntries);
                    $lessonCount++;
                    $dailyAssignedLearningAreas[] = $learningArea->id;
                }
            }
        }

        NewTimetable::insert($timetableEntries);
    }

    private function distributeLearningAreas($learningAreas, $days)
    {
        $distributed = [];
        $dayCount = count($days);
        $areaCount = count($learningAreas);

        for ($i = 0; $i < $dayCount; $i++) {
            $distributed[$days[$i]] = [];
        }

        foreach ($learningAreas as $index => $learningArea) {
            $dayIndex = $index % $dayCount;
            $distributed[$days[$dayIndex]][] = $learningArea;
        }

        return $distributed;
    }

    private function getLearningAreaForDayAndSlot($distributedLearningAreas, $day, $timeslot, $dailyAssignedLearningAreas)
    {
        if (!isset($distributedLearningAreas[$day])) {
            return null;
        }

        $dayLearningAreas = $distributedLearningAreas[$day];
        foreach ($dayLearningAreas as $learningArea) {
            if (!in_array($learningArea->id, $dailyAssignedLearningAreas)) {
                return $learningArea;
            }
        }

        return null;
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

    private function getLearningAreasForGrade($grade)
    {
        return LearningArea::whereIn('id', function($query) use ($grade) {
            $query->select('learning_area_id')
                ->from('grade_learning_areas')
                ->where('grade_id', $grade->id);
        })->get();
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
                return false; // Teacher is already booked for this timeslot
            }
        }
        return true; // Teacher is available for this timeslot
    }

    private function canTeachLearningArea($teacher, $learningArea)
    {
        return DB::table('teachers_learning_areas')
                ->where('teacher_id', $teacher->id)
                ->where('learning_area_id', $learningArea->id)
                ->exists();
    }

    private function createBreakTimetableEntry(&$timetableEntries, $grade, $stream, $timeslot, $day)
    {
        $timetableEntry = [
            'grade_id' => $grade->id,
            'stream_id' => $stream ? $stream->id : null,
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




  