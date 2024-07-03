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
use App\Models\Stream;
use App\Models\Teacher;
use App\Models\Timeslot;
use App\Services\TimetableGenerator;
use Illuminate\Http\Request;
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
        $streams = stream::all(); 
        $selectedGrade = null;
        $selectedStream = null;
        $timetables = collect();
        $timeslots = Timeslot::all();
        $breaks = Breaks::all();
        $learningAreas = LearningArea::all();
    
        // Function to generate random colors
        function generateRandomColor()
        {
            $h = mt_rand(0, 360); // Hue
            $s = mt_rand(42, 98); // Saturation
            $l = mt_rand(60, 90); // Adjust for lighter colors
            return "hsl($h, {$s}%, {$l}%)";
        }
    
        // Generating colors for each learning area
        $learningAreaColors = [];
        foreach ($learningAreas as $learningArea) {
            $learningAreaColors[$learningArea->name] = generateRandomColor();
        }
    
        // Fetch the selected grade and its streams
        if ($request->has('grade_id') && $request->grade_id) {
            $selectedGrade = Grade::find($request->grade_id);
            if ($selectedGrade) {
                $streams = $selectedGrade->streams;
            }
        }
    
        // Fetch the selected stream
        if ($request->has('stream_id') && $request->stream_id) {
            $selectedStream = Stream::find($request->stream_id);
        }
    
        // Fetch timetables based on the selected grade and stream
        if ($selectedGrade) {
            $timetablesQuery = NewTimetable::with(['timeslot', 'grade', 'learningArea', 'teacher'])
                ->where('grade_id', $selectedGrade->id);
    
            if ($selectedStream) {
                $timetablesQuery->where('stream_id', $selectedStream->id);
            }
    
            $timetables = $timetablesQuery->get();
        }
    
        return view('timetables.index', compact('grades', 'streams', 'selectedGrade', 'selectedStream', 'timetables', 'timeslots', 'breaks', 'learningAreaColors'));
    }
    
    
    
    public function generateTimetableForGrade(Request $request)
    {
        try {
            $gradeId = $request->input('grade_id');
            $streamId = $request->input('stream_id');
        
            $grade = Grade::findOrFail($gradeId);
            
            // Check if necessary data is available before generating timetable in the databases
            if (Teacher::count() === 0 || LearningArea::count() === 0 || Timeslot::count() === 0 || Breaks::count() === 0  || GradeLearningArea::count() === 0 || TeachersLearningArea::count() === 0) {
                return redirect()->route('timetables.index')->with('error', 'Cannot generate timetable. Ensure there are teachers, learning areas, timeslots, grade learning areas, teachers learning areas and breaks available.');
            }
    
            if ($streamId) {
                $stream = Stream::findOrFail($streamId);
                $this->timetableGenerator->generateForGrade($grade, $stream);
                return redirect()->route('timetables.index', ['grade_id' => $gradeId, 'stream_id' => $streamId])->with('success', 'Timetable generated successfully for ' . $grade->grade . ' - ' . $stream->stream);
            } else {
                $this->timetableGenerator->generateForGrade($grade);
                return redirect()->route('timetables.index', ['grade_id' => $gradeId])->with('success', 'Timetable generated successfully for ' . $grade->grade);
            }
        } catch (\Exception $e) {
            return redirect()->route('timetables.index')->with('error', 'Failed to generate timetable. Ensure there are teachers, grades, streams, learning areas, timeslots, grade learning areas, teachers learning areas and breaks data available.');
        }
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'room_id' => 'required|exists:rooms,id',
            'timeslot_id' => 'required|exists:timeslots,id',
        ]);

        NewTimetable::create($validated);

        return redirect()->route('timetables.index');
    }
    public function destroy(Request $request)
    {
        $gradeId = $request->input('grade_id');
        $streamId = $request->input('stream_id');
    
        if (!$gradeId) {
            return redirect()->route('timetables.index')->with('error', 'Grade ID is required to delete timetable.');
        }
    
        $query = NewTimetable::where('grade_id', $gradeId);
    
        if ($streamId) {
            $query->where('stream_id', $streamId);
        }
    
        $deleted = $query->delete();
    
        if ($deleted) {
            if ($streamId) {
                return redirect()->route('timetables.index')->with('success', 'Timetable deleted successfully for the selected grade and stream.');
            } else {
                return redirect()->route('timetables.index')->with('success', 'Timetable deleted successfully for the selected grade.');
            }
        } else {
            return redirect()->route('timetables.index')->with('error', 'No timetables found to delete.');
        }
    }
    
    public function exportPDF(Request $request)
    {
        $timetables = NewTimetable::with(['timeslot', 'grade', 'learningArea', 'teacher'])
            ->when($request->grade_id, function ($query) use ($request) {
                $query->where('grade_id', $request->grade_id);
            })
            ->when($request->stream_id, function ($query) use ($request) {
                $query->where('stream_id', $request->stream_id);
            })
            ->get();
        
        $breaks = Breaks::all()->keyBy('start_time');
        $learningAreas = LearningArea::all();
    
         function generateRandomColor()
        {
            $h = mt_rand(0, 360); // Hue
            $s = mt_rand(42, 98); // Saturation
            $l = mt_rand(60, 90); //hii ndio na adjust if i need lighter colours
            return "hsl($h, {$s}%, {$l}%)";
        }
   
        $learningAreaColors = [];
        foreach ($learningAreas as $learningArea) {
            $learningAreaColors[$learningArea->name] = generateRandomColor();
        }

        $selectedGrade = Grade::find($request->grade_id);
        $selectedStream = Stream::find($request->stream_id);
    
        $pdf = FacadePdf::loadView('timetables\pdf', compact('timetables', 'breaks', 'learningAreaColors', 'selectedGrade', 'selectedStream'))
                        ->setPaper('a4', 'landscape');
    
        return $pdf->download('timetables.pdf');
    }
    
}