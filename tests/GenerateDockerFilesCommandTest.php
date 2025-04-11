<?php

namespace DockerGenerator\Tests;

use DockerGenerator\Commands\GenerateDockerFilesCommand;
use Orchestra\Testbench\TestCase;
use DockerGenerator\Providers\DockerServiceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(GenerateDockerFilesCommand::class)]
class GenerateDockerFilesCommandTest extends TestCase
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Specify the service providers needed for tests.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            DockerServiceProvider::class,
        ];
    }

    #[Test]
    public function the_generate_docker_command_is_registered()
    {
        $kernel = $this->app->make('Illuminate\Contracts\Console\Kernel');
        $commands = $kernel->all();

        $this->assertArrayHasKey('generate:docker', $commands);
    }

    #[Test]
    public function it_generates_docker_files_with_correct_replacement()
    {
        $dir = env('DOCKER_OUTPUT_DIR', 'docker/basic');
        $prefix = 'myproject_php';

        $expectedReplacements = [
            base_path(sprintf('docker-compose.%s.yml', $prefix)) => $prefix,
            base_path(sprintf('%s/Dockerfile', $dir)) => 'FROM php:8.3',
            base_path('Makefile') => 'DOCKER_EXEC',
            base_path('.env.docker.dist') => 'DOCKER_NGINX_PORT=8091',
            base_path(sprintf('%s/php.ini', $dir)) => 'display_errors = On',
            base_path(sprintf('%s/nginx/conf.d/default.conf', $dir)) => 'listen 80;',
        ];

        $this->artisan('generate:docker', ['prefix' => $prefix])
            ->expectsOutput('Docker files generated successfully!')
            ->assertExitCode(0);

        foreach ($expectedReplacements as $filePath => $expectedContent) {
            $this->assertFileExists($filePath);

            $content = file_get_contents($filePath);
            $this->assertStringNotContainsString('{{prefix}}', $content);
            $this->assertStringContainsString($expectedContent, $content);

            @unlink($filePath);
        }
    }
}