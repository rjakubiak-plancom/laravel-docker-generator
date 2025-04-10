<?php

namespace DockerGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class RemoveDockerFilesCommand extends Command
{
    protected $signature = 'remove:docker-generator';
    protected $description = 'Remove generated Docker configuration files and directories';

    public function handle()
    {
        $files = [
            base_path(env('DOCKER_OUTPUT_DIR', 'docker/basic')),
            base_path('.env.docker.dist'),
            base_path('Makefile'),
        ];

        $fs = new Filesystem;

        foreach ($files as $file) {
            if ($fs->exists($file)) {
                if ($fs->isDirectory($file)) {
                    $fs->deleteDirectory($file);
                    $this->info("Removed directory: {$file}");
                } else {
                    $fs->delete($file);
                    $this->info("Removed file: {$file}");
                }
            } else {
                $this->info("Not found: {$file}");
            }
        }

        $this->info('Cleanup completed.');
    }
}