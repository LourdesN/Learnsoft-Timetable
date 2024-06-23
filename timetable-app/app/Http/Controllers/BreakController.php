<?php
namespace App\Http\Controllers;

use App\Models\Breaks;
use Illuminate\Http\Request;

class BreakController extends Controller
{
    public function index()
    {
        $breaks = Breaks::all();
        return view('breaks.index', compact('breaks'));
    }

    public function edit($id)
    {
        $break = Breaks::findOrFail($id);
        return view('breaks.edit', compact('break'));
    }

    public function update(Request $request, $id)
    {
        $break = Breaks::findOrFail($id);
        $break->update($request->all());
        return redirect()->route('breaks.index')->with('success', 'Break updated successfully');
    }

    public function destroy($id)
    {
        $break = Breaks::findOrFail($id);
        $break->delete();
        return redirect()->route('breaks.index')->with('success', 'Break deleted successfully');
    }
    public function create()
{
    return view('breaks.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'duration_minutes' => 'required|integer|min:1',
    ]);

    $break = new Breaks();
    $break->name = $request->name;
    $break->duration_minutes = $request->duration_minutes;
    $break->save();

    return redirect()->route('breaks.index')->with('success', 'Break created successfully!');
}
}
