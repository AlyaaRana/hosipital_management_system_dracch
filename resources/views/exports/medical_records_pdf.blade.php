<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekam Medis</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Rekam Medis Admin</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Diagnosa</th>
            <th>Resep</th>
        </tr>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->diagnosis }}</td>
            <td>{{ $record->prescription }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
