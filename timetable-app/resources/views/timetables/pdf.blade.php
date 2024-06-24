<!DOCTYPE html>
<html>
<head>
    <title>Timetable</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Generated Timetable</h2>
    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Grade</th>
                <th>Learning Area</th>
                <th>Teacher</th>
            </tr>
        </thead>
        <tbody>
            @foreach($timetables as $entry)
                <tr>
                    <td>{{ $entry->day }}</td>
                    <td>{{ $entry->timeslot ? $entry->timeslot->start_time : 'N/A' }}</td>
                    <td>{{ $entry->timeslot ? $entry->timeslot->end_time : 'N/A' }}</td>
                    <td>{{ $entry->grade->grade }}</td>
                    <td>{{ $entry->learningArea->name }}</td>
                    <td>{{ $entry->teacher->title }} {{ $entry->teacher->surname }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
