<?php

namespace Granule\LaravelShifter\Console;

use Illuminate\Console\Command;

trait ShiftToLaravel11
{
    /**
     * Shift to Laravel 11.x
     *
     * @return void
     */
    public function shiftToLaravel11()
    {
        $this->info('Upgrading to Laravel 11.x');

        // change php 8.x to 8.2
        $this->replaceContent(base_path('composer.json'), [
            '"php": "^8.0"' => '"php": "^8.2"',
            '"php": "^8.1"' => '"php": "^8.2"',
        ]);

        // change package versions
        $this->runCommands([
            'composer require inertiajs/inertia-laravel:^1.3.0 laravel/framework:^11.22 laravel/reverb:^1.3 laravel/sanctum:^4.0 laravel/tinker:^2.9 --no-update --quiet',
            'composer require fakerphp/faker:^1.23 laravel/breeze:v2.1 laravel/pint:^1.13 laravel/sail:^1.26 mockery/mockery:^1.6 nunomaduro/collision:^8.1 --dev --no-update --quiet',
            'composer update -W --no-scripts --no-interaction'
        ]);
    }
}
