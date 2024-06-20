@extends('layouts.app')

@section('content')
<h1>Create Timetable</h1>
<form action="{{ route('timetables.store') }}" method="POST">
    @csrf
    <div>
        <label for="course_id">Course</label>
        <select name="course_id" id="course_id">
            @foreach ($courses as $course)
            <option value="{{ $course->id }}">{{ $course->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="room_id">Room</label>
        <select name="room_id" id="room_id">
            @foreach ($rooms as $room)
            <option value="{{ $room->id }}">{{ $room->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="timeslot_id">Timeslot</label>
        <select name="timeslot_id" id="timeslot_id">
            @foreach ($timeslots as $timeslot)
            <option value="{{ $timeslot->id }}">{{ $timeslot->start_time }} - {{ $timeslot->end_time }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit">Create</button>
</form>
@endsection
