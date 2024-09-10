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

        // upgrade composer.json
        $this->runCommands([
            'composer require inertiajs/inertia-laravel "^1.3.0" -W',
            'composer require laravel/framework "^11.9" -W',
            'composer require laravel/reverb "^1.3" -W',
            'composer require laravel/sanctum "^4.0" -W',
            'composer require laravel/tinker "^2.9" -W',

            'composer require fakerphp/faker "^1.23" --dev -W',
            'composer require laravel/breeze "v2.1" --dev -W',
            'composer require laravel/pint "^1.13" --dev -W',
            'composer require laravel/sail "^1.26" --dev -W',
            'composer require mockery/mockery "^1.6" --dev -W',
            'composer require nunomaduro/collision "^8.1" --dev -W',
        ]);

    }
}
