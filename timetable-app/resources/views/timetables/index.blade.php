@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="font-family: Georgia; width: 90%; margin-top: 1%;">
                {{ session('error') }}
            </div>
        @endif

        <div class="col-md-12">
            <form action="{{ route('delete.timetable') }}" method="POST" style="display: inline; float:right;">
                @csrf
                @method('DELETE')
                <div class="input-group mb-3 mt-2">
                    <select name="grade_id" class="form-control">
                        <option value="">Select Grade</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}">{{ $grade->grade }}</option>
                        @endforeach
                    </select>
                    <select name="stream_id" class="form-control">
                     <option value="">Select Stream</option>
                     @foreach($streams as $stream)
                        <option value="{{ $stream->id }}" {{ request('stream_id') == $stream->id ? 'selected' : '' }}>
                            {{ $stream->stream }}
                        </option>
                    @endforeach
                    </select>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-danger">Delete Timetable</button>
                    </div>
                </div>
            </form>
            <form action="{{ route('generate.timetable.by.grade') }}" method="POST">
                @csrf
                <div class="input-group mt-2 mb-4" style="width:443px;">
                    <select name="grade_id" class="form-control" id="grade_id">
                        <option value="">Select Grade</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}">{{ $grade->grade }}</option>
                        @endforeach
                    </select>
                    <select name="stream_id" class="form-control" id="stream_id">
                        <option value="">Select Stream to Generate</option>
                        @foreach($streams as $stream)
                            <option value="{{ $stream->id }}">{{ $stream->stream }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-success mb-3">Generate Timetable</button>
                    </div>
                </div>
            </form>
            <form action="{{ route('timetables.index') }}" method="GET" class="mb-3">
                <div class="input-group" style="width:350px; margin-left: 35%;">
                    <select name="grade_id" class="form-control">
                        <option value="">Select Grade</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->grade }}
                            </option>
                        @endforeach
                    </select>
                    <select name="stream_id" class="form-control">
                     <option value="">Select Stream</option>
                     @foreach($streams as $stream)
                        <option value="{{ $stream->id }}" {{ request('stream_id') == $stream->id ? 'selected' : '' }}>
                            {{ $stream->stream }}
                        </option>
                    @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit">Search</button>
                    </div>
                </div>
            </form>
            <div class="mb-3">
                <a href="{{ route('timetable.export.pdf', ['grade_id' => request('grade_id'), 'stream_id' => request('stream_id')]) }}" class="btn btn-success">Export Timetable to PDF</a>
            </div>
            <div class="card">
                <div class="card-header text-center" style="font-weight:600; font-size:1.5em; font-family:Georgia;">
                    Generated Timetable
                    @if($selectedGrade)
                        <div>{{ $selectedGrade->grade }}</div>
                    @endif
                    @if($selectedStream)
                     <div>Stream {{ $selectedStream->stream }}</div>
                     @endif
                </div>
                <div class="card-body">
                    @if($timetables->isEmpty())
                        <div class="alert alert-danger text-center">
                            No Timetable Entries &#9786;.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Timeline</th>
                                        @foreach(\App\Services\TimetableGenerator::DAYS_OF_WEEK as $day)
                                            <th>{{ $day }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($timeslots as $timeslot)
                                        <tr>
                                            <td style="width:18%;">{{ $timeslot->start_time }} - {{ $timeslot->end_time }}</td>
                                            @foreach(\App\Services\TimetableGenerator::DAYS_OF_WEEK as $day)
                                                @php
                                                    $entry = $timetables->where('day', $day)
                                                                        ->where('timeslot_id', $timeslot->id)
                                                                        ->first();
                                                    $isBreak = $breaks->where('start_time', $timeslot->start_time)
                                                                      ->where('end_time', $timeslot->end_time)
                                                                      ->first();
                                                @endphp
                                                    <td @if($entry) style="background-color: {{ $learningAreaColors[$entry->learningArea->name] ?? '#FFFFFF' }};" @endif>
                                                    @if($isBreak)
                                                    <div class="alert alert-success text-center" style="background-color: #d4edda; color: #155724;">
                                                            {{ $isBreak->name }}
                                                        </div>
                                                    @elseif($entry)
                                                        <span><strong>Learning Area:</strong> {{ $entry->learningArea->name }}</span><br><br>
                                                        <span><strong>Teacher:</strong> {{ $entry->teacher->title }} {{ $entry->teacher->surname }}</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        @foreach ($breaks as $break)
                                            @if ($timeslot->end_time === $break->start_time)
                                                <tr>
                                                    <td style="width:18%;">{{ $break->start_time }} - {{ $break->end_time }}</td>
                                                    @foreach(\App\Services\TimetableGenerator::DAYS_OF_WEEK as $day)
                                                        <td class="text-center">
                                                            <div class="alert alert-success" style="font-size:13px; font-weight:600; width:7rem;">
                                                                {{ $break->name }}
                                                            </div>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
