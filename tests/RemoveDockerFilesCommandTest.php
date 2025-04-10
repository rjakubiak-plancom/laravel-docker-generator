<?php

namespace DockerGenerator\Tests;

use DockerGenerator\Providers\DockerServiceProvider;
use Orchestra\Testbench\TestCase;
use Illuminate\Filesystem\Filesystem;

class RemoveDockerFilesCommandTest extends TestCase
{
    protected $files;
    protected $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem;

        $this->dir = base_path(env('DOCKER_OUTPUT_DIR', 'docker/basic'));

        if (! $this->files->exists($this->dir)) {
            $this->files->makeDirectory($this->dir, 0755, true);
        }

        $this->files->put($this->dir . DIRECTORY_SEPARATOR . 'dummy.txt', 'dummy content');

        $envFile = base_path('.env.docker.dist');
        $this->files->put($envFile, 'ENV_VAR=example');

        $makefile = base_path('Makefile');
        $this->files->put($makefile, 'Dummy Makefile content');
    }

    protected function getPackageProviders($app)
    {
        return [
            DockerServiceProvider::class,
        ];
    }

    public function testRemoveDockerFilesCommand()
    {
        $this->assertTrue($this->files->exists($this->dir));
        $this->assertTrue($this->files->exists(base_path('.env.docker.dist')));
        $this->assertTrue($this->files->exists(base_path('Makefile')));

        $this->artisan('remove:docker-generator')
            ->expectsOutput('Cleanup completed.')
            ->assertExitCode(0);

        $this->assertFalse($this->files->exists($this->dir));
        $this->assertFalse($this->files->exists(base_path('.env.docker.dist')));
        $this->assertFalse($this->files->exists(base_path('Makefile')));
    }
}