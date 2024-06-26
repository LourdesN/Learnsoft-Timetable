@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <a href="{{ route('generate.timetable') }}" class="btn btn-success mb-3 mt-2">Generate Timetable (All Grades)</a>
            <form action="{{ route('delete.timetable') }}" method="POST" style="display: inline; float:right;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger mb-3 mt-2">Delete Timetable</button>
            </form>
          
            <form action="{{ route('timetables.index') }}" method="GET" class="mb-3">
                <div class="input-group" style="width:300px; margin-left: 35%;">
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
            <div class="mb-3 ">
                <a href="{{ route('timetable.export.pdf', ['grade_id' => request('grade_id')]) }}" class="btn btn-success">Export to PDF</a>
            </div>
            <div class="card">
                <div class="card-header text-center" style="font-weight:600; font-size:1.5em; font-family:Georgia;">Generated Timetable</div>
                <div class="card-body">
                    @if(!empty($timetables) && !$timetables->isEmpty())
                    <div class="mb-3 text-center" style="font-weight:600; font-size:1.2em;">
                        {{ $timetables->first()->grade->grade }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Timeline</th>
                                    @foreach($daysOfWeek as $day)
                                    <th>{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timetables->groupBy('timeslot_id') as $timeslotId => $entries)
                                <tr>
                                    <td style="width:18%;">{{ $entries->first()->timeslot->start_time }} - {{ $entries->first()->timeslot->end_time }}</td>
                                    @foreach($daysOfWeek as $day)
                                    <td>
                                        @php
                                        $entry = $entries->firstWhere('day', $day);
                                        @endphp
                                        @if($entry)
                                            @if(array_key_exists($entry->timeslot->end_time, $breaks))
                                                <div class="alert alert-success text-center">
                                                    {{ $breaks[$entry->timeslot->end_time] }}
                                                </div>
                                            @else
                                                <strong>Learning Area:</strong> {{ $entry->learningArea->name }}<br><br>
                                                <strong>Teacher:</strong> {{ $entry->teacher->title }} {{ $entry->teacher->surname }}
                                            @endif
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($timetables->isEmpty())
                        <div class="alert alert-success text-center">No timetable entries found.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection









