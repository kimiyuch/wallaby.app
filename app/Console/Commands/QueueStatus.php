<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueueStatus extends Command
{
    protected $signature = 'app:queue-status {--failed : Show failed jobs}';

    protected $description = 'Show queue status and pending jobs';

    public function handle(): void
    {
        if ($this->option('failed')) {
            $this->showFailedJobs();

            return;
        }

        $jobs = DB::table('jobs')->get();

        if ($jobs->isEmpty()) {
            $this->info('✓ Queue is empty');

            return;
        }

        $this->info("Pending jobs: {$jobs->count()}");
        $this->newLine();

        $rows = $jobs->map(function ($job) {
            $payload = json_decode($job->payload);

            return [
                'id' => $job->id,
                'queue' => $job->queue,
                'job' => class_basename($payload->displayName ?? 'Unknown'),
                'attempts' => $job->attempts,
                'created' => date('Y-m-d H:i:s', $job->created_at),
            ];
        });

        $this->table(['ID', 'Queue', 'Job', 'Attempts', 'Created'], $rows);
    }

    protected function showFailedJobs(): void
    {
        $jobs = DB::table('failed_jobs')->get();

        if ($jobs->isEmpty()) {
            $this->info('✓ No failed jobs');

            return;
        }

        $this->error("Failed jobs: {$jobs->count()}");
        $this->newLine();

        $rows = $jobs->map(function ($job) {
            $payload = json_decode($job->payload);

            return [
                'id' => $job->id,
                'queue' => $job->queue,
                'job' => class_basename($payload->displayName ?? 'Unknown'),
                'failed_at' => $job->failed_at,
            ];
        });

        $this->table(['ID', 'Queue', 'Job', 'Failed At'], $rows);
    }
}
