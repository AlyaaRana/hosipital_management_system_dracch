<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perubahan Status Janji Temu</title>
</head>
<body>
    <h1>Perubahan Status Janji Temu</h1>
    <p>Halo {{ $appointment->patient->user->name ?? 'Pasien' }},</p>
    <p>Status janji temu Anda telah berubah menjadi <strong>{{ ucfirst($appointment->status) }}</strong>.</p>
    <ul>
        <li>Dokter: {{ $appointment->doctor->user->name ?? 'N/A' }}</li>
        <li>Tanggal: {{ $appointment->appointment_date }}</li>
        <li>Keluhan: {{ $appointment->complaint }}</li>
    </ul>
    <p>Jika Anda memiliki pertanyaan, silakan hubungi layanan pelanggan kami.</p>
    <p>Terima kasih.</p>
</body>
</html>
