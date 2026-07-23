<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupAuto extends Command
{
    protected $signature = 'backup:auto';
    protected $description = 'Automatically back up the database (used by scheduled task)';

    public function handle()
    {
        $settings  = AppSetting::current();
        $backupDir = storage_path('app/backups');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $source   = database_path('database.sqlite');
        $filename = 'auto_backup_' . date('Y-m-d_His') . '.sqlite';

        File::copy($source, $backupDir . '/' . $filename);

        // Also copy to external path if configured
        if ($settings->backup_external_path && File::exists($settings->backup_external_path)) {
            File::copy($source, rtrim($settings->backup_external_path, '\\/') . '/' . $filename);
        }

        $settings->last_backup_at = now();
        $settings->save();

        // Keep only the last 30 automatic backups to avoid filling up disk space
        $backups = collect(File::files($backupDir))
            ->filter(fn ($f) => str_starts_with($f->getFilename(), 'auto_backup_'))
            ->sortByDesc(fn ($f) => $f->getMTime());

        $backups->slice(30)->each(fn ($f) => File::delete($f->getPathname()));

        $this->info('Backup created: ' . $filename);
    }
}
