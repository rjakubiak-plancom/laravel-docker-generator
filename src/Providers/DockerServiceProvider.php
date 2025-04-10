<?php

namespace DockerGenerator\Providers;

use DockerGenerator\Commands\RemoveDockerFilesCommand;
use Illuminate\Support\ServiceProvider;
use DockerGenerator\Commands\GenerateDockerFilesCommand;

class DockerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateDockerFilesCommand::class,
                RemoveDockerFilesCommand::class,
            ]);
        }
    }
}