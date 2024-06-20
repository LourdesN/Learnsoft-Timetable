<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\GradeRepository;
use Illuminate\Http\Request;
use Flash;

class GradeController extends AppBaseController
{
    /** @var GradeRepository $gradeRepository*/
    private $gradeRepository;

    public function __construct(GradeRepository $gradeRepo)
    {
        $this->gradeRepository = $gradeRepo;
    }

    /**
     * Display a listing of the Grade.
     */
    public function index(Request $request)
    {
        $grades = $this->gradeRepository->paginate(10);

        return view('grades.index')
            ->with('grades', $grades);
    }

    /**
     * Show the form for creating a new Grade.
     */
    public function create()
    {
        return view('grades.create');
    }

    /**
     * Store a newly created Grade in storage.
     */
    public function store(CreateGradeRequest $request)
    {
        $input = $request->all();

        $grade = $this->gradeRepository->create($input);

        Flash::success('Grade saved successfully.');

        return redirect(route('grades.index'));
    }

    /**
     * Display the specified Grade.
     */
    public function show($id)
    {
        $grade = $this->gradeRepository->find($id);

        if (empty($grade)) {
            Flash::error('Grade not found');

            return redirect(route('grades.index'));
        }

        return view('grades.show')->with('grade', $grade);
    }

    /**
     * Show the form for editing the specified Grade.
     */
    public function edit($id)
    {
        $grade = $this->gradeRepository->find($id);

        if (empty($grade)) {
            Flash::error('Grade not found');

            return redirect(route('grades.index'));
        }

        return view('grades.edit')->with('grade', $grade);
    }

    /**
     * Update the specified Grade in storage.
     */
    public function update($id, UpdateGradeRequest $request)
    {
        $grade = $this->gradeRepository->find($id);

        if (empty($grade)) {
            Flash::error('Grade not found');

            return redirect(route('grades.index'));
        }

        $grade = $this->gradeRepository->update($request->all(), $id);

        Flash::success('Grade updated successfully.');

        return redirect(route('grades.index'));
    }

    /**
     * Remove the specified Grade from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $grade = $this->gradeRepository->find($id);

        if (empty($grade)) {
            Flash::error('Grade not found');

            return redirect(route('grades.index'));
        }

        $this->gradeRepository->delete($id);

        Flash::success('Grade deleted successfully.');

        return redirect(route('grades.index'));
    }
}
