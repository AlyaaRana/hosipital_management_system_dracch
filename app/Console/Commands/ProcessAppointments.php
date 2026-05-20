<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Notifications\AppointmentReminderNotification; // Buat notification baru jika diperlukan
use Carbon\Carbon;

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
            $patient = $appointment->patient;
            // Kirim notifikasi/email pengingat
            // $patient->notify(new AppointmentReminderNotification($appointment));
        }
        $this->info('Reminder H-1 berhasil dikirim ke pasien.');

        // === LOGIKA 2: STATUS CHANGE ===
        // Mengubah status janji temu yang sudah lewat hari ini menjadi 'expired' atau 'done' jika belum diproses
        Appointment::where('appointment_date', '<', Carbon::today())
            ->where('status', 'pending') // atau 'confirmed' yang terlewat
            ->update(['status' => 'expired']);

        $this->info('Status janji temu yang kadaluarsa berhasil diperbarui.');
    }
}
