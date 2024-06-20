@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center" style="font-weight:600; font-size:1.5em; font-family:Georgia;">Edit Break</div>
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

                    <form action="{{ route('breaks.update', $break->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Break Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $break->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="duration_minutes">Duration (minutes)</label>
                            <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $break->duration_minutes) }}" required>
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="{{ route('breaks.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
