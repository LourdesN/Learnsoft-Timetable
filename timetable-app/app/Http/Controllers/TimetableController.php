<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTimetableRequest;
use App\Http\Requests\UpdateTimetableRequest;
use App\Models\GradeLearningArea;
use App\Models\TeachersLearningArea;
use App\Repositories\TimetableRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Breaks;
use App\Models\Grade;
use App\Models\LearningArea;
use App\Models\NewTimetable;
use App\Models\Teacher;
use App\Models\Timeslot;
use App\Models\Timetable;
use App\Services\TimetableGenerator;
use Illuminate\Http\Request;
use Flash;
use Response;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class TimetableController extends AppBaseController
{
    /** @var  TimetableRepository */ 
    protected $timetableGenerator;

    public function __construct(TimetableGenerator $timetableGenerator)
    {
        $this->timetableGenerator = $timetableGenerator;
    }

    public function index(Request $request)
    {
        $grades = Grade::all();
        $selectedGrade = null;
        $timetables = collect();
        $timeslots = Timeslot::all();
        $breaks = Breaks::all();

        if ($request->has('grade_id') && $request->grade_id) {
            $selectedGrade = Grade::find($request->grade_id);
            if ($selectedGrade) {
                $timetables = NewTimetable::where('grade_id', $selectedGrade->id)->get();
            }
        }

    
        return view('timetables.index', compact('grades', 'selectedGrade', 'timetables', 'timeslots', 'breaks'));
    }
    
    
 public function generateTimetableForGrade(Request $request)
{
    try {
        $gradeId = $request->input('grade_id');
        $grade = Grade::findOrFail($gradeId);

        // Check if necessary data is available before generating timetable in the databases
        if (Teacher::count() === 0 || LearningArea::count() === 0 || Timeslot::count() === 0 || Breaks::count() === 0  || GradeLearningArea::count() === 0 || TeachersLearningArea::count() === 0) {
            return redirect()->route('timetables.index')->with('error', 'Cannot generate timetable. Ensure there are teachers, learning areas, timeslots, gradelearningareas, teacherslearningareas and breaks available.');
        }

        $this->timetableGenerator->generateForGrade($grade);

        return redirect()->route('timetables.index')->with('success', 'Timetable generated successfully for grade ' . $grade->grade);
    } catch (\Exception $e) {
        return redirect()->route('timetables.index')->with('error', 'Failed to generate timetable. Ensure there are teachers, learning areas, timeslots, grade_learning_areas, teachers_learning_areas and breaks data available. ');
    }
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'room_id' => 'required|exists:rooms,id',
            'timeslot_id' => 'required|exists:timeslots,id',
        ]);

        Timetable::create($validated);

        return redirect()->route('timetables.index');
    }
    public function destroy()
    {
        NewTimetable::truncate();

        return redirect()->route('timetables.index')->with('success', 'Timetable deleted successfully.');
    }

    public function exportPDF(Request $request)
    {
        $timetables = NewTimetable::with(['timeslot', 'grade', 'learningArea', 'teacher'])
            ->when($request->grade_id, function ($query) use ($request) {
                $query->where('grade_id', $request->grade_id);
            })
            ->get();
            
        $breaks = Breaks::all()->keyBy('start_time');
    
        $pdf = FacadePdf::loadView('timetables\pdf', compact('timetables', 'breaks'));
    
        return $pdf->download('timetables.pdf');
    }
    
}