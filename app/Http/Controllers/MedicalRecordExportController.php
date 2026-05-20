<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalRecordExportController extends Controller
{
    public function exportPdf()
    {
        $records = MedicalRecord::with([
            'appointment.patient.user',
            'appointment.doctor.user'
        ])->get();

        $data = [
            'title' => 'Laporan Rekam Medis Rumah Sakit',
            'date' => date('d M Y'),
            'records' => $records
        ];
        $pdf = PDF::loadView('exports.medical_records_pdf', $data);

        return $pdf->download('laporan-rekam-medis-' . date('Ymd') . '.pdf');
    }

    public function exportCsv()
    {
        $fileName = 'laporan-rekam-medis-' . date('Ymd') . '.csv';

        $recordsQuery = MedicalRecord::with([
            'appointment.patient.user',
            'appointment.doctor.user'
        ]);

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($recordsQuery) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Rekam Medis', 'Tanggal', 'Nama Pasien', 'Nama Dokter', 'Diagnosa', 'Terapi']);

            foreach ($recordsQuery->cursor() as $record) {
                fputcsv($file, [
                    $record->id,
                    $record->created_at->format('Y-m-d'),
                    $record->appointment->patient->user->name ?? 'N/A',
                    $record->appointment->doctor->user->name ?? 'N/A',
                    $record->diagnosis,
                    $record->treatment,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
