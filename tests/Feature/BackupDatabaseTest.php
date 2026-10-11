<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->dir = sys_get_temp_dir().'/wallaby-backup-test-'.uniqid();
    File::makeDirectory($this->dir.'/database', 0755, true);
    File::put($this->dir.'/database/database.sqlite', 'db');

    $this->app->useDatabasePath($this->dir.'/database');
    $this->app->useStoragePath($this->dir.'/storage');

    $this->backups = $this->dir.'/storage/backups';
    File::makeDirectory($this->backups, 0755, true);
});

afterEach(fn () => File::deleteDirectory($this->dir));

function oldBackup(string $dir, int $daysAgo): string
{
    $path = $dir.'/database_'.now()->subDays($daysAgo)->format('Y-m-d_H-i-s').'.sqlite';
    File::put($path, 'old');
    touch($path, now()->subDays($daysAgo)->getTimestamp());

    return $path;
}

it('keeps backups for three months by default', function () {
    $recent = oldBackup($this->backups, 89);
    $expired = oldBackup($this->backups, 91);

    $this->artisan('app:backup-database')->assertSuccessful();

    expect(File::exists($recent))->toBeTrue()
        ->and(File::exists($expired))->toBeFalse()
        ->and(File::glob($this->backups.'/database_*.sqlite'))->toHaveCount(2);
});

it('can keep backups for a different number of days', function () {
    $recent = oldBackup($this->backups, 5);
    $expired = oldBackup($this->backups, 8);

    $this->artisan('app:backup-database', ['--days' => 7])->assertSuccessful();

    expect(File::exists($recent))->toBeTrue()
        ->and(File::exists($expired))->toBeFalse();
});

it('still honours a maximum number of backups when given', function () {
    foreach (range(1, 5) as $day) {
        oldBackup($this->backups, $day);
    }

    $this->artisan('app:backup-database', ['--keep' => 3])->assertSuccessful();

    expect(File::glob($this->backups.'/database_*.sqlite'))->toHaveCount(3);
});
