<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reminder Janji Temu Besok</title>
</head>
<body>
    <h1>Reminder Janji Temu</h1>
    <p>Halo {{ $appointment->patient->user->name ?? 'Pasien' }},</p>
    <p>Ini adalah pengingat bahwa janji temu Anda akan dilaksanakan besok dengan detail berikut:</p>
    <ul>
        <li>Dokter: {{ $appointment->doctor->user->name ?? 'N/A' }}</li>
        <li>Tanggal: {{ $appointment->appointment_date }}</li>
        <li>Status: {{ ucfirst($appointment->status) }}</li>
        <li>Keluhan: {{ $appointment->complaint }}</li>
    </ul>
    <p>Mohon datang tepat waktu dan siapkan dokumen medis jika diperlukan.</p>
    <p>Terima kasih.</p>
</body>
</html>
