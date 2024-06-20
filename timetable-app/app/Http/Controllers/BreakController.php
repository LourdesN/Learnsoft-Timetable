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
}
