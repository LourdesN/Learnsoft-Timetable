<?php
namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::all();
        return view('schedules.index', compact('schedules'));
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        return view('schedules.edit', compact('schedule'));
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->update($request->all());
        return redirect()->route('schedules.index')->with('success', 'Schedule updated successfully');
    }
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully');
    }
    public function create()
{
    return view('schedules.create');
}

public function store(Request $request)
{
    $request->validate([
        'day' => 'required|string|max:255',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
    ]);

    $schedule = new Schedule();
    $schedule->day = $request->day;
    $schedule->start_time = $request->start_time;
    $schedule->end_time = $request->end_time;
    $schedule->save();

    return redirect()->route('schedules.index')->with('success', 'Schedule created successfully!');
}

}
