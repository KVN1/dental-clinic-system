<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class RunScheduledBackup extends Command
{
    protected $signature = 'backup:auto';
    protected $description = 'Create a database backup based on the configured schedule';

    public function handle()
    {
        $settings = AppSetting::current();
        $now = now();
        $backupTime = Carbon::parse($settings->backup_time);

        $isDue = false;

        if ($settings->backup_frequency_type === 'daily') {
            // Due if it's past today's scheduled time, and we haven't backed up since then
            $todayScheduled = $now->copy()->setTimeFrom($backupTime);
            $isDue = $now->greaterThanOrEqualTo($todayScheduled)
                && (! $settings->last_backup_at || $settings->last_backup_at->lessThan($todayScheduled));
        } else {
            // Hourly: due if we've passed the start time today, and at least an hour has passed since the last backup
            $todayStart = $now->copy()->setTimeFrom($backupTime);
            $isDue = $now->greaterThanOrEqualTo($todayStart)
                && (! $settings->last_backup_at || $settings->last_backup_at->diffInMinutes($now) >= 60);
        }

        if (! $isDue) {
            $this->info('Backup not due yet. Last backup: ' . ($settings->last_backup_at ?? 'never'));
            return;
        }

        $filename = 'backup_' . now()->format('Y-m-d_His') . '.sqlite';
        $source = database_path('database.sqlite');

        $internalDir = storage_path('app/backups');
        if (! File::exists($internalDir)) {
            File::makeDirectory($internalDir, 0755, true);
        }
        File::copy($source, $internalDir . '/' . $filename);

        if ($settings->backup_external_path && File::exists($settings->backup_external_path)) {
            File::copy($source, rtrim($settings->backup_external_path, '\\/') . '/' . $filename);
        }

        $settings->last_backup_at = now();
        $settings->save();

        $this->info('Backup created: ' . $filename);
        $this->sendNotification('Clear Smile Dental', 'Backup completed: ' . $filename);
    }

    private function sendNotification(string $title, string $message)
    {
        $script = <<<PS
        [Windows.UI.Notifications.ToastNotificationManager, Windows.UI.Notifications, ContentType = WindowsRuntime] > \$null
        \$template = [Windows.UI.Notifications.ToastNotificationManager]::GetTemplateContent([Windows.UI.Notifications.ToastTemplateType]::ToastText02)
        \$textNodes = \$template.GetElementsByTagName('text')
        \$textNodes.Item(0).AppendChild(\$template.CreateTextNode('$title')) | Out-Null
        \$textNodes.Item(1).AppendChild(\$template.CreateTextNode('$message')) | Out-Null
        \$toast = [Windows.UI.Notifications.ToastNotification]::new(\$template)
        [Windows.UI.Notifications.ToastNotificationManager]::CreateToastNotifier('Clear Smile Dental')::Show(\$toast)
        PS;

        $tempFile = sys_get_temp_dir() . '/dental_notify.ps1';
        file_put_contents($tempFile, $script);

        exec('powershell -ExecutionPolicy Bypass -WindowStyle Hidden -File ' . escapeshellarg($tempFile) . ' > NUL 2>&1');
    }
}