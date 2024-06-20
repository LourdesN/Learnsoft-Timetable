<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeachersLearningArea; 
use App\Models\Teacher;
use App\Models\LearningArea;

class TeachersLearningAreasController extends Controller
{
    public function index()
    {
        $teachersLearningAreas = TeachersLearningArea::with(['teacher', 'learningArea'])->get();
        return view('teachers_learning_areas.index', compact('teachersLearningAreas'));
    }

    public function create()
    {
        $teachers = Teacher::all();
        $learningAreas = LearningArea::all();
        return view('teachers_learning_areas.create', compact('teachers', 'learningAreas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'learning_area_id' => 'required|exists:learning_areas,id',
        ]);

        TeachersLearningArea::create($request->all());
        return redirect()->route('teachers_learning_areas.index')->with('success', 'Teacher Learning Area created successfully.');
    }

    public function edit($id)
    {
        $teachersLearningArea = TeachersLearningArea::findOrFail($id);
        $teachers = Teacher::all();
        $learningAreas = LearningArea::all();
        return view('teachers_learning_areas.edit', compact('teachersLearningArea', 'teachers', 'learningAreas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'learning_area_id' => 'required|exists:learning_areas,id',
        ]);

        $teachersLearningArea = TeachersLearningArea::findOrFail($id);
        $teachersLearningArea->update($request->all());

        return redirect()->route('teachers_learning_areas.index')->with('success', 'Teacher Learning Area updated successfully.');
    }

    public function destroy($id)
    {
        $teachersLearningArea = TeachersLearningArea::findOrFail($id);
        $teachersLearningArea->delete();
        return redirect()->route('teachers_learning_areas.index')->with('success', 'Teacher Learning Area deleted successfully.');
    }
}

