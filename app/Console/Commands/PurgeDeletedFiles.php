<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File as FileModel;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PurgeDeletedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:purge {--days=7}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge soft-deleted files older than N days and remove physical files';

    public function handle()
    {
        $days = (int) $this->option('days');
        $threshold = Carbon::now()->subDays($days);

        $this->info("Purging files soft-deleted before: {$threshold}");

        $files = FileModel::onlyTrashed()->where('deleted_at', '<=', $threshold)->get();
        $this->info('Found ' . $files->count() . ' records to purge');

        foreach ($files as $file) {
            try {
                if ($file->path && Storage::exists($file->path)) {
                    Storage::delete($file->path);
                    $this->info("Deleted physical file: {$file->path}");
                }
                $file->forceDelete();
                $this->info("Purged DB record id={$file->id}");
            } catch (\Exception $e) {
                $this->error('Failed to purge file id=' . $file->id . ' : ' . $e->getMessage());
            }
        }

        $this->info('Purge complete.');
        return 0;
    }
}
