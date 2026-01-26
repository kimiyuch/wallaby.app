<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database {--keep=7 : Number of backups to keep}';

    protected $description = 'Create a backup of the SQLite database';

    public function handle(): void
    {
        $dbPath = database_path('database.sqlite');
        $backupDir = storage_path('backups');

        if (! File::exists($dbPath)) {
            $this->error('Database file not found: '.$dbPath);

            return;
        }

        // Create backup directory if it doesn't exist
        if (! File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // Create backup with timestamp
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupPath = $backupDir.'/database_'.$timestamp.'.sqlite';

        File::copy($dbPath, $backupPath);

        $this->info("✓ Backup created: {$backupPath}");

        // Cleanup old backups
        $this->cleanup($backupDir);
    }

    protected function cleanup(string $backupDir): void
    {
        $keep = (int) $this->option('keep');
        $files = File::glob($backupDir.'/database_*.sqlite');

        // Sort by modification time (newest first)
        usort($files, fn ($a, $b) => filemtime($b) - filemtime($a));

        // Delete old backups
        $toDelete = array_slice($files, $keep);

        foreach ($toDelete as $file) {
            File::delete($file);
            $this->line('  Deleted old backup: '.basename($file));
        }

        if (count($toDelete) > 0) {
            $this->info('✓ Cleaned up '.count($toDelete).' old backup(s)');
        }
    }
}
