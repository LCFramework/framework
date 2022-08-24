<?php

namespace LCFramework\Framework\Installer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use LCFramework\Framework\LCFrameworkServiceProvider;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\MountManager;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'lcframework:publish')]
class PublishComponent extends Command
{
    protected $signature = 'lcframework:publish {--module=} {--theme=}';

    protected $description = 'Publish LCFramework assets or assets that belong to a module or theme.';

    protected Filesystem $files;

    protected ?string $provider = null;

    protected array $tags = [];

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle(): void
    {
        $this->determineWhatShouldBePublished();

        foreach ($this->tags ?: [null] as $tag) {
            $this->publishTag($tag);
        }
    }

    protected function determineWhatShouldBePublished(): void
    {
        $provider = $this->option('provider');
        $tag = $this->option('tag');

        if ($provider === null && $tag === null) {
            $provider = LCFrameworkServiceProvider::class;
            $tag = 'assets';
        }

        [$this->provider, $this->tags] = [
            $provider, (array)$tag,
        ];
    }

    protected function publishTag($tag)
    {
        $pathsToPublish = $this->pathsToPublish($tag);

        if ($publishing = count($pathsToPublish) > 0) {
            $this->components->info(sprintf(
                'Publishing %sassets',
                $tag ? "[$tag] " : '',
            ));
        }

        foreach ($pathsToPublish as $from => $to) {
            $this->publishItem($from, $to);
        }

        if ($publishing === false) {
            $this->components->info('No publishable resources for tag [' . $tag . '].');
        } else {
            $this->laravel['events']->dispatch(new VendorTagPublished($tag, $pathsToPublish));

            $this->newLine();
        }
    }

    protected function pathsToPublish($tag): array
    {
        return ServiceProvider::pathsToPublish(
            $this->provider, $tag
        );
    }

    protected function publishItem($from, $to): void
    {
        if ($this->files->isFile($from)) {
            $this->publishFile($from, $to);
            return;
        } else if ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $to);
            return;
        }

        $this->components->error("Can't locate path: <{$from}>");
    }

    protected function publishFile($from, $to): void
    {
        if ((!$this->option('existing') && (!$this->files->exists($to) || $this->option('force')))
            || ($this->option('existing') && $this->files->exists($to))) {
            $this->createParentDirectory(dirname($to));

            $this->files->copy($from, $to);

            $this->status($from, $to, 'file');
        } else {
            if ($this->option('existing')) {
                $this->components->twoColumnDetail(sprintf(
                    'File [%s] does not exist',
                    str_replace(base_path() . '/', '', $to),
                ), '<fg=yellow;options=bold>SKIPPED</>');
            } else {
                $this->components->twoColumnDetail(sprintf(
                    'File [%s] already exists',
                    str_replace(base_path() . '/', '', realpath($to)),
                ), '<fg=yellow;options=bold>SKIPPED</>');
            }
        }
    }

    protected function publishDirectory($from, $to): void
    {
        $visibility = PortableVisibilityConverter::fromArray([], Visibility::PUBLIC);

        $this->moveManagedFiles(new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to' => new Flysystem(new LocalAdapter($to, $visibility)),
        ]));

        $this->status($from, $to, 'directory');
    }

    protected function moveManagedFiles($manager): void
    {
        foreach ($manager->listContents('from://', true) as $file) {
            $path = Str::after($file['path'], 'from://');

            if (
                $file['type'] === 'file'
                && (
                    (!$this->option('existing') && (!$manager->fileExists('to://' . $path) || $this->option('force')))
                    || ($this->option('existing') && $manager->fileExists('to://' . $path))
                )
            ) {
                $manager->write('to://' . $path, $manager->read($file['path']));
            }
        }
    }

    protected function createParentDirectory($directory): void
    {
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    protected function status($from, $to, $type): void
    {
        $from = str_replace(base_path() . '/', '', realpath($from));

        $to = str_replace(base_path() . '/', '', realpath($to));

        $this->components->task(sprintf(
            'Copying %s [%s] to [%s]',
            $type,
            $from,
            $to,
        ));
    }
}
