<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTimetableRequest;
use App\Http\Requests\UpdateTimetableRequest;
use App\Repositories\TimetableRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Grade;
use App\Models\NewTimetable;
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
    private $timetableRepository;

    public function __construct(TimetableRepository $timetableRepo)
    {
        $this->timetableRepository = $timetableRepo;
    }

    public function index(Request $request)
    {
        $grades = Grade::all();
        $gradeId = $request->input('grade_id');
    
        // Eager load relationships and optionally filter by grade_id
        $query = NewTimetable::with(['grade', 'learningArea', 'teacher', 'timeslot']);
    
        if ($gradeId) {
            $query->where('grade_id', $gradeId);
        }
    
        // Fetch data
        $timetables = $query->get();
    
        return view('timetables.index', compact('timetables', 'grades'));
    }
    

    public function generateTimetable(TimetableGenerator $timetableGenerator)
    {
        $timetableGenerator->generate();

        return redirect()->route('timetables.index')->with('success', 'Timetable generated successfully.');
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

    // Use forward slashes '/' in the view name, not backslashes '\'
    $pdf = FacadePdf::loadView('timetables\pdf', compact('timetables'));

    // Specify a file name with .pdf extension for download
    return $pdf->download('timetables.pdf');
}
}