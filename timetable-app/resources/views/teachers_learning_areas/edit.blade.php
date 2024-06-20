@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Teacher Learning Area</div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('teachers_learning_areas.update', $teachersLearningArea->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="teacher_id">Teacher</label>
                            <select name="teacher_id" class="form-control" required>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ $teachersLearningArea->teacher_id == $teacher->id ? 'selected' : '' }}> {{ $teacher->title }} {{ $teacher->first_name }} {{ $teacher->surname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="learning_area_id">Learning Area</label>
                            <select name="learning_area_id" class="form-control" required>
                                @foreach($learningAreas as $learningArea)
                                    <option value="{{ $learningArea->id }}" {{ $teachersLearningArea->learning_area_id == $learningArea->id ? 'selected' : '' }}>{{ $learningArea->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="{{ route('teachers_learning_areas.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
