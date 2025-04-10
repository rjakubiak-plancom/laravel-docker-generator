<?php

namespace DockerGenerator\Commands;

use Illuminate\Console\Command;

class GenerateDockerFilesCommand extends Command
{
    protected $signature = 'generate:docker {prefix} {--output-dir=docker/basic}';
    protected $description = 'Generate Docker related configuration files for your project based on a custom prefix.';

    public function handle()
    {
        $prefix = $this->argument('prefix');
        $outputDir = env('DOCKER_OUTPUT_DIR', 'docker/basic');

        $files = [
            'docker-compose.yml.stub' => base_path( 'docker-compose.yml'),
            'Dockerfile.stub' => base_path($outputDir . '/Dockerfile'),
            'Makefile.stub' => base_path('Makefile'),
            '.env.docker.dist.stub' => base_path('.env.docker.dist'),
            'nginx.conf.stub' => base_path($outputDir . '/nginx/conf.d/default.conf'),
            'php.ini.stub' => base_path($outputDir . '/php.ini'),
        ];

        foreach ($files as $stub => $targetPath) {
            $stubPath = __DIR__ . '/../../stubs/' . $stub;
            if (!file_exists($stubPath)) {
                $this->error("Stub file {$stub} not found.");
                continue;
            }

            $contents = file_get_contents($stubPath);
            $processed = str_replace('{{prefix}}', $prefix, $contents);

            $targetDir = dirname($targetPath);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            file_put_contents($targetPath, $processed);
            $this->info("Created file: {$targetPath}");
        }

        $this->info('Docker files generated successfully!');
    }
}