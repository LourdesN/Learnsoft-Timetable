<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTimetableRequest;
use App\Http\Requests\UpdateTimetableRequest;
use App\Repositories\TimetableRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\NewTimetable;
use App\Models\Timetable;
use App\Services\TimetableGenerator;
use Illuminate\Http\Request;
use Flash;
use Response;

class TimetableController extends AppBaseController
{
    /** @var  TimetableRepository */
    private $timetableRepository;

    public function __construct(TimetableRepository $timetableRepo)
    {
        $this->timetableRepository = $timetableRepo;
    }

    public function index()
    {
        $timetables = NewTimetable::with(['learningArea', 'teacher', 'grade', 'schedule'])
                                  ->get()
                                  ->groupBy('day');
        return view('timetables.index', compact('timetables'));
    }
    


    public function generateTimetable(TimetableGenerator $timetableGenerator)
    {
        $timetable = $timetableGenerator->generate();

        // Save the generated timetable to the database
        foreach ($timetable as $entry) {
            NewTimetable::create($entry);
        }

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
}