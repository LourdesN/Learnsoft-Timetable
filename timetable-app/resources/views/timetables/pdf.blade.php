<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Timetable PDF</title>
    <style>
        @page { 
            size: A4 landscape;
        }
        body {
            font-family: Georgia;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            font-weight: 600;
            font-size: 1.5em;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table, .table th, .table td {
            border: 1px solid black;
        }
        .table th, .table td {
            padding: 10px;
            text-align: left;
        }
        .alert {
            padding: 5px;
            margin: 0;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Generated Timetable
            @if($timetables->first() && $timetables->first()->grade)
                <div>{{ $timetables->first()->grade->grade }}</div>
            @endif
            @if($selectedStream)
                <div> Stream: {{ $selectedStream->stream }}</div>
            @endif
        </div>
        @if($timetables->isEmpty())
            <div class="alert alert-danger text-center">
                No Timetable Entries &#9786;.
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Timeline</th>
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                            <th>{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($timetables->groupBy('timeslot.id') as $timeslotId => $entries)
                        <tr>
                            <td style="width:18%;">{{ $entries->first()->timeslot->start_time }} - {{ $entries->first()->timeslot->end_time }}</td>
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                @php
                                    $entry = $entries->where('day', $day)->first();
                                    $isBreak = $breaks->has($entries->first()->timeslot->start_time);
                                    $learningAreaColor = $entry ? $learningAreaColors[$entry->learningArea->name] : 'transparent';
                                @endphp
                             <td style="--bg-color: {{ $learningAreaColor }}; background-color: var(--bg-color);">
                                    @if($isBreak)
                                        <div class="alert alert-success text-center" style="background-color: #d4edda; color: #155724;">
                                            {{ $breaks[$entries->first()->timeslot->start_time]->name }}
                                        </div>
                                    @elseif($entry)
                                        <span><strong>Learning Area:</strong> {{ $entry->learningArea->name }}</span><br><br>
                                        <span><strong>Teacher:</strong> {{ $entry->teacher->title }} {{ $entry->teacher->surname }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @if ($breaks->has($entries->first()->timeslot->end_time))
                            <tr>
                                <td style="width:18%;">{{ $entries->first()->timeslot->end_time }} - {{ $breaks[$entries->first()->timeslot->end_time]->end_time }}</td>
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                    <td class="text-center" style="background-color: #d4edda; color: #155724;">
                                        <div class="alert alert-success">
                                            {{ $breaks[$entries->first()->timeslot->end_time]->name }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>








