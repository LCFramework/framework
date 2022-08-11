<?php

namespace LCFramework\Framework\Setting\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'settings:table')]
class SettingsTableCommand extends Command
{
    protected $name = 'settings:table';

    protected static $defaultName = 'settings:table';

    protected $description = 'Create a migration for the settings database table.';

    protected Filesystem $files;

    protected Composer $composer;

    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    public function handle(): void
    {
        $fullPath = $this->createBaseMigration();

        $this->files->put(
            $fullPath,
            $this->files->get(
                __DIR__ . '/../stubs/database/create_settings_table.php'
            )
        );

        $this->components->info('Migration created successfully.');

        $this->composer->dumpAutoloads();
    }

    protected function createBaseMigration(): string
    {
        $name = 'create_settings_table';

        $path = $this->laravel->databasePath('migrations');

        return $this->laravel
            ->make('migration.creator')
            ->create($name, $path);
    }
}
