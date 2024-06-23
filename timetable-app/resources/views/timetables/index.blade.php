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
            <div class="card">
                <div class="card-header text-center" style="font-weight:600; font-size:1.5em; font-family:Georgia;">Generated Timetable</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">Day</th>
                                    <th style="width: 20%">Time</th>
                                    <th style="width: 15%">Grade</th>
                                    <th style="width: 30%">Learning Area</th>
                                    <th style="width: 25%">Teacher</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timetables as $day => $entries)
                                <tr>
                                <td rowspan="{{ count($entries) }}" class="align-middle" style="border-bottom: 2px solid;">{{ $day }}</td>
                                    @foreach($entries as $key => $entry)
                                    
                                    <td> @if($entry->timeslot)
                                            {{ $entry->timeslot->start_time }} - {{ $entry->timeslot->end_time }}
                                        @else
                                            N/A
                                        @endif</td>
                                        <td>{{ $entry->grade->grade }}</td>
                                        <td>{{ $entry->learningArea->name }}</td>
                                        <td>{{ $entry->teacher->title }} {{ $entry->teacher->surname }}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




