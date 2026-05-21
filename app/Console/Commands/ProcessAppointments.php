<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ProcessAppointments extends Command
{
    // Nama command yang dipanggil di terminal
    protected $signature = 'hospital:process-appointments';
    protected $description = 'Kirim reminder H-1 dan perbarui status janji temu otomatis';

    public function handle()
    {
        // === LOGIKA 1: REMINDER H-1 ===
        $tomorrow = Carbon::tomorrow()->toDateString();

        $appointmentsTomorrow = Appointment::whereDate('appointment_date', $tomorrow)
            ->where('status', 'confirmed') // Sesuaikan dengan status sistem Anda
            ->get();

        foreach ($appointmentsTomorrow as $appointment) {
            if ($appointment->patient?->user?->email) {
                Mail::to($appointment->patient->user->email)
                    ->send(new AppointmentReminder($appointment));
            }
        }
        $this->info('Reminder H-1 berhasil dikirim ke pasien.');

        // === LOGIKA 2: STATUS CHANGE ===
        // Mengubah status janji temu yang sudah lewat hari ini menjadi 'expired' jika belum diproses
        $expiredCount = Appointment::whereDate('appointment_date', '<', Carbon::today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->update(['status' => 'expired']);

        $this->info("Jumlah janji temu yang berubah menjadi expired: {$expiredCount}");

        $this->info('Status janji temu yang kadaluarsa berhasil diperbarui.');
    }
}
