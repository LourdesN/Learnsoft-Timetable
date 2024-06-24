@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <a href="{{ route('generate.timetable') }}" class="btn btn-success mb-3 mt-2">Generate Timetable</a>
            <form action="{{ route('delete.timetable') }}" method="POST" style="display: inline; float:right;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger mb-3 mt-2">Delete Timetable</button>
            </form>
            <form action="{{ route('timetables.index') }}" method="GET" class="mb-3">
                <div class="input-group" style="width:300px; margin-left: 35%;" >
                    <select name="grade_id" class="form-control">
                        <option value="">Select Grade</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->grade }}
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit">Search</button>
                    </div>
                </div>
            </form>
            <div class="mb-3">
                <a href="{{ route('timetable.export.pdf', ['grade_id' => request('grade_id')]) }}" class="btn btn-secondary">Export to PDF</a>
            </div>
            <div class="card">
                <div class="card-header text-center" style="font-weight:600; font-size:1.5em; font-family:Georgia;">Generated Timetable</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Grade</th>
                                    <th>Learning Area</th>
                                    <th>Teacher</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timetables as $entry)
                                    <tr>
                                        <td>{{ $entry->day }}</td>
                                        <td>{{ $entry->timeslot ? $entry->timeslot->start_time : 'N/A' }}</td>
                                        <td>{{ $entry->timeslot ? $entry->timeslot->end_time : 'N/A' }}</td>
                                        <td>{{ $entry->grade->grade }}</td>
                                        <td>{{ $entry->learningArea->name }}</td>
                                        <td>{{ $entry->teacher->title }} {{ $entry->teacher->surname }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($timetables->isEmpty())
                            <div class="alert alert-info text-center">No timetable entries found.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection





