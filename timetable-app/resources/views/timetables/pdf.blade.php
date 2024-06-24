<!DOCTYPE html>
<html>
<head>
    <title>Timetable</title>
    <style>
        body {
            font-family: Georgia;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }
        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: #fff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <h2>St Peter and St Paul Junior Secondary School</h2>
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
