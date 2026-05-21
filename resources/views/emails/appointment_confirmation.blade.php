<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Konfirmasi Janji Temu</title>
</head>
<body>
    <h1>Konfirmasi Janji Temu</h1>
    <p>Halo {{ $appointment->patient->user->name ?? 'Pasien' }},</p>
    <p>Janji temu Anda telah berhasil dibuat dengan detail berikut:</p>
    <ul>
        <li>Dokter: {{ $appointment->doctor->user->name ?? 'N/A' }}</li>
        <li>Tanggal: {{ $appointment->appointment_date }}</li>
        <li>Status: {{ ucfirst($appointment->status) }}</li>
        <li>Keluhan: {{ $appointment->complaint }}</li>
    </ul>
    <p>Silakan cek kembali informasi janji temu Anda di aplikasi.</p>
    <p>Terima kasih telah menggunakan layanan kami.</p>
</body>
</html>
