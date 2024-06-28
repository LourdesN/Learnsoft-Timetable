<?php

use App\Http\Controllers\BreakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TeachersLearningAreasController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable');
Route::resource('timetables', TimetableController::class);
Route::delete('/timetable', [TimetableController::class, 'destroy'])->name('delete.timetable');


Route::resource('grades', App\Http\Controllers\GradeController::class);
Route::get('/generate-timetable', [TimetableController::class, 'generateTimetable'])->name('generate.timetable');

Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
Route::get('/schedules/{id}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
Route::put('/schedules/{id}', [ScheduleController::class, 'update'])->name('schedules.update');
Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');



Route::get('/breaks', [BreakController::class, 'index'])->name('breaks.index');
Route::get('/breaks/{id}/edit', [BreakController::class, 'edit'])->name('breaks.edit');
Route::put('/breaks/{id}', [BreakController::class, 'update'])->name('breaks.update');
Route::delete('/breaks/{id}', [BreakController::class, 'destroy'])->name('breaks.destroy');
Route::get('/breaks/create', [BreakController::class, 'create'])->name('breaks.create');
Route::post('/breaks', [BreakController::class, 'store'])->name('breaks.store');


Route::resource('teachers_learning_areas', TeachersLearningAreasController::class);

Route::get('timetable/export/pdf', [TimetableController::class, 'exportPDF'])->name('timetable.export.pdf');


Route::post('generate-timetable-by-grade', [TimetableController::class, 'generateTimetableForGrade'])->name('generate.timetable.by.grade');

Route::get('/generate-timetable/{gradeId}', [TimetableController::class, 'generateTimetableForGrade']);

