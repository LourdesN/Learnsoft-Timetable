<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="grades-table">
            <thead>
            <tr>
                <th>Grade</th>
                <th>Created By</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($grades as $grade)
                <tr>
                    <td>{{ $grade->grade }}</td>
                    <td>{{ $grade->created_by }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['grades.destroy', $grade->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('grades.show', [$grade->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('grades.edit', [$grade->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $grades])
        </div>
    </div>
</div>
