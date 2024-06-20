@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center" style="font-weight:600; font-size:1.5em; font-family:Georgia;">Teachers Learning Areas
                    <a href="{{ route('teachers_learning_areas.create') }}" class="btn btn-primary float-right">Add New</a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Learning Area</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teachersLearningAreas as $entry)
                                <tr>
                                    <td> {{ $entry->teacher->title }} {{ $entry->teacher->first_name }} {{ $entry->teacher->surname }}</td>
                                    <td>{{ $entry->learningArea->name }}</td>
                                    <td>
                                        <a href="{{ route('teachers_learning_areas.edit', $entry->id) }}" class="btn btn-primary">Edit</a>
                                        <form action="{{ route('teachers_learning_areas.destroy', $entry->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><i class="fas fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
