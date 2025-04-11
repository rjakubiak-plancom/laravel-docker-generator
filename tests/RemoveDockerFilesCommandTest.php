<?php

namespace DockerGenerator\Tests;

use DockerGenerator\Providers\DockerServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase;

class RemoveDockerFilesCommandTest extends TestCase
{
    protected ?Filesystem $files;
    protected string $dir;
    protected string $prefix = 'something';

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem;

        $this->dir = base_path(env('DOCKER_OUTPUT_DIR', 'docker/basic'));

        if (!$this->files->exists($this->dir)) {
            $this->files->makeDirectory($this->dir, 0755, true);
        }

        $this->files->put($this->dir . DIRECTORY_SEPARATOR . 'dummy.txt', 'dummy content');

        $envFile = base_path('.env.docker.dist');
        $this->files->put($envFile, 'ENV_VAR=example');

        $makefile = base_path('Makefile');
        $this->files->put($makefile, 'Dummy Makefile content');

        $dockerCompose = base_path($this->getDockerComposeFileName());
        $this->files->put($dockerCompose, 'Dummy Docker compose content');
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
        $this->assertTrue($this->files->exists(base_path($this->getDockerComposeFileName())));

        $this->artisan('remove:docker-generator', ['prefix' => $this->prefix])
            ->expectsOutput('Cleanup completed.')
            ->assertExitCode(0);

        $this->assertFalse($this->files->exists($this->dir));
        $this->assertFalse($this->files->exists(base_path('.env.docker.dist')));
        $this->assertFalse($this->files->exists(base_path('Makefile')));
        $this->assertFalse($this->files->exists(base_path($this->getDockerComposeFileName())));
    }

    private function getDockerComposeFileName(): string
    {
        return sprintf('docker-compose.%s.yml', $this->prefix);
    }
}