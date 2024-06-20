<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="timetables-table">
            <thead>
            <tr>
                <th>Day</th>
                <th>Time</th>
                <th>Subject</th>
                <th>Teacher</th>
                <th>Class</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($timetables as $timetable)
                <tr>
                    <td>{{ $timetable->day }}</td>
                    <td>{{ $timetable->time }}</td>
                    <td>{{ $timetable->subject }}</td>
                    <td>{{ $timetable->teacher }}</td>
                    <td>{{ $timetable->class }}</td>
                    <td>{{ $timetable->created_at }}</td>
                    <td>{{ $timetable->updated_at }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['timetables.destroy', $timetable->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('timetables.show', [$timetable->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('timetables.edit', [$timetable->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $timetables])
        </div>
    </div>
</div>
