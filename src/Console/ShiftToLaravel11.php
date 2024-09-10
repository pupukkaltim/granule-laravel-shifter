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

        // from
        // "require": {
        //     "php": "^8.1",
        //     "doctrine/dbal": "*",
        //     "granule/starter-kit": "^5.1",
        //     "guzzlehttp/guzzle": "^7.2",
        //     "inertiajs/inertia-laravel": "^0.6.8",
        //     "laravel/framework": "^10.10",
        //     "laravel/reverb": "@beta",
        //     "laravel/sanctum": "^3.2",
        //     "laravel/tinker": "^2.8",
        //     "pusher/pusher-php-server": "^7.2",
        //     "spatie/laravel-permission": "^6.1",
        //     "staudenmeir/laravel-migration-views": "^1.7",
        //     "tightenco/ziggy": "^2.0"
        // },
        // "require-dev": {
        //     "fakerphp/faker": "^1.9.1",
        //     "laravel/breeze": "v1.29",
        //     "laravel/pint": "^1.0",
        //     "laravel/sail": "^1.18",
        //     "mockery/mockery": "^1.4.4",
        //     "nunomaduro/collision": "^7.0",
        //     "pestphp/pest": "^2.0",
        //     "pestphp/pest-plugin-laravel": "^2.0",
        //     "spatie/laravel-ignition": "^2.0"
        // },

        // to
        // "require": {
        //     "php": "^8.2",
        //     "doctrine/dbal": "*",
        //     "granule/starter-kit": "dev-v6-dev",
        //     "guzzlehttp/guzzle": "^7.2",
        //     "inertiajs/inertia-laravel": "^1.3.0",
        //     "laravel/framework": "^11.9",
        //     "laravel/reverb": "^1.3",
        //     "laravel/sanctum": "^4.0",
        //     "laravel/tinker": "^2.9",
        //     "pusher/pusher-php-server": "^7.2",
        //     "spatie/laravel-permission": "^6.1",
        //     "staudenmeir/laravel-migration-views": "^1.7",
        //     "tightenco/ziggy": "^2.0"
        // },
        // "require-dev": {
        //     "fakerphp/faker": "^1.23",
        //     "laravel/breeze": "v2.1",
        //     "laravel/pint": "^1.13",
        //     "laravel/sail": "^1.26",
        //     "mockery/mockery": "^1.6",
        //     "nunomaduro/collision": "^8.1",
        //     "pestphp/pest": "^2.0",
        //     "pestphp/pest-plugin-laravel": "^2.0",
        //     "spatie/laravel-ignition": "^2.0"
        // },

        // $this->replaceContent(base_path('composer.json'), [
        //     '"granule/starter-kit": "^5.1"' => '"granule/starter-kit": "dev-v6-dev"',
        //     '"inertiajs/inertia-laravel": "^0.6.8"' => '"inertiajs/inertia-laravel": "^1.3.0"',
        //     '"laravel/framework": "^10.10"' => '"laravel/framework": "^11.9"',
        //     '"laravel/reverb": "@beta"' => '"laravel/reverb": "^1.3"',
        //     '"laravel/sanctum": "^3.2"' => '"laravel/sanctum": "^4.0"',
        //     '"laravel/tinker": "^2.8"' => '"laravel/tinker": "^2.9"',

        //     '"fakerphp/faker": "^1.9.1"' => '"fakerphp/faker": "^1.23"',
        //     '"laravel/breeze": "v1.29"' => '"laravel/breeze": "v2.1"',
        //     '"laravel/pint": "^1.0"' => '"laravel/pint": "^1.13"',
        //     '"laravel/sail": "^1.18"' => '"laravel/sail": "^1.26"',
        //     '"mockery/mockery": "^1.4.4"' => '"mockery/mockery": "^1.6"',
        //     '"nunomaduro/collision": "^7.0"' => '"nunomaduro/collision": "^8.1"',
        //     '"pestphp/pest": "^2.0"' => '"pestphp/pest": "^2.0"',
        // ]);

        // upgrade composer.json
        $this->runCommands([
            'composer require granule/starter-kit:dev-v6-dev --no-install',
            'composer require inertiajs/inertia-laravel:^1.3.0 --no-install',
            'composer require laravel/framework:^11.9 --no-install',
            'composer require laravel/reverb:^1.3 --no-install',
            'composer require laravel/sanctum:^4.0 --no-install',
            'composer require laravel/tinker:^2.9 --no-install',

            'composer require fakerphp/faker:^1.23 --dev --no-install',
            'composer require laravel/breeze:v2.1 --dev --no-install',
            'composer require laravel/pint:^1.13 --dev --no-install',
            'composer require laravel/sail:^1.26 --dev --no-install',
            'composer require mockery/mockery:^1.6 --dev --no-install',
            'composer require nunomaduro/collision:^8.1 --dev --no-install',
        ]);

        // upgrade composer.json
        $this->runCommands([
            'composer update -W',
        ]);
    }
}
