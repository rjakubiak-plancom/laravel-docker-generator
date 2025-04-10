# Docker files generator
Laravel package that provides an Artisan command to quickly generate all the essential Docker configuration files.  
Included php 8.3, mysql, nginx, redis and xdebug enabled. Good alternative for sail when you want to have more controller  

## Features

- **Single Artisan Command:** Run one command to generate your complete Docker configuration.
- **Dynamic Placeholder Replacement:** Replace the `{{prefix}}` placeholder in your stub files with a custom project prefix.
- **No Extra Configuration Needed:** Uses the built-in stubs contained in the package.
- **Seamless Laravel Integration:** Perfectly designed for Laravel applications.
- **Tested and Reliable:** Comprehensive tests ensure the command works as expected in your application.

## Installation

Add the package to your Laravel project using Composer:

```bash
composer require plan-com/php-docker-generator
```

## Usage

Generate files  
```
php artisan generate:docker {prefix}
```
Replace {prefix} with your desired project prefix. For example, if you want to prefix all container names with myproject, run:
```
php artisan generate:docker myproject
```
Remove Files(clean project)
```
php artisan remove:docker-generator
```

## Build docker environment after
Handful make commands included for project management.
```
make build # first command to run to actually build docker envoirenment 
make composer-install
make migrate
make migrate-test
make phpstan #requires phpstan installed
```

## What Happens When You Run the Command

### Stub Files Are Loaded:
The command loads the built-in stub files from the package's stubs/ directory. These files include:

```
docker-compose.yml.stub
Dockerfile.stub
Makefile.stub
.env.docker.dist.stub
```

### Dynamic Replacement:
In each stub, every occurrence of {{prefix}} is replaced with the value you provide (myproject in this example).

### File Generation:
The processed files are then written to your Laravel application's file system in the following locations:

`docker-compose.yml` – Located in the root directory, this file defines your Docker services with names incorporating the provided prefix.  
`docker/basic/Dockerfile` – Located in a subdirectory (i.e. docker/basic/), this file contains the Docker build instructions for your PHP container.  
`Makefile` – Placed at the project root, it includes commands (e.g., for running Docker and Artisan operations) that reference your prefixed service names.  
`.env.docker.dist` – Also in the project root, this file acts as a template for any environment variables needed by your Docker configuration.  

