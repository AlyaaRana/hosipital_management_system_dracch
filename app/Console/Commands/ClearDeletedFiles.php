<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ClearDeletedFiles extends Command
{
    protected $signature = 'hospital:clear-deleted-files';
    protected $description = 'Menghapus fisik file dari storage yang sudah di-soft delete lebih dari 30 hari';

    public function handle()
    {
        // Cari data file yang statusnya soft-deleted (onlyTrashed) lewat dari 30 hari
        $expiredFiles = File::onlyTrashed()
            ->where('deleted_at', '<=', Carbon::now()->subDays(30))
            ->get();

        foreach ($expiredFiles as $file) {
            // Hapus file fisik dari storage
            if (Storage::exists($file->file_path)) {
                Storage::delete($file->file_path);
            }

            // Hapus permanen baris data dari database (Force Delete)
            $file->forceDelete();
        }

        $this->info(count($expiredFiles) . ' file sampah berhasil dihapus permanen secara fisik.');
    }
}
