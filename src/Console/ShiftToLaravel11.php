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
            'composer require granule/starter-kit:dev-v6-dev --no-update --quiet',
            'composer require inertiajs/inertia-laravel:^1.3.0 laravel/framework:^11.22 laravel/reverb:^1.3 laravel/sanctum:^4.0 laravel/tinker:^2.9 --no-update --quiet',
            'composer require fakerphp/faker:^1.23 laravel/breeze:v2.1 laravel/pint:^1.13 laravel/sail:^1.26 mockery/mockery:^1.6 nunomaduro/collision:^8.1 --dev --no-update --quiet',
            'composer update -W --no-scripts --no-interaction'
        ]);
    }

    /**
     * Refactoring console to Laravel 11.x standards
     *
     * @return void
     */
    private function refactoringConsole()
    {
        // get content of file console/Kernel.php
        $content = file_get_contents(base_path('app/Console/Kernel.php'));
        // get any all content inside the schedule method, just take th content inside { } that not commented //
        preg_match('/protected function schedule\(Schedule \$schedule\): void\n    {\n        (.*)\n    }\n\n    /s', $content, $matches);
        // TODO: $matches[1] contains the content of the schedule method
        // copy the content of the schedule method to the schedule method in the stubs/bootstrap/app.php
        copy(__DIR__.'/../../stubs/bootstrap/app.php', base_path('bootstrap/app-new.php'));
        // replace {{ schedule }} with the content of the schedule method
        $this->replaceContent(base_path('bootstrap/app.php'), [
            '{{ schedule }}' => $matches[1],
        ]);
    }
}
